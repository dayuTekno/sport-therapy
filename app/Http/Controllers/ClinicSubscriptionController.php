<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClinicSubscriptionController extends Controller
{
    /**
     * Halaman langganan klinik: status paket, pilihan paket, form request pembelian & riwayat
     */
    public function index()
    {
        $clinic = $this->resolveActiveClinic();

        if (!$clinic) {
            return redirect()->route('dashboard')->with('error', 'Data klinik tidak ditemukan.');
        }

        $clinic->loadMissing('plan');
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $requests = ClinicSubscription::with(['plan'])
            ->where('clinic_id', $clinic->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $pendingRequest = $requests->firstWhere('payment_status', 'pending');
        $paymentInstructions = ClinicSubscription::paymentInstructions();

        return view('admin.pages.clinic-subscription.index', compact(
            'clinic',
            'plans',
            'requests',
            'pendingRequest',
            'paymentInstructions'
        ));
    }

    public function store(Request $request)
    {
        $clinic = $this->resolveActiveClinic();
        abort_unless($clinic, 404);

        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|string|in:Transfer Bank,QRIS,Lainnya',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (ClinicSubscription::where('clinic_id', $clinic->id)->where('payment_status', 'pending')->exists()) {
            return redirect()->route('clinic.subscription')->with('error', 'Masih ada request pembelian paket yang menunggu verifikasi admin.');
        }

        $plan = SubscriptionPlan::where('is_active', true)->findOrFail($request->subscription_plan_id);

        ClinicSubscription::create([
            'clinic_id' => $clinic->id,
            'subscription_plan_id' => $plan->id,
            'requested_by_user_id' => auth()->id(),
            'invoice_code' => ClinicSubscription::generateInvoiceCode(),
            'amount' => $plan->price,
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_proof_path' => $request->file('payment_proof')?->store('subscription-proofs', 'local'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('clinic.subscription')->with('success', "Request pembelian {$plan->name} berhasil dikirim. Paket akan aktif setelah diverifikasi admin.");
    }

    /**
     * Upload / ganti bukti pembayaran untuk request yang masih menunggu verifikasi
     */
    public function uploadProof(Request $request, $id)
    {
        $subscription = $this->ownedSubscription($id);
        abort_unless($subscription->payment_status === 'pending', 422);

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ]);

        if ($subscription->payment_proof_path) {
            Storage::disk('local')->delete($subscription->payment_proof_path);
        }

        $subscription->update([
            'payment_proof_path' => $request->file('payment_proof')->store('subscription-proofs', 'local'),
        ]);

        return redirect()->route('clinic.subscription')->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function cancel($id)
    {
        $subscription = $this->ownedSubscription($id);

        if ($subscription->payment_status !== 'pending') {
            return redirect()->route('clinic.subscription')->with('error', 'Hanya request yang menunggu verifikasi yang dapat dibatalkan.');
        }

        $subscription->update(['payment_status' => 'cancelled']);

        return redirect()->route('clinic.subscription')->with('success', 'Request pembelian paket dibatalkan.');
    }

    /**
     * Tampilkan bukti pembayaran (disimpan di disk private) untuk klinik pemilik atau SaaS admin
     */
    public function proof($id)
    {
        $subscription = ClinicSubscription::findOrFail($id);
        $user = auth()->user();
        $clinic = $this->resolveActiveClinic();

        abort_unless($user->isSaasAdmin() || ($clinic && $subscription->clinic_id === $clinic->id), 403);
        abort_unless($subscription->payment_proof_path && Storage::disk('local')->exists($subscription->payment_proof_path), 404);

        return Storage::disk('local')->response($subscription->payment_proof_path);
    }

    protected function ownedSubscription($id): ClinicSubscription
    {
        $clinic = $this->resolveActiveClinic();
        abort_unless($clinic, 404);

        return ClinicSubscription::where('clinic_id', $clinic->id)->findOrFail($id);
    }

    protected function resolveActiveClinic(): ?Clinic
    {
        if (session()->has('active_clinic_id')) {
            return Clinic::find(session('active_clinic_id'));
        }

        return auth()->user()?->clinic;
    }
}
