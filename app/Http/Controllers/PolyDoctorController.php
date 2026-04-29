<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolyDoctorController extends Controller
{
    public function index()
    {
        $polyclinics = \App\Models\MasterPolyclinic::withCount('doctors')->orderBy('name', 'asc')->paginate(10);
        return view('admin.pages.poly-doctors.index', compact('polyclinics'));
    }

    public function edit($id)
    {
        $polyclinic = \App\Models\MasterPolyclinic::with('doctors')->findOrFail($id);
        $doctors = \App\Models\MasterDoctor::orderBy('full_name', 'asc')->get();
        return view('admin.pages.poly-doctors.edit', compact('polyclinic', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'doctors' => 'nullable|array',
            'doctors.*' => 'exists:master_doctors,id',
        ]);

        $polyclinic = \App\Models\MasterPolyclinic::findOrFail($id);
        $polyclinic->doctors()->sync($request->doctors ?? []);

        return redirect()->back()->with('success', 'Data Dokter Poliklinik berhasil diperbarui');
    }
}
