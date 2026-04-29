<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDiagnosis;
use App\Models\MedicalRecordProcedure;
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
        $queue = Antrian::with(['patient.eselon', 'poly'])->findOrFail($queue_id);
        
        // Tandai sedang diperiksa perawat
        if ($queue->status == 'waiting') {
            $queue->update(['status' => 'nurse_exam']);
        }

        // Cek apakah sudah ada medical record yang di draft
        $record = MedicalRecord::where('queue_id', $queue->id)->first();

        // Load existing diagnoses & procedures
        $nurseDiagnoses = $record ? MedicalRecordDiagnosis::with('icd')
            ->where('medical_record_id', $record->id)
            ->where('source', 'nurse')
            ->get() : collect();

        $nurseProcedures = $record ? MedicalRecordProcedure::with('icd')
            ->where('medical_record_id', $record->id)
            ->where('source', 'nurse')
            ->get() : collect();

        return view('admin.pages.anamnesis.process', compact('queue', 'record', 'nurseDiagnoses', 'nurseProcedures'));
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
            'icd10_ids' => 'required|array|min:1',
            'icd10_ids.*' => 'exists:master_icds,id',
            'icd10_primary' => 'nullable|integer',
            'icd9_ids' => 'nullable|array',
            'icd9_ids.*' => 'exists:master_icds,id',
        ]);

        $record = MedicalRecord::updateOrCreate(
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
            ]
        );

        // Delete old nurse diagnoses & procedures, then re-insert
        MedicalRecordDiagnosis::where('medical_record_id', $record->id)->where('source', 'nurse')->delete();
        MedicalRecordProcedure::where('medical_record_id', $record->id)->where('source', 'nurse')->delete();

        // Insert ICD-10 diagnoses
        $primaryId = $request->icd10_primary;
        foreach ($request->icd10_ids as $index => $icdId) {
            MedicalRecordDiagnosis::create([
                'medical_record_id' => $record->id,
                'icd_id' => $icdId,
                'type' => ($icdId == $primaryId || ($index == 0 && !$primaryId)) ? 'primary' : 'secondary',
                'source' => 'nurse',
            ]);
        }

        // Insert ICD-9 procedures
        if ($request->icd9_ids) {
            foreach ($request->icd9_ids as $icdId) {
                MedicalRecordProcedure::create([
                    'medical_record_id' => $record->id,
                    'icd_id' => $icdId,
                    'source' => 'nurse',
                ]);
            }
        }

        $queue->update(['status' => 'waiting_doctor']);

        return redirect()->route('anamnesis.index')->with('success', 'Amnesa perawat berhasil disimpan. Pasien sekarang menunggu dokter.');
    }
}
