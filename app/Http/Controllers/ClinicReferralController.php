<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicReferralCommission;
use App\Models\SubscriptionPlan;

class ClinicReferralController extends Controller
{
    /**
     * Kode referal klinik, link undangan, dan daftar klinik yang direferensikan
     */
    public function index()
    {
        $clinic = $this->resolveActiveClinic();

        if (!$clinic) {
            return redirect()->route('dashboard')->with('error', 'Data klinik tidak ditemukan.');
        }

        // Klinik lama yang belum punya kode (mis. dibuat sebelum fitur ini) langsung dibuatkan
        if (empty($clinic->referral_code)) {
            $clinic->update(['referral_code' => Clinic::generateReferralCode($clinic->name)]);
        }

        $referredClinics = $clinic->referredClinics()
            ->with(['plan', 'referralCommission'])
            ->orderBy('created_at', 'desc')
            ->get();

        $commissions = $clinic->referralCommissions()->where('status', '!=', 'cancelled')->get();
        $totalPending = $commissions->where('status', 'pending')->sum('commission_amount');
        $totalPaid = $commissions->where('status', 'paid')->sum('commission_amount');

        $settings = ClinicReferralCommission::settings();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $shareLink = route('register.clinic', ['ref' => $clinic->referral_code]);

        return view('admin.pages.clinic-referral.index', compact(
            'clinic',
            'referredClinics',
            'totalPending',
            'totalPaid',
            'settings',
            'plans',
            'shareLink'
        ));
    }

    protected function resolveActiveClinic(): ?Clinic
    {
        if (session()->has('active_clinic_id')) {
            return Clinic::find(session('active_clinic_id'));
        }

        return auth()->user()?->clinic;
    }
}
