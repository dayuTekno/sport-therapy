<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use Illuminate\Http\Request;

class TherapistController extends Controller
{
    public function index(Request $request)
    {
        $query = Therapist::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('specialization', 'ilike', "%{$search}%")
                  ->orWhere('therapist_code', 'ilike', "%{$search}%");
            });
        }

        $therapists = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.pages.therapists.index', compact('therapists'));
    }

    public function create()
    {
        return view('admin.pages.therapists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'therapist_code' => 'required|unique:therapists,therapist_code',
            'full_name' => 'required',
            'specialization' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:therapists,email',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Therapist::create($validated);

        return redirect()->route('therapists.index')->with('success', 'Data Terapis berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $therapist = Therapist::findOrFail($id);
        return view('admin.pages.therapists.edit', compact('therapist'));
    }

    public function update(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validated = $request->validate([
            'therapist_code' => 'required|unique:therapists,therapist_code,' . $id,
            'full_name' => 'required',
            'specialization' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:therapists,email,' . $id,
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $therapist->update($validated);

        return redirect()->route('therapists.index')->with('success', 'Data Terapis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $therapist = Therapist::findOrFail($id);
        $therapist->delete();

        return redirect()->route('therapists.index')->with('success', 'Data Terapis berhasil dihapus.');
    }
}
