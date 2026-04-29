<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDiagnosis;
use App\Models\MedicalRecordProcedure;
use App\Models\MedicalRecordTreatment;
use App\Models\MedicalRecordMedicine;
use Illuminate\Http\Request;

class DoctorExaminationController extends Controller
{
    /**
     * Daftar pasien menunggu pemeriksaan dokter
     */
    public function index()
    {
        $today = now()->format('Y-m-d');
        $queues = Antrian::with(['patient', 'poly', 'doctor'])
            ->whereDate('created_at', $today)
            ->whereIn('status', ['waiting_doctor', 'doctor_exam'])
            ->orderBy('queue_number', 'asc')
            ->get();

        return view('admin.pages.doctor-exam.index', compact('queues'));
    }

    /**
     * Proses pemeriksaan dokter - tampilkan hasil amnesa perawat + form dokter
     */
    public function process($queue_id)
    {
        $queue = Antrian::with(['patient.eselon', 'poly', 'doctor'])->findOrFail($queue_id);
        
        // Ambil medical record dari amnesa perawat
        $record = MedicalRecord::where('queue_id', $queue->id)->firstOrFail();

        // Tandai sedang diperiksa dokter
        if ($queue->status == 'waiting_doctor') {
            $queue->update(['status' => 'doctor_exam']);
        }

        // Load nurse diagnoses & procedures
        $nurseDiagnoses = MedicalRecordDiagnosis::with('icd')
            ->where('medical_record_id', $record->id)
            ->where('source', 'nurse')
            ->get();

        $nurseProcedures = MedicalRecordProcedure::with('icd')
            ->where('medical_record_id', $record->id)
            ->where('source', 'nurse')
            ->get();

        // Load doctor diagnoses & procedures (if already saved)
        $doctorDiagnoses = MedicalRecordDiagnosis::with('icd')
            ->where('medical_record_id', $record->id)
            ->where('source', 'doctor')
            ->get();

        $doctorProcedures = MedicalRecordProcedure::with('icd')
            ->where('medical_record_id', $record->id)
            ->where('source', 'doctor')
            ->get();

        // Load treatments (master procedures)
        $treatments = MedicalRecordTreatment::with('procedure')
            ->where('medical_record_id', $record->id)
            ->get();

        // Load medicines (master medicines)
        $medicines = MedicalRecordMedicine::with('medicine')
            ->where('medical_record_id', $record->id)
            ->get();

        return view('admin.pages.doctor-exam.process', compact(
            'queue', 'record',
            'nurseDiagnoses', 'nurseProcedures',
            'doctorDiagnoses', 'doctorProcedures',
            'treatments', 'medicines'
        ));
    }

    /**
     * Simpan hasil pemeriksaan dokter
     */
    public function store(Request $request, $queue_id)
    {
        $queue = Antrian::findOrFail($queue_id);
        $record = MedicalRecord::where('queue_id', $queue->id)->firstOrFail();

        $request->validate([
            'doctor_icd10_ids' => 'required|array|min:1',
            'doctor_icd10_ids.*' => 'exists:master_icds,id',
            'doctor_icd9_ids' => 'nullable|array',
            'doctor_icd9_ids.*' => 'exists:master_icds,id',
            'doctor_diagnosis' => 'required|string',
            'doctor_notes' => 'nullable|string',
            'treatment_ids' => 'nullable|array',
            'treatment_ids.*' => 'exists:master_procedures,id',
            // Medicine validation
            'medicine_ids' => 'nullable|array',
            'medicine_ids.*' => 'exists:master_medicines,id',
            'quantities' => 'nullable|array',
            'instructions' => 'nullable|array',
        ]);

        $record->update([
            'doctor_name' => auth()->user()->name ?? 'Dokter',
            'doctor_diagnosis' => $request->doctor_diagnosis,
            'doctor_notes' => $request->doctor_notes,
            'prescription' => null, // We'll use medical_record_medicines instead
        ]);

        // Delete old doctor diagnoses & procedures & treatments & medicines, then re-insert
        MedicalRecordDiagnosis::where('medical_record_id', $record->id)->where('source', 'doctor')->delete();
        MedicalRecordProcedure::where('medical_record_id', $record->id)->where('source', 'doctor')->delete();
        MedicalRecordTreatment::where('medical_record_id', $record->id)->delete();
        MedicalRecordMedicine::where('medical_record_id', $record->id)->delete();

        // Insert doctor ICD-10 diagnoses
        foreach ($request->doctor_icd10_ids as $index => $icdId) {
            MedicalRecordDiagnosis::create([
                'medical_record_id' => $record->id,
                'icd_id' => $icdId,
                'type' => $index == 0 ? 'primary' : 'secondary',
                'source' => 'doctor',
            ]);
        }

        // Insert doctor ICD-9 procedures
        if ($request->doctor_icd9_ids) {
            foreach ($request->doctor_icd9_ids as $icdId) {
                MedicalRecordProcedure::create([
                    'medical_record_id' => $record->id,
                    'icd_id' => $icdId,
                    'source' => 'doctor',
                ]);
            }
        }

        // Insert treatments from master procedures
        if ($request->treatment_ids) {
            foreach ($request->treatment_ids as $procId) {
                MedicalRecordTreatment::create([
                    'medical_record_id' => $record->id,
                    'procedure_id' => $procId,
                ]);
            }
        }

        // Insert medicines from master medicines & deduct stock
        if ($request->medicine_ids) {
            foreach ($request->medicine_ids as $index => $medId) {
                $qty = $request->quantities[$index] ?? 0;
                $medicine = \App\Models\MasterMedicine::findOrFail($medId);
                
                // Cek stok sebelum pemotongan
                $stockToDeduct = ceil((float) $qty);
                if ($medicine->stock < $stockToDeduct) {
                    return back()->withInput()->with('error', "Stok obat '{$medicine->medicine_name}' tidak mencukupi. Stok tersedia: {$medicine->stock}.");
                }

                // Simpan record resep
                MedicalRecordMedicine::create([
                    'medical_record_id' => $record->id,
                    'medicine_id' => $medId,
                    'quantity' => $qty,
                    'instructions' => $request->instructions[$index] ?? null,
                ]);

                // Kurangi stok
                if ($stockToDeduct > 0) {
                    $medicine->decrement('stock', $stockToDeduct);
                }
            }
        }

        // Tentukan alur berikutnya berdasarkan kategori pasien (diambil dari data pendaftaran/antrian)
        $eselonName = $queue->eselon->name ?? '';
        
        // Kondisi: Jika BPJS maka otomatis Lunas (Apotik), selain itu harus ke Kasir
        $isBpjs = str_contains(strtolower($eselonName), 'bpjs');
        
        $nextStatus = $isBpjs ? 'pharmacy' : 'payment';
        $queue->update(['status' => $nextStatus]);

        $msg = $isBpjs ? 'Pemeriksaan selesai (BPJS). Pasien diarahkan langsung ke Apotik.' : 'Pemeriksaan selesai. Pasien diarahkan ke Kasir untuk pelunasan.';

        return redirect()->route('doctor-exam.index')->with('success', $msg);
    }
}
