<?php

namespace App\Http\Controllers;

use App\Models\TherapyType;
use Illuminate\Http\Request;

class TherapyTypeController extends Controller
{
    public function index()
    {
        $types = TherapyType::orderBy('stage_order', 'asc')->get();
        return view('admin.pages.therapy-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.pages.therapy-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:therapy_types,code',
            'name' => 'required',
            'description' => 'nullable',
            'duration_minutes' => 'required|numeric',
            'stage_order' => 'required|numeric',
            'price' => 'nullable|numeric',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        TherapyType::create($validated);

        return redirect()->route('therapy-types.index')->with('success', 'Jenis jenjang terapi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $type = TherapyType::findOrFail($id);
        return view('admin.pages.therapy-types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = TherapyType::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:therapy_types,code,' . $id,
            'name' => 'required',
            'description' => 'nullable',
            'duration_minutes' => 'required|numeric',
            'stage_order' => 'required|numeric',
            'price' => 'nullable|numeric',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $type->update($validated);

        return redirect()->route('therapy-types.index')->with('success', 'Data jenis terapi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $type = TherapyType::findOrFail($id);
        $type->delete();

        return redirect()->route('therapy-types.index')->with('success', 'Jenis terapi berhasil dihapus.');
    }
}
