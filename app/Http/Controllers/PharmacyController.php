<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index()
    {
        $queues = Antrian::with(['patient.eselon', 'poly'])
            ->where('status', 'pharmacy')
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('admin.pages.pharmacy.index', compact('queues'));
    }

    public function process($id)
    {
        $queue = Antrian::with(['patient.eselon', 'poly', 'doctor'])->findOrFail($id);
        $record = MedicalRecord::with(['medicines.medicine'])->where('queue_id', $id)->first();

        if (!$record) {
            return redirect()->route('pharmacy.index')->with('error', 'Data resep tidak ditemukan.');
        }

        return view('admin.pages.pharmacy.process', compact('queue', 'record'));
    }

    public function store(Request $request, $id)
    {
        $queue = Antrian::findOrFail($id);
        
        // Update status ke completed setelah obat diberikan
        $queue->update(['status' => 'completed']);

        return redirect()->route('pharmacy.index')->with('success', 'Obat telah diserahkan. Pasien selesai.');
    }
}
