<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\LandingPageSetting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    /**
     * Halaman Depan / Bagian Luar Website (Public Landing Page)
     */
    public function landing()
    {
        $hero = LandingPageSetting::getSection('hero');
        $features = LandingPageSetting::getSection('features');
        $contact = LandingPageSetting::getSection('contact');
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $trialDays = LandingPageSetting::getTrialDays();

        return view('public.landing', compact('hero', 'features', 'contact', 'plans', 'trialDays'));
    }

    /**
     * Halaman Pendaftaran Mitra Klinik Baru (Free Trial)
     */
    public function registerClinicForm(Request $request)
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $trialDays = LandingPageSetting::getTrialDays();

        // Link undangan: /register-clinic?ref=KODE
        $referralCode = strtoupper(trim((string) old('referral_code', $request->query('ref', ''))));
        $referrer = Clinic::findByReferralCode($referralCode);

        return view('public.register-clinic', compact('plans', 'trialDays', 'referralCode', 'referrer'));
    }

    /**
     * Proses Pendaftaran Mitra Klinik Baru
     */
    public function registerClinicSubmit(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email|unique:clinics,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'referral_code' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (!Clinic::findByReferralCode($value)) {
                        $fail('Kode referal tidak ditemukan atau klinik pemilik kode sudah tidak aktif.');
                    }
                },
            ],
        ]);

        $referrer = Clinic::findByReferralCode($request->referral_code);

        $slug = Str::slug($request->clinic_name);
        $countSlug = Clinic::where('slug', 'like', "{$slug}%")->count();
        if ($countSlug > 0) {
            $slug .= '-' . ($countSlug + 1);
        }

        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
        $trialDays = $plan->trial_days ?: LandingPageSetting::getTrialDays();

        // Buat Tenant / Klinik Baru
        $clinic = Clinic::create([
            'clinic_code' => Clinic::generateClinicCode(),
            'name' => $request->clinic_name,
            'slug' => $slug,
            'subdomain' => Str::slug($request->clinic_name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays($trialDays),
            'subscription_plan_id' => $plan->id,
            'max_therapists' => $plan->max_therapists,
            'max_patients' => $plan->max_patients,
            'is_active' => true,
            'referred_by_clinic_id' => $referrer?->id,
            'notes' => 'Pendaftaran online via landing page publik.' . ($referrer ? " Referal dari {$referrer->name} ({$referrer->referral_code})." : ''),
        ]);

        // Buat Akun Admin Klinik
        $user = User::create([
            'name' => $request->admin_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'clinic_id' => $clinic->id,
        ]);

        // Assign Role admin klinik
        $user->assignRole('admin');

        // Otomatis login
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "Selamat datang di Smart Clinic Therapy! Klinik {$clinic->name} berhasil didaftarkan dengan masa uji coba gratis {$trialDays} hari.");
    }

    /**
     * XML Sitemap dinamis untuk Google, Bing, dan mesin pencari lainnya.
     */
    public function sitemap()
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('register.clinic'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
            [
                'loc' => route('login'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
        ];

        return response()->view('public.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
