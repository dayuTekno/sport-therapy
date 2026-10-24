<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaasClinicController extends Controller
{
    public function index(Request $request)
    {
        $query = Clinic::with(['plan', 'users', 'referrer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'ilike', "%{$s}%")
                  ->orWhere('clinic_code', 'ilike', "%{$s}%")
                  ->orWhere('referral_code', 'ilike', "%{$s}%")
                  ->orWhere('email', 'ilike', "%{$s}%")
                  ->orWhere('phone_number', 'ilike', "%{$s}%");
            });
        }

        $clinics = $query->orderBy('created_at', 'desc')->paginate(10);
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('admin.saas.clinics.index', compact('clinics', 'plans'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('admin.saas.clinics.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $referrer = null;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clinics,email',
            'phone_number' => 'required|string',
            'address' => 'nullable|string',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'subscription_ends_at' => 'nullable|date',
            'status' => 'required|in:active,trial,suspended',
            'admin_name' => 'required|string|max:255',
            'admin_password' => 'required|string|min:8',
            'referral_code' => $this->referralCodeRules($referrer),
        ]);

        $slug = Str::slug($request->name);
        $count = Clinic::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) $slug .= '-' . ($count + 1);

        $plan = SubscriptionPlan::find($request->subscription_plan_id);

        $clinic = Clinic::create([
            'clinic_code' => Clinic::generateClinicCode(),
            'name' => $request->name,
            'slug' => $slug,
            'subdomain' => Str::slug($request->name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => $request->status,
            'trial_ends_at' => $request->status === 'trial' ? now()->addDays($plan->trial_days ?: \App\Models\LandingPageSetting::getTrialDays()) : null,
            'subscription_ends_at' => $request->subscription_ends_at,
            'subscription_plan_id' => $plan->id,
            'max_therapists' => $plan->max_therapists,
            'max_patients' => $plan->max_patients,
            'is_active' => true,
            'referred_by_clinic_id' => $referrer?->id,
        ]);

        // Komisi referal TIDAK dicatat saat pendaftaran, hanya saat klinik mulai berlangganan paket (lihat update())

        // Buat User Admin Klinik
        $adminUser = User::create([
            'name' => $request->admin_name,
            'email' => $request->email,
            'password' => bcrypt($request->admin_password),
            'clinic_id' => $clinic->id,
        ]);
        $adminUser->assignRole('clinic_admin');

        return redirect()->route('saas.clinics.index')->with('success', "Klinik {$clinic->name} berhasil didaftarkan!");
    }

    public function edit($id)
    {
        $clinic = Clinic::with('plan')->findOrFail($id);
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('admin.saas.clinics.edit', compact('clinic', 'plans'));
    }

    public function update(Request $request, $id)
    {
        $clinic = Clinic::findOrFail($id);
        $referrer = null;
        $wasSubscribed = $clinic->isSubscribed();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clinics,email,' . $clinic->id,
            'phone_number' => 'required|string',
            'address' => 'nullable|string',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'subscription_ends_at' => 'nullable|date',
            'status' => 'required|in:active,trial,suspended,expired',
            'max_therapists' => 'required|integer|min:1',
            'max_patients' => 'required|integer|min:1',
            'referral_code' => $this->referralCodeRules($referrer, $clinic),
        ]);

        $status = $request->status;
        $subscriptionEndsAt = $request->subscription_ends_at;

        // Auto-correct hanya untuk mismatch 'expired' ↔ 'active' vs tanggal.
        // Pilihan 'trial' dan 'suspended' dari admin SELALU direspect tanpa override.
        if ($subscriptionEndsAt && in_array($status, ['active', 'expired'])) {
            $endDate = \Carbon\Carbon::parse($subscriptionEndsAt);

            if ($endDate->isFuture() && $status === 'expired') {
                // Admin set expired tapi tanggal belum lewat → koreksi ke active
                $status = 'active';
            } elseif ($endDate->isPast() && $status === 'active') {
                // Admin set active tapi tanggal sudah lewat → koreksi ke expired
                $status = 'expired';
            }
        }

        $clinic->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'subscription_plan_id' => $request->subscription_plan_id,
            'subscription_ends_at' => $subscriptionEndsAt,
            'status' => $status,
            'max_therapists' => $request->max_therapists,
            'max_patients' => $request->max_patients,
            'is_active' => $status !== 'suspended',
            'notes' => $request->notes,
        ]);

        // Klinik perujuk hanya bisa diubah selama komisi referal belum tercatat
        if (!$clinic->referralCommission()->exists()) {
            $clinic->update(['referred_by_clinic_id' => $referrer?->id]);
        }

        // Komisi hanya dicatat saat klinik berpindah dari belum berlangganan (trial/expired) menjadi berlangganan paket
        if (!$wasSubscribed && $clinic->isSubscribed()) {
            $clinic->recordReferralCommission();
        }

        return redirect()->route('saas.clinics.index')->with('success', "Data klinik {$clinic->name} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $clinic = Clinic::findOrFail($id);
        if ($clinic->id == 1) {
            return redirect()->back()->with('error', 'Klinik default RSTBDI tidak dapat dihapus.');
        }

        $clinic->update(['status' => 'suspended', 'is_active' => false]);
        return redirect()->route('saas.clinics.index')->with('success', "Klinik {$clinic->name} berhasil di-suspend.");
    }

    /**
     * Aturan validasi kode referal klinik perujuk. Klinik pemilik kode ditampung ke $referrer.
     */
    protected function referralCodeRules(?Clinic &$referrer, ?Clinic $clinic = null): array
    {
        return [
            'nullable',
            'string',
            'max:20',
            function ($attribute, $value, $fail) use (&$referrer, $clinic) {
                // Perujuk yang sudah tersimpan tetap diterima walau kliniknya kini nonaktif
                $current = $clinic?->referrer;
                $referrer = ($current && strtoupper(trim($value)) === $current->referral_code)
                    ? $current
                    : Clinic::findByReferralCode($value);

                if (!$referrer) {
                    $fail('Kode referal tidak ditemukan atau klinik pemilik kode sudah tidak aktif.');
                } elseif ($clinic && $referrer->id === $clinic->id) {
                    $fail('Klinik tidak dapat menggunakan kode referalnya sendiri.');
                }
            },
        ];
    }

    /**
     * Fitur Impersonate / Masuk & Tinjau Operasional Klinik Ini
     */
    public function impersonate($id)
    {
        $clinic = Clinic::findOrFail($id);
        session(['active_clinic_id' => $clinic->id]);
        return redirect()->route('dashboard')->with('info', "Anda sedang masuk meninjau operasional: {$clinic->name}");
    }

    /**
     * Keluar dari mode tinjau klinik
     */
    public function exitImpersonation()
    {
        session()->forget('active_clinic_id');
        return redirect()->route('saas.clinics.index')->with('info', 'Keluar dari mode tinjau klinik, kembali ke Panel SaaS Administrator.');
    }
}
