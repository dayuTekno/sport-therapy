<?php

namespace App\Http\Controllers;

use App\Models\AdmissionQueue;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
    /**
     * Kiosk: Tampilan untuk mengambil nomor antrian
     */
    public function index()
    {
        return view('admin.pages.antrian.index');
    }

    /**
     * Kiosk: Proses ambil nomor antrian
     */
    public function generate(Request $request)
    {
        $today = now()->format('Y-m-d');
        
        // Cari nomor antrian terakhir untuk hari ini
        $lastQueue = AdmissionQueue::whereDate('created_at', $today)
            ->orderBy('queue_number', 'desc')
            ->first();

        $nextNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;
        $queueCode = 'A-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $queue = AdmissionQueue::create([
            'queue_number' => $nextNumber,
            'queue_code' => $queueCode,
            'status' => 'waiting',
        ]);

        return redirect()->route('antrian.ticket', $queue->id);
    }

    /**
     * Kiosk: Tampilan tiket setelah mengambil nomor antrian
     */
    public function ticket($id)
    {
        $queue = AdmissionQueue::findOrFail($id);
        return view('admin.pages.antrian.ticket', compact('queue'));
    }

    /**
     * Admin: Dashboard untuk memanggil antrian admisi
     */
    public function dashboard()
    {
        $today = now()->format('Y-m-d');

        $queues = AdmissionQueue::whereDate('created_at', $today)
            ->orderBy('queue_number', 'asc')
            ->get();
            
        $currentQueue = AdmissionQueue::whereDate('created_at', $today)
            ->where('status', 'called')
            ->orderBy('updated_at', 'desc')
            ->first();

        return view('admin.pages.antrian.dashboard', compact('queues', 'currentQueue'));
    }

    /**
     * Admin: Proses memanggil nomor antrian berikutnya
     */
    public function call($id)
    {
        $today = now()->format('Y-m-d');

        // Tandai yang sebelumnya "called" menjadi "completed"
        AdmissionQueue::where('status', 'called')
            ->whereDate('created_at', $today)
            ->update(['status' => 'completed']);
            
        // Panggil antrian yang dipilih
        $queue = AdmissionQueue::findOrFail($id);
        $queue->update(['status' => 'called']);
        
        return redirect()->back()->with('success', 'Antrian ' . $queue->queue_code . ' sedang dipanggil.');
    }
}
