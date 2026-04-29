<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Antrian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MedicalRecordReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $query = Antrian::with(['patient', 'poly', 'doctor', 'eselon'])
            ->whereIn('status', ['completed', 'pharmacy']) // Selesai periksa atau sudah ambil obat
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('patient_code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('poly_id')) {
            $query->where('poly_id', $request->poly_id);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $polyclinics = \App\Models\MasterPolyclinic::all();

        return view('admin.pages.reports.medical-records.index', compact('records', 'startDate', 'endDate', 'polyclinics'));
    }

    public function show($id)
    {
        $queue = Antrian::with(['patient.eselon', 'poly', 'doctor'])->findOrFail($id);
        $record = MedicalRecord::with([
            'diagnoses.icd', 
            'procedures.icd', 
            'treatments.procedure', 
            'medicines.medicine'
        ])->where('queue_id', $id)->firstOrFail();

        return view('admin.pages.reports.medical-records.show', compact('queue', 'record'));
    }
}
