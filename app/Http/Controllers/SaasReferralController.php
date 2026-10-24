<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicReferralCommission;
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
        $request->validate([
            'commission_type' => 'required|in:rupiah,percent',
            'commission_value' => 'required|numeric|min:0' . ($request->commission_type === 'percent' ? '|max:100' : ''),
        ], [
            'commission_value.max' => 'Komisi persentase maksimal 100%.',
        ]);

        ClinicReferralCommission::saveSettings([
            'is_enabled' => $request->boolean('is_enabled'),
            'commission_type' => $request->commission_type,
            'commission_value' => (float) $request->commission_value,
        ]);

        return redirect()->route('saas.referrals.index')->with('success', 'Setting komisi referal klinik berhasil disimpan. Berlaku untuk komisi yang dicatat berikutnya.');
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
