<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolyProcedureController extends Controller
{
    public function index()
    {
        $polyclinics = \App\Models\MasterPolyclinic::withCount('procedures')->orderBy('name', 'asc')->paginate(10);
        return view('admin.pages.poly-procedures.index', compact('polyclinics'));
    }

    public function edit($id)
    {
        $polyclinic = \App\Models\MasterPolyclinic::with('procedures')->findOrFail($id);
        $procedures = \App\Models\MasterProcedure::orderBy('name', 'asc')->get();
        return view('admin.pages.poly-procedures.edit', compact('polyclinic', 'procedures'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'procedures' => 'nullable|array',
            'procedures.*' => 'exists:master_procedures,id',
        ]);

        $polyclinic = \App\Models\MasterPolyclinic::findOrFail($id);
        $polyclinic->procedures()->sync($request->procedures ?? []);

        return redirect()->back()->with('success', 'Data Tindakan Poliklinik berhasil diperbarui');
    }
}
