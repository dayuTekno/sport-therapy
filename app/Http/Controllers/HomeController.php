<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MasterEselon;
use App\Models\MedicalRecord;
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
        $today = now()->format('Y-m-d');

        // 1. Jumlah Antrian Hari Ini
        $totalQueues = Antrian::whereDate('created_at', $today)->count();

        // 2. Jumlah Antrian Selesai (Sudah Ambil Obat)
        $completedQueues = Antrian::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        // 3. Jumlah Pasien Selesai Diperiksa (Status pharmacy, payment, atau completed)
        $examinedQueues = Antrian::whereDate('created_at', $today)
            ->whereIn('status', ['payment', 'pharmacy', 'completed'])
            ->count();

        // 4. Pendapatan Real Hari Ini (Hanya yang sudah bayar/lunas)
        // Status 'pharmacy' atau 'completed' berarti sudah melewati kasir (atau BPJS yang lunas otomatis)
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
            
            // Jika BPJS, pendapatan dari pasien adalah 0 (gratis)
            if (str_contains($eselonName, 'bpjs')) {
                continue;
            }

            $itemTotal = 0;
            // Tindakan
            foreach ($record->treatments as $t) {
                $itemTotal += $t->procedure->price ?? 0;
            }
            // Obat
            foreach ($record->medicines as $m) {
                $qty = ceil((float) $m->quantity);
                $price = $m->medicine->price ?? 0;
                $disc = $m->medicine->discount ?? 0;
                $itemTotal += $qty * ($price - $disc);
            }
            // Kurangi diskon global
            $revenue += max(0, $itemTotal - ($record->discount ?? 0));
        }

        // 5. Jumlah Pasien berdasarkan Eselon
        $eselonStats = MasterEselon::withCount(['queues' => function($query) use ($today) {
            $query->whereDate('created_at', $today)
                  ->whereIn('status', ['payment', 'pharmacy', 'completed']);
        }])->get();

        return view('admin.pages.dashboard', compact(
            'totalQueues', 
            'completedQueues', 
            'examinedQueues', 
            'revenue', 
            'eselonStats'
        ));
    }
}
