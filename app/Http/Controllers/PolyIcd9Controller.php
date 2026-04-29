<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolyIcd9Controller extends Controller
{
    public function index()
    {
        $polyclinics = \App\Models\MasterPolyclinic::withCount('icds9')->orderBy('name', 'asc')->paginate(10);
        return view('admin.pages.poly-icds9.index', compact('polyclinics'));
    }

    public function edit($id)
    {
        $polyclinic = \App\Models\MasterPolyclinic::with('icds9')->findOrFail($id);
        return view('admin.pages.poly-icds9.edit', compact('polyclinic'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'icds' => 'nullable|array',
            'icds.*' => 'exists:master_icds,id',
        ]);

        $polyclinic = \App\Models\MasterPolyclinic::findOrFail($id);
        
        $newIcd9Ids = $request->icds ?? [];
        $currentIcd10Ids = $polyclinic->icds10()->pluck('master_icds.id')->toArray();
        $allIcds = array_merge($newIcd9Ids, $currentIcd10Ids);

        $polyclinic->icds()->sync($allIcds);

        return redirect()->back()->with('success', 'Data ICD 9 Poliklinik berhasil diperbarui');
    }
}
