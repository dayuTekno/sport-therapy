<?php

namespace App\Http\Controllers;

use App\Models\ClinicSubscription;
use Illuminate\Http\Request;

class SaasSubscriptionRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->isSaasAdmin(), 403);
            return $next($request);
        });
    }

    /**
     * Monitoring request pembelian / perpanjangan paket dari klinik (approval manual)
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = ClinicSubscription::with(['clinic', 'plan', 'requester', 'processor']);

        if ($status !== 'all') {
            $query->whereIn('payment_status', $status === 'rejected' ? ['rejected', 'failed'] : [$status]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_code', 'ilike', "%{$s}%")
                  ->orWhereHas('clinic', fn ($c) => $c->where('name', 'ilike', "%{$s}%")->orWhere('clinic_code', 'ilike', "%{$s}%"));
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ringkasan dalam satu query agregat
        $summary = ClinicSubscription::selectRaw("
                COUNT(*) FILTER (WHERE payment_status = 'pending') as pending_count,
                COALESCE(SUM(amount) FILTER (WHERE payment_status = 'pending'), 0) as pending_amount,
                COUNT(*) FILTER (WHERE payment_status = 'paid' AND paid_at >= ?) as paid_month_count,
                COALESCE(SUM(amount) FILTER (WHERE payment_status = 'paid' AND paid_at >= ?), 0) as paid_month_amount
            ", [now()->startOfMonth(), now()->startOfMonth()])
            ->first();

        $paymentInstructions = ClinicSubscription::paymentInstructions();

        return view('admin.saas.subscription-requests.index', compact('subscriptions', 'summary', 'status', 'paymentInstructions'));
    }

    public function approve(Request $request, $id)
    {
        $subscription = ClinicSubscription::with(['clinic', 'plan'])->findOrFail($id);

        if ($subscription->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $subscription->approve(auth()->user(), $request->admin_notes);
        $subscription->refresh();

        return redirect()->back()->with('success', "Paket {$subscription->plan?->name} untuk {$subscription->clinic?->name} telah diaktifkan hingga {$subscription->expires_at?->format('d M Y')}.");
    }

    public function reject(Request $request, $id)
    {
        $subscription = ClinicSubscription::with('clinic')->findOrFail($id);

        if ($subscription->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $request->validate(['admin_notes' => 'required|string|max:1000'], [
            'admin_notes.required' => 'Alasan penolakan wajib diisi agar klinik mengetahui penyebabnya.',
        ]);

        $subscription->reject(auth()->user(), $request->admin_notes);

        return redirect()->back()->with('success', "Request {$subscription->invoice_code} dari {$subscription->clinic?->name} ditolak.");
    }

    public function updateInstructions(Request $request)
    {
        $request->validate(['instructions' => 'nullable|string|max:2000']);

        ClinicSubscription::savePaymentInstructions((string) $request->instructions);

        return redirect()->route('saas.subscription-requests.index')->with('success', 'Instruksi pembayaran berhasil disimpan.');
    }
}
