<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MasterPatients;
use App\Models\MedicalRecord;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapyEquipment;
use App\Models\TherapySession;
use App\Models\TherapyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Jika pengguna adalah Administrator SaaS dan tidak sedang meninjau klinik tertentu
        if (auth()->user()->isSaasAdmin() && !session()->has('active_clinic_id')) {
            return redirect()->route('saas.dashboard');
        }

        $today = now()->format('Y-m-d');

        // 1. Jumlah Antrian / Reservasi Hari Ini
        $todayQueues = Antrian::whereDate('created_at', $today)->count();
        $todayReservations = Reservation::whereDate('created_at', $today)->count();
        $totalQueues = $todayQueues > 0 ? $todayQueues : $todayReservations;

        // 2. Jumlah Selesai Diperiksa / Ditangani Hari Ini
        $examinedQueues = Antrian::whereDate('created_at', $today)
            ->whereIn('status', ['payment', 'pharmacy', 'completed'])
            ->count();
        $completedSessions = TherapySession::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();
        $totalCompleted = max($examinedQueues, $completedSessions);

        // 3. Total Master Pasien Terdaftar
        $totalPatients = MasterPatients::count();

        // 4. Pendapatan Real Hari Ini (Hanya yang sudah bayar/lunas)
        $paidQueues = Antrian::with('eselon')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['pharmacy', 'completed'])
            ->get();

        $paidQueueIds = $paidQueues->pluck('id');
        $revenue = 0;
        
        $records = MedicalRecord::with(['treatments.procedure', 'medicines.medicine'])
            ->whereIn('queue_id', $paidQueueIds)
            ->get();

        foreach ($records as $record) {
            $queue = $paidQueues->firstWhere('id', $record->queue_id);
            $eselonName = strtolower($queue->eselon->name ?? '');
            
            if (str_contains($eselonName, 'bpjs')) {
                continue;
            }

            $itemTotal = 0;
            foreach ($record->treatments as $t) {
                $itemTotal += $t->procedure->price ?? 0;
            }
            foreach ($record->medicines as $m) {
                $qty = ceil((float) $m->quantity);
                $price = $m->medicine->price ?? 0;
                $disc = $m->medicine->discount ?? 0;
                $itemTotal += $qty * ($price - $disc);
            }
            $revenue += max(0, $itemTotal - ($record->discount ?? 0));
        }

        // Pendapatan dari Kasir Terapi Sport Clinic Hari Ini (Per Reservasi)
        $therapyRevenue = Reservation::whereDate('paid_at', $today)
            ->where('payment_status', 'paid')
            ->sum('paid_amount');
        if ($therapyRevenue == 0) {
            $therapyRevenue = TherapySession::whereDate('paid_at', $today)
                ->where('payment_status', 'paid')
                ->sum('paid_amount');
        }
        $revenue += $therapyRevenue;

        // 5. Data Ringkasan Operasional Sport Therapist
        $totalTherapists = Therapist::count();
        $totalEquipments = TherapyEquipment::count();
        $totalTherapyTypes = TherapyType::count();
        $pendingReservations = Reservation::where('status', 'pending_confirmation')->count();

        return view('admin.pages.dashboard', compact(
            'totalQueues', 
            'totalCompleted', 
            'examinedQueues', 
            'totalPatients',
            'revenue', 
            'totalTherapists',
            'totalEquipments',
            'totalTherapyTypes',
            'pendingReservations'
        ));
    }
}
