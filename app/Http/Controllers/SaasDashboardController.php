<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicSubscription;
use App\Models\MasterPatients;
use App\Models\SubscriptionPlan;
use App\Models\Therapist;
use App\Models\TherapySession;
use Illuminate\Http\Request;

class SaasDashboardController extends Controller
{
    public function index()
    {
        // Metrik Platform Global SaaS
        $clinicStats = Clinic::query()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("COUNT(*) FILTER (WHERE status = 'active') as active_count")
            ->selectRaw("COUNT(*) FILTER (WHERE status = 'trial') as trial_count")
            ->selectRaw("COUNT(*) FILTER (WHERE status = 'suspended') as suspended_count")
            ->first();
        $totalClinics = (int) $clinicStats->total_count;
        $activeClinics = (int) $clinicStats->active_count;
        $trialClinics = (int) $clinicStats->trial_count;
        $suspendedClinics = (int) $clinicStats->suspended_count;

        $totalTherapists = Therapist::withoutGlobalScopes()->count();
        $totalPatients = MasterPatients::withoutGlobalScopes()->count();
        $totalSessions = TherapySession::withoutGlobalScopes()->count();

        // Estimasi Pendapatan Langganan SaaS Bulanan (MRR)
        // (dijumlahkan di database, tanpa memuat seluruh klinik ke memori)
        $mrr = (float) Clinic::where('clinics.status', 'active')
            ->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'clinics.subscription_plan_id')
            ->sum('subscription_plans.price');

        // 5 Klinik Terbaru
        $recentClinics = Clinic::with('plan')->orderBy('created_at', 'desc')->take(5)->get();

        // 5 Transaksi Langganan Terbaru
        $recentSubscriptions = ClinicSubscription::with(['clinic', 'plan'])->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.saas.dashboard', compact(
            'totalClinics',
            'activeClinics',
            'trialClinics',
            'suspendedClinics',
            'totalTherapists',
            'totalPatients',
            'totalSessions',
            'mrr',
            'recentClinics',
            'recentSubscriptions'
        ));
    }
}
