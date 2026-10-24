<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicReferralCommission;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SaasReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->isSaasAdmin(), 403);
            return $next($request);
        });
    }

    /**
     * Program Referal Klinik: setting komisi & rekap komisi antar klinik mitra
     */
    public function index(Request $request)
    {
        $settings = ClinicReferralCommission::settings();
        $plans = SubscriptionPlan::orderBy('sort_order')->orderBy('id')->get();

        $query = ClinicReferralCommission::with(['referrer', 'referred', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('referrer', fn ($c) => $c->where('name', 'ilike', "%{$s}%")->orWhere('referral_code', 'ilike', "%{$s}%"))
                  ->orWhereHas('referred', fn ($c) => $c->where('name', 'ilike', "%{$s}%"));
            });
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $totalPending = ClinicReferralCommission::where('status', 'pending')->sum('commission_amount');
        $totalPaid = ClinicReferralCommission::where('status', 'paid')->sum('commission_amount');
        $totalReferredClinics = Clinic::whereNotNull('referred_by_clinic_id')->count();

        // Klinik referred yang belum berlangganan (masih trial / belum menghasilkan komisi)
        $awaitingClinics = Clinic::with('referrer')
            ->whereNotNull('referred_by_clinic_id')
            ->whereDoesntHave('referralCommission')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $topReferrers = Clinic::has('referredClinics')
            ->withCount('referredClinics')
            ->withSum(['referralCommissions as total_commission' => fn ($q) => $q->where('status', '!=', 'cancelled')], 'commission_amount')
            ->orderByDesc('referred_clinics_count')
            ->take(5)
            ->get();

        return view('admin.saas.referrals.index', compact(
            'settings',
            'plans',
            'commissions',
            'totalPending',
            'totalPaid',
            'totalReferredClinics',
            'awaitingClinics',
            'topReferrers'
        ));
    }

    public function updateSettings(Request $request)
    {
        $plans = SubscriptionPlan::all()->keyBy('id');

        $request->validate([
            'plans' => 'required|array',
            'plans.*.type' => 'required|in:rupiah,percent',
            'plans.*.value' => 'required|numeric|min:0',
        ], [
            'plans.*.value.required' => 'Nilai komisi setiap paket wajib diisi (isi 0 jika tanpa komisi).',
        ]);

        foreach ($request->input('plans') as $planId => $row) {
            if ($row['type'] === 'percent' && (float) $row['value'] > 100) {
                $name = $plans->get($planId)?->name ?? "#{$planId}";
                return back()->withInput()->withErrors(["plans.{$planId}.value" => "Komisi persentase paket {$name} maksimal 100%."]);
            }
        }

        foreach ($request->input('plans') as $planId => $row) {
            $plans->get($planId)?->update([
                'referral_commission_type' => $row['type'],
                'referral_commission_value' => (float) $row['value'],
            ]);
        }

        ClinicReferralCommission::saveSettings([
            'is_enabled' => $request->boolean('is_enabled'),
        ]);

        return redirect()->route('saas.referrals.index')->with('success', 'Setting komisi referal per paket berhasil disimpan. Berlaku untuk komisi yang dicatat berikutnya.');
    }

    public function markPaid(Request $request, $id)
    {
        $commission = ClinicReferralCommission::findOrFail($id);

        $request->validate(['notes' => 'nullable|string|max:500']);

        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
            'notes' => $request->notes ?: $commission->notes,
        ]);

        return redirect()->back()->with('success', 'Komisi referal ditandai sudah dibayarkan ke ' . ($commission->referrer?->name ?? 'klinik perujuk') . '.');
    }

    public function cancel($id)
    {
        $commission = ClinicReferralCommission::findOrFail($id);
        $commission->update(['status' => 'cancelled', 'paid_at' => null]);

        return redirect()->back()->with('success', 'Komisi referal dibatalkan.');
    }
}
