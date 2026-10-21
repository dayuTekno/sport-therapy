<?php

namespace App\Http\Controllers;

use App\Models\MasterReferral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterReferral::query()
            ->withCount([
                'patients',
                'therapySessions',
                'therapySessions as completed_sessions_count' => function ($q) {
                    $q->where('therapy_sessions.status', 'completed');
                }
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%")
                  ->orWhere('agency', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $referrals = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Rekapitulasi ringkasan
        $allReferrals = MasterReferral::withCount([
            'patients',
            'therapySessions',
            'therapySessions as completed_sessions_count' => function ($q) {
                $q->where('therapy_sessions.status', 'completed');
            }
        ])->get();

        $totalReferrals = $allReferrals->count();
        $totalAffiliatedPatients = $allReferrals->sum('patients_count');
        $totalTherapySessions = $allReferrals->sum('therapy_sessions_count');
        $totalCompletedSessions = $allReferrals->sum('completed_sessions_count');

        // Total estimasi komisi (komisi * sesi selesai)
        $totalEstimatedCommission = $allReferrals->sum(function ($ref) {
            return ($ref->commission_amount ?? 0) * ($ref->completed_sessions_count ?? 0);
        });

        return view('admin.pages.referrals.index', compact(
            'referrals',
            'totalReferrals',
            'totalAffiliatedPatients',
            'totalTherapySessions',
            'totalCompletedSessions',
            'totalEstimatedCommission'
        ));
    }

    public function create()
    {
        // Suggest auto-generated code
        $lastId = MasterReferral::withoutGlobalScopes()->max('id') ?? 0;
        $suggestedCode = 'REF-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        return view('admin.pages.referrals.create', compact('suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'agency' => 'nullable|string|max:255',
            'commission_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable',
        ]);

        if (empty($validated['code'])) {
            $lastId = MasterReferral::withoutGlobalScopes()->max('id') ?? 0;
            $validated['code'] = 'REF-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['commission_amount'] = $request->filled('commission_amount') ? $request->commission_amount : 0;

        MasterReferral::create($validated);

        return redirect()->route('referrals.index')->with('success', 'Data Referal berhasil ditambahkan ke Master Data.');
    }

    public function show($id)
    {
        $referral = MasterReferral::withCount([
            'patients',
            'therapySessions',
            'therapySessions as completed_sessions_count' => function ($q) {
                $q->where('therapy_sessions.status', 'completed');
            }
        ])->with(['patients' => function ($pq) {
            $pq->withCount([
                'therapySessions',
                'therapySessions as completed_sessions_count' => function ($q) {
                    $q->where('therapy_sessions.status', 'completed');
                }
            ])->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('admin.pages.referrals.show', compact('referral'));
    }

    public function edit($id)
    {
        $referral = MasterReferral::findOrFail($id);
        return view('admin.pages.referrals.edit', compact('referral'));
    }

    public function update(Request $request, $id)
    {
        $referral = MasterReferral::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'agency' => 'nullable|string|max:255',
            'commission_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['commission_amount'] = $request->filled('commission_amount') ? $request->commission_amount : 0;

        $referral->update($validated);

        return redirect()->route('referrals.index')->with('success', 'Data Referal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $referral = MasterReferral::findOrFail($id);
        $referral->delete();

        return redirect()->route('referrals.index')->with('success', 'Data Referal berhasil dihapus.');
    }
}
