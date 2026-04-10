<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterPolyclinic;

class PolyclinicController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-polyclinic.view')->only('index');
        $this->middleware('permission:master-polyclinic.create')->only(['create', 'store']);
        $this->middleware('permission:master-polyclinic.edit')->only(['edit', 'update']);
        $this->middleware('permission:master-polyclinic.delete')->only('destroy');
    }

    public function index()
    {
        $polyclinics = MasterPolyclinic::
        orderBy('created_at', 'desc')
        ->paginate(10); // 👈 ini kuncinya

        return view('admin.pages.master-polyclinic.index', compact('polyclinics'));
    }

    public function create()
    {
        return view('admin.pages.master-polyclinic.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:master_polyclinics,name',
            'polyclinic_code' => 'required|unique:master_polyclinics,polyclinic_code',
            'is_active' => 'required',
        ]);

        $poly = MasterPolyclinic::create([
            'polyclinic_code' => $request->polyclinic_code,
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('polyclinics.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $poly = MasterPolyclinic::where('id', $id)->first();
        return view('admin.pages.master-polyclinic.edit', compact('poly'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'polyclinic_code' => 'required|unique:master_polyclinics,polyclinic_code,' . $id,
            'name' => 'required|unique:master_polyclinics,name,' . $id,
            'is_active' => 'required',
        ]);

        $poly = MasterPolyclinic::where('id', $id)->first();

        $poly->update([
            'polyclinic_code' => $request->polyclinic_code,
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('polyclinics.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $poly = MasterPolyclinic::where('id', $id)->first();
        $poly->delete();
        return redirect()->route('polyclinics.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
