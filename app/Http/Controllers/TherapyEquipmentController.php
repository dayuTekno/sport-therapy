<?php

namespace App\Http\Controllers;

use App\Models\TherapyEquipment;
use Illuminate\Http\Request;

class TherapyEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = TherapyEquipment::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('equipment_code', 'ilike', "%{$search}%");
            });
        }

        $equipments = $query->orderBy('name', 'asc')->paginate(10);
        $categories = TherapyEquipment::select('category')->distinct()->whereNotNull('category')->pluck('category');

        return view('admin.pages.therapy-equipments.index', compact('equipments', 'categories'));
    }

    public function create()
    {
        return view('admin.pages.therapy-equipments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_code' => 'required|unique:therapy_equipments,equipment_code',
            'name' => 'required',
            'category' => 'nullable',
            'stock' => 'required|numeric|min:0',
            'uom' => 'required',
            'condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'description' => 'nullable',
        ]);

        TherapyEquipment::create($validated);

        return redirect()->route('therapy-equipments.index')->with('success', 'Peralatan terapi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $equipment = TherapyEquipment::findOrFail($id);
        return view('admin.pages.therapy-equipments.edit', compact('equipment'));
    }

    public function update(Request $request, $id)
    {
        $equipment = TherapyEquipment::findOrFail($id);

        $validated = $request->validate([
            'equipment_code' => 'required|unique:therapy_equipments,equipment_code,' . $id,
            'name' => 'required',
            'category' => 'nullable',
            'stock' => 'required|numeric|min:0',
            'uom' => 'required',
            'condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'description' => 'nullable',
            'is_available' => 'nullable',
        ]);

        $validated['is_available'] = $request->has('is_available') ? 1 : 0;

        $equipment->update($validated);

        return redirect()->route('therapy-equipments.index')->with('success', 'Data peralatan terapi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $equipment = TherapyEquipment::findOrFail($id);
        $equipment->delete();

        return redirect()->route('therapy-equipments.index')->with('success', 'Peralatan terapi berhasil dihapus.');
    }
}
