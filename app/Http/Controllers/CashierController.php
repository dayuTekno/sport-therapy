<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\TherapySession;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'unpaid'); // 'unpaid' or 'paid'

        $query = Reservation::with(['patient', 'therapist', 'sessions.therapyType', 'cashier']);

        if ($tab === 'paid') {
            $query->where('payment_status', 'paid');
        } else {
            // Unpaid: reservasi yang belum dibayar, tidak batal, dan memiliki sesi terapi atau tagihan
            $query->where('payment_status', 'unpaid')
                  ->where('status', '!=', 'cancelled')
                  ->where(function($q) {
                      $q->whereHas('sessions')
                        ->orWhere('total_price', '>', 0);
                  });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_code', 'ilike', "%{$search}%")
                  ->orWhere('reservation_code', 'ilike', "%{$search}%")
                  ->orWhere('payment_method', 'ilike', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('full_name', 'ilike', "%{$search}%")
                         ->orWhere('phone_number', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            if ($tab === 'paid') {
                $query->whereDate('paid_at', $request->date);
            } else {
                $query->whereDate('created_at', $request->date);
            }
        }

        if ($request->filled('payment_method')) {
            $method = $request->payment_method;
            if ($method === 'Lainnya') {
                $query->where(function($q) {
                    $q->where('payment_method', 'ilike', 'Lainnya%')
                      ->orWhereNotIn('payment_method', ['Cash', 'QRIS', 'Transfer Bank', 'Debit', 'cash', 'qris', 'Kasir Offline']);
                });
            } else {
                $query->where('payment_method', 'ilike', "%{$method}%");
            }
        }

        $reservations = $query->orderBy($tab === 'paid' ? 'paid_at' : 'created_at', 'desc')->paginate(10);

        // Counter Metrics
        $today = now()->toDateString();
        // (jumlah & total dihitung dalam satu query agregat per kelompok)
        $unpaidStats = Reservation::where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->where(function($q) {
                $q->whereHas('sessions')->orWhere('total_price', '>', 0);
            })
            ->selectRaw('COUNT(*) as unpaid_count, COALESCE(SUM(total_price + COALESCE(other_fee, 0)), 0) as unpaid_total')
            ->first();
        $unpaidCount = (int) $unpaidStats->unpaid_count;
        $unpaidTotal = (float) $unpaidStats->unpaid_total;

        $todayPaidStats = Reservation::whereDate('paid_at', $today)->where('payment_status', 'paid')
            ->selectRaw('COUNT(*) as paid_count, COALESCE(SUM(paid_amount), 0) as paid_total')
            ->first();
        $todayPaidCount = (int) $todayPaidStats->paid_count;
        $todayPaidTotal = $todayPaidStats->paid_total;

        // Rekapitulasi Pembayaran per Metode (QRIS, Transfer Bank, Cash, Debit, dll)
        $paymentMethodsRecap = Reservation::where('payment_status', 'paid')
            ->when($request->filled('date'), function($q) use ($request) {
                $q->whereDate('paid_at', $request->date);
            }, function($q) use ($today) {
                $q->whereDate('paid_at', $today);
            })
            ->selectRaw("COALESCE(NULLIF(payment_method, ''), 'Cash') as method, count(*) as count, sum(paid_amount) as total")
            ->groupByRaw("COALESCE(NULLIF(payment_method, ''), 'Cash')")
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.pages.cashier.index', compact(
            'reservations',
            'tab',
            'unpaidCount',
            'unpaidTotal',
            'todayPaidCount',
            'todayPaidTotal',
            'paymentMethodsRecap'
        ));
    }

    public function process($id)
    {
        $reservation = Reservation::with(['patient', 'therapist', 'sessions.therapyType', 'cashier'])->findOrFail($id);

        // Pastikan total harga reservasi terhitung akurat dari sesi-sesinya
        if ($reservation->total_price <= 0 && $reservation->sessions->isNotEmpty()) {
            $reservation->calculateTotalPrice();
        }

        $total = $reservation->total_price > 0 ? (float)$reservation->total_price : $reservation->calculateTotalPrice();

        return view('admin.pages.cashier.process', compact('reservation', 'total'));
    }

    public function store(Request $request, $id)
    {
        $reservation = Reservation::with(['patient', 'sessions.therapyType'])->findOrFail($id);

        $request->validate([
            'other_fee' => 'nullable|numeric|min:0',
            'other_fee_description' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:rupiah,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'payment_method_detail' => 'nullable|string|max:100',
            'cashier_notes' => 'nullable|string',
        ]);

        $basePrice = $reservation->calculateTotalPrice();
        if ($basePrice <= 0 && $reservation->total_price > 0) {
            $basePrice = (float) $reservation->total_price;
        }

        $otherFee = max(0, floatval($request->input('other_fee', 0)));
        $otherFeeDescription = $request->filled('other_fee_description') ? trim($request->input('other_fee_description')) : null;

        $discountType = $request->input('discount_type', 'rupiah');
        $discountValue = floatval($request->input('discount_value', 0));
        if ($discountType === 'percent' && $discountValue > 0) {
            $discount = round(($basePrice * min(100, max(0, $discountValue))) / 100, 2);
        } else {
            $discount = min($basePrice + $otherFee, max(0, floatval($request->input('discount', $discountValue))));
        }

        $finalTotal = max(0, ($basePrice + $otherFee) - $discount);
        $paidAmount = floatval($request->paid_amount);

        $invoiceCode = $reservation->invoice_code ?: Reservation::generateInvoiceCode();

        $paymentMethod = $request->payment_method ?: 'Cash';
        if ($paymentMethod === 'Lainnya' && $request->filled('payment_method_detail')) {
            $paymentMethod = 'Lainnya: ' . trim($request->payment_method_detail);
        }

        $reservation->update([
            'payment_status' => 'paid',
            'total_price' => $basePrice,
            'other_fee' => $otherFee,
            'other_fee_description' => $otherFeeDescription,
            'discount' => $discount,
            'paid_amount' => $paidAmount,
            'payment_method' => $paymentMethod,
            'invoice_code' => $invoiceCode,
            'paid_at' => now(),
            'cashier_user_id' => auth()->id(),
            'cashier_notes' => $request->cashier_notes,
        ]);

        // Tandai seluruh sesi terapi di reservasi ini sebagai lunas
        $reservation->sessions()->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'invoice_code' => $invoiceCode,
            'paid_at' => now(),
            'cashier_user_id' => auth()->id(),
        ]);

        return redirect()->route('cashier.receipt', $reservation->id)
            ->with('success', "Pembayaran ({$paymentMethod}) untuk reservasi {$reservation->reservation_code} ({$reservation->patient?->full_name}) berhasil diselesaikan (Invoice: {$invoiceCode})!");
    }

    public function receipt($id)
    {
        $reservation = Reservation::with(['patient', 'therapist', 'sessions.therapyType', 'cashier'])->findOrFail($id);

        return view('admin.pages.cashier.receipt', compact('reservation'));
    }
}
