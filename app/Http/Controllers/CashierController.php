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

        $reservations = $query->orderBy($tab === 'paid' ? 'paid_at' : 'created_at', 'desc')->paginate(10);

        // Counter Metrics
        $today = now()->toDateString();
        $unpaidCount = Reservation::where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->where(function($q) {
                $q->whereHas('sessions')->orWhere('total_price', '>', 0);
            })
            ->count();
        $unpaidTotal = Reservation::where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->where(function($q) {
                $q->whereHas('sessions')->orWhere('total_price', '>', 0);
            })
            ->sum('total_price');

        $todayPaidCount = Reservation::whereDate('paid_at', $today)->where('payment_status', 'paid')->count();
        $todayPaidTotal = Reservation::whereDate('paid_at', $today)->where('payment_status', 'paid')->sum('paid_amount');

        return view('admin.pages.cashier.index', compact(
            'reservations',
            'tab',
            'unpaidCount',
            'unpaidTotal',
            'todayPaidCount',
            'todayPaidTotal'
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
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'cashier_notes' => 'nullable|string',
        ]);

        $basePrice = $reservation->calculateTotalPrice();
        if ($basePrice <= 0 && $reservation->total_price > 0) {
            $basePrice = (float) $reservation->total_price;
        }

        $discount = floatval($request->discount ?? 0);
        $finalTotal = max(0, $basePrice - $discount);
        $paidAmount = floatval($request->paid_amount);

        $invoiceCode = $reservation->invoice_code ?: Reservation::generateInvoiceCode();

        $reservation->update([
            'payment_status' => 'paid',
            'total_price' => $basePrice,
            'discount' => $discount,
            'paid_amount' => $paidAmount,
            'payment_method' => $request->payment_method ?: 'Kasir Offline',
            'invoice_code' => $invoiceCode,
            'paid_at' => now(),
            'cashier_user_id' => auth()->id(),
            'cashier_notes' => $request->cashier_notes,
        ]);

        // Tandai seluruh sesi terapi di reservasi ini sebagai lunas
        $reservation->sessions()->update([
            'payment_status' => 'paid',
            'invoice_code' => $invoiceCode,
            'paid_at' => now(),
            'cashier_user_id' => auth()->id(),
        ]);

        return redirect()->route('cashier.receipt', $reservation->id)
            ->with('success', "Pembayaran untuk reservasi {$reservation->reservation_code} ({$reservation->patient?->full_name}) berhasil diselesaikan (Invoice: {$invoiceCode})!");
    }

    public function receipt($id)
    {
        $reservation = Reservation::with(['patient', 'therapist', 'sessions.therapyType', 'cashier'])->findOrFail($id);

        return view('admin.pages.cashier.receipt', compact('reservation'));
    }
}
