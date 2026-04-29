<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolyIcd10Controller extends Controller
{
    public function index()
    {
        $polyclinics = \App\Models\MasterPolyclinic::withCount('icds10')->orderBy('name', 'asc')->paginate(10);
        return view('admin.pages.poly-icds10.index', compact('polyclinics'));
    }

    public function edit($id)
    {
        $polyclinic = \App\Models\MasterPolyclinic::with('icds10')->findOrFail($id);
        return view('admin.pages.poly-icds10.edit', compact('polyclinic'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'icds' => 'nullable|array',
            'icds.*' => 'exists:master_icds,id',
        ]);

        $polyclinic = \App\Models\MasterPolyclinic::findOrFail($id);
        
        $newIcd10Ids = $request->icds ?? [];
        $currentIcd9Ids = $polyclinic->icds9()->pluck('master_icds.id')->toArray();
        $allIcds = array_merge($newIcd10Ids, $currentIcd9Ids);

        $polyclinic->icds()->sync($allIcds);

        return redirect()->back()->with('success', 'Data ICD 10 Poliklinik berhasil diperbarui');
    }
}
