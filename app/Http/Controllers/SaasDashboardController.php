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
        $totalClinics = Clinic::count();
        $activeClinics = Clinic::where('status', 'active')->count();
        $trialClinics = Clinic::where('status', 'trial')->count();
        $suspendedClinics = Clinic::where('status', 'suspended')->count();

        $totalTherapists = Therapist::withoutGlobalScopes()->count();
        $totalPatients = MasterPatients::withoutGlobalScopes()->count();
        $totalSessions = TherapySession::withoutGlobalScopes()->count();

        // Estimasi Pendapatan Langganan SaaS Bulanan (MRR)
        $mrr = Clinic::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(function($c) {
                return $c->plan?->price ?? 0;
            });

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
