<?php

namespace App\Http\Controllers;

use App\Models\MasterDoctor;
use Illuminate\Http\Request;
use App\Imports\MedicineImport;
use Maatwebsite\Excel\Facades\Excel;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-doctor.view')->only('index');
        $this->middleware('permission:master-doctor.create')->only(['create', 'store']);
        $this->middleware('permission:master-doctor.edit')->only(['edit', 'update']);
        $this->middleware('permission:master-doctor.delete')->only('destroy');
    }

    public function index(Request $request)
    {

        $query = MasterDoctor::query();
    
        $doctors = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pages.master-doctor.index', compact('doctors'));
    }

    public function create()
    {
        return view('admin.pages.master-doctor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_code' => 'required|unique:master_doctors,doctor_code',
            'employee_code' => 'required|unique:master_doctors,employee_code',
            'full_name' => 'required',
            'specialization' => 'required',
            'email' => 'required|email|unique:master_doctors,email',
        ]);

        $doc = MasterDoctor::create($request->all());

        return redirect()->route('doctors.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $doc = MasterDoctor::where('id', $id)->first();

        return view('admin.pages.master-doctor.edit', compact('doc'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'doctor_code' => 'required|unique:master_doctors,doctor_code,'.$id,
            'employee_code' => 'required|unique:master_doctors,employee_code,'.$id,
            'full_name' => 'required',
            'specialization' => 'required',
            'email' => 'required|email|unique:master_doctors,email,'.$id,
        ]);

        $doc = MasterDoctor::where('id', $id)->first();

        $doc->update($request->all());

        return redirect()->route('doctors.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $doc = MasterDoctor::where('id', $id)->first();
        $doc->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
