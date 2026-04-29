<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use App\Models\MasterEselon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPatients::query();

        if ($request->search) {
            $query->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%')
                  ->orWhere('patient_code', 'like', '%' . $request->search . '%');
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.pages.master-patient.index', compact('patients'));
    }

    public function create()
    {
        $eselons = MasterEselon::where('is_active', true)->get();
        return view('admin.pages.master-patient.create', compact('eselons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:master_patients,nik|digits:16',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        MasterPatients::create([
            'patient_code' => Str::uuid(),
            'nik' => $request->nik,
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'eselon_id' => $request->eselon_id,
        ]);

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil ditambahkan');
    }

    public function edit($id)
    {
        $patient = MasterPatients::findOrFail($id);
        $eselons = MasterEselon::where('is_active', true)->get();
        return view('admin.pages.master-patient.edit', compact('patient', 'eselons'));
    }

    public function update(Request $request, $id)
    {
        $patient = MasterPatients::findOrFail($id);

        $request->validate([
            'nik' => 'required|digits:16|unique:master_patients,nik,' . $id,
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $patient->update([
            'nik' => $request->nik,
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'eselon_id' => $request->eselon_id,
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
