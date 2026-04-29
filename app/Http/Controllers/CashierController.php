<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $queues = Antrian::with(['patient.eselon', 'poly'])
            ->where('status', 'payment')
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('admin.pages.cashier.index', compact('queues'));
    }

    public function process($id)
    {
        $queue = Antrian::with(['patient.eselon', 'poly', 'doctor'])->findOrFail($id);
        $record = MedicalRecord::with(['treatments.procedure', 'medicines.medicine'])->where('queue_id', $id)->first();

        if (!$record) {
            return redirect()->route('cashier.index')->with('error', 'Data rekam medis tidak ditemukan.');
        }

        // Hitung total awal
        $total = 0;
        foreach ($record->treatments as $t) {
            $total += $t->procedure->price ?? 0;
        }
        foreach ($record->medicines as $m) {
            $qty = ceil((float) $m->quantity);
            $price = $m->medicine->price ?? 0;
            $discountPerItem = $m->medicine->discount ?? 0; // Diskon dari master data
            
            $total += $qty * ($price - $discountPerItem);
        }

        return view('admin.pages.cashier.process', compact('queue', 'record', 'total'));
    }

    public function store(Request $request, $id)
    {
        $queue = Antrian::findOrFail($id);
        $record = MedicalRecord::where('queue_id', $id)->first();

        if ($record) {
            $record->update([
                'discount' => $request->discount ?? 0
            ]);
        }
        
        // Update status ke pharmacy setelah dibayar
        $queue->update(['status' => 'pharmacy']);

        return redirect()->route('cashier.index')->with('success', 'Pembayaran berhasil dikonfirmasi. Silakan pasien ke Apotik.');
    }
}
