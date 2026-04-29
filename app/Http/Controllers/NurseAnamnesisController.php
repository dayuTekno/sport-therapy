<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class NurseAnamnesisController extends Controller
{
    public function index()
    {
        // Get queues for today that are waiting for nurse
        $today = now()->format('Y-m-d');
        $queues = Antrian::with(['patient', 'poly'])
            ->whereDate('created_at', $today)
            ->whereIn('status', ['waiting', 'nurse_exam'])
            ->orderBy('queue_number', 'asc')
            ->get();

        return view('admin.pages.anamnesis.index', compact('queues'));
    }

    public function process($queue_id)
    {
        $queue = Antrian::with(['patient', 'poly'])->findOrFail($queue_id);
        
        // Tandai sedang diperiksa perawat
        if ($queue->status == 'waiting') {
            $queue->update(['status' => 'nurse_exam']);
        }

        // Cek apakah sudah ada medical record yang di draft
        $record = MedicalRecord::where('queue_id', $queue->id)->first();

        return view('admin.pages.anamnesis.process', compact('queue', 'record'));
    }

    public function store(Request $request, $queue_id)
    {
        $queue = Antrian::findOrFail($queue_id);

        $request->validate([
            'systolic' => 'nullable|integer',
            'diastolic' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'symptoms' => 'required|string',
            'diagnosis' => 'nullable|string',
            'icd10_id' => 'nullable|exists:master_icds,id',
            'icd9_id' => 'nullable|exists:master_icds,id',
        ]);

        MedicalRecord::updateOrCreate(
            ['queue_id' => $queue->id],
            [
                'patient_id' => $queue->patient_id,
                'nurse_name' => auth()->user()->name ?? 'Perawat',
                'systolic' => $request->systolic,
                'diastolic' => $request->diastolic,
                'temperature' => $request->temperature,
                'heart_rate' => $request->heart_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'weight' => $request->weight,
                'height' => $request->height,
                'symptoms' => $request->symptoms,
                'diagnosis' => $request->diagnosis,
                'icd10_id' => $request->icd10_id,
                'icd9_id' => $request->icd9_id,
            ]
        );

        $queue->update(['status' => 'waiting_doctor']);

        return redirect()->route('anamnesis.index')->with('success', 'Amnesa perawat berhasil disimpan. Pasien sekarang menunggu dokter.');
    }
}
