<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use App\Models\MasterEselon;
use App\Models\Clinic;
use App\Models\MasterReferral;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function exportPdf(Request $request)
    {
        $query = MasterPatients::with('referral');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', '%' . $search . '%')
                  ->orWhere('phone_number', 'ilike', '%' . $search . '%')
                  ->orWhere('occupation', 'ilike', '%' . $search . '%')
                  ->orWhere('nik', 'ilike', '%' . $search . '%')
                  ->orWhere('patient_code', 'ilike', '%' . $search . '%')
                  ->orWhereHas('referral', function ($rq) use ($search) {
                      $rq->where('name', 'ilike', '%' . $search . '%')
                         ->orWhere('code', 'ilike', '%' . $search . '%')
                         ->orWhere('agency', 'ilike', '%' . $search . '%');
                  });
            });
        }

        $patients = $query->orderBy('full_name', 'asc')->get();

        $clinic = null;
        if (auth()->check()) {
            $clinic = auth()->user()->clinic;
        }
        if (!$clinic) {
            $clinic = Clinic::first();
        }

        $pdf = Pdf::loadView('admin.pages.master-patient.pdf', [
            'patients' => $patients,
            'clinic' => $clinic,
            'search' => $request->search,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        $filename = 'data-pasien-' . date('Ymd-His') . '.pdf';

        if ($request->get('mode') === 'stream' || $request->has('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function index(Request $request)
    {
        $query = MasterPatients::with('referral');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', '%' . $search . '%')
                  ->orWhere('phone_number', 'ilike', '%' . $search . '%')
                  ->orWhere('occupation', 'ilike', '%' . $search . '%')
                  ->orWhere('nik', 'ilike', '%' . $search . '%')
                  ->orWhere('patient_code', 'ilike', '%' . $search . '%')
                  ->orWhereHas('referral', function ($rq) use ($search) {
                      $rq->where('name', 'ilike', '%' . $search . '%')
                         ->orWhere('code', 'ilike', '%' . $search . '%')
                         ->orWhere('agency', 'ilike', '%' . $search . '%');
                  });
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.pages.master-patient.index', compact('patients'));
    }

    public function create(Request $request)
    {
        $eselons = MasterEselon::where('is_active', true)->get();
        $referrals = MasterReferral::where('is_active', true)->orderBy('name')->get();
        $phone = $request->get('phone');
        $from = $request->get('from');
        return view('admin.pages.master-patient.create', compact('eselons', 'phone', 'from', 'referrals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:25',
            'full_name' => 'required|string|max:255',
            'age' => 'required|numeric|min:1|max:120',
            'gender' => 'required|in:male,female',
            'occupation' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'nik' => 'nullable|digits:16|unique:master_patients,nik',
            'date_of_birth' => 'nullable|date',
            'eselon_id' => 'nullable|exists:master_eselons,id',
            'referral_id' => 'nullable|exists:master_referrals,id',
        ]);

        $patient = MasterPatients::create([
            'patient_code' => (string) Str::uuid(),
            'phone_number' => $request->phone_number,
            'full_name' => $request->full_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'nik' => $request->nik,
            'date_of_birth' => $request->date_of_birth,
            'eselon_id' => $request->eselon_id,
            'referral_id' => $request->referral_id,
        ]);

        if ($request->get('from') === 'reservation') {
            return redirect()->route('reservations.create', ['patient_id' => $patient->id, 'phone' => $patient->phone_number])
                ->with('success', "Pasien {$patient->full_name} berhasil didaftarkan! Data langsung terpilih di formulir reservasi.");
        }

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil ditambahkan ke Master Data');
    }

    public function edit($id)
    {
        $patient = MasterPatients::with('referral')->findOrFail($id);
        $eselons = MasterEselon::where('is_active', true)->get();
        $referrals = MasterReferral::where('is_active', true)->orderBy('name')->get();
        if ($patient->referral_id && !$referrals->contains('id', $patient->referral_id)) {
            $currentReferral = MasterReferral::find($patient->referral_id);
            if ($currentReferral) {
                $referrals->push($currentReferral);
            }
        }
        return view('admin.pages.master-patient.edit', compact('patient', 'eselons', 'referrals'));
    }

    public function update(Request $request, $id)
    {
        $patient = MasterPatients::findOrFail($id);

        $request->validate([
            'phone_number' => 'required|string|max:25',
            'full_name' => 'required|string|max:255',
            'age' => 'required|numeric|min:1|max:120',
            'gender' => 'required|in:male,female',
            'occupation' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'nik' => 'nullable|digits:16|unique:master_patients,nik,' . $id,
            'date_of_birth' => 'nullable|date',
            'eselon_id' => 'nullable|exists:master_eselons,id',
            'referral_id' => 'nullable|exists:master_referrals,id',
        ]);

        $patient->update([
            'phone_number' => $request->phone_number,
            'full_name' => $request->full_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'nik' => $request->nik,
            'date_of_birth' => $request->date_of_birth,
            'eselon_id' => $request->eselon_id,
            'referral_id' => $request->referral_id,
        ]);

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil diperbarui');
    }

    public function destroy($id)
    {
        $patient = MasterPatients::findOrFail($id);
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil dihapus');
    }
}
