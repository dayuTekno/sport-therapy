<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterEselon;

class EselonController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-eselon.view')->only('index');
        $this->middleware('permission:master-eselon.create')->only(['create', 'store']);
        $this->middleware('permission:master-eselon.edit')->only(['edit', 'update']);
        $this->middleware('permission:master-eselon.delete')->only('destroy');
    }

    public function index()
    {
        $eselons = MasterEselon::
        orderBy('created_at', 'desc')
        ->paginate(10); // 👈 ini kuncinya

        return view('admin.pages.master-eselon.index', compact('eselons'));
    }

    public function create()
    {
        return view('admin.pages.master-eselon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:master_eselons,name',
            'eselon_code' => 'required|unique:master_eselons,eselon_code',
        ]);

        $es = MasterEselon::create([
            'eselon_code' => $request->eselon_code,
            'name' => $request->name,
            'desc' => $request->desc,
        ]);

        return redirect()->route('eselons.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $es = MasterEselon::where('id', $id)->first();
        return view('admin.pages.master-eselon.edit', compact('es'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eselon_code' => 'required|unique:master_eselons,eselon_code,' . $id,
            'name' => 'required|unique:master_eselons,name,' . $id,
        ]);

        $es = MasterEselon::where('id', $id)->first();

        $es->update([
            'eselon_code' => $request->eselon_code,
            'name' => $request->name,
            'desc' => $request->desc,
        ]);

        return redirect()->route('eselons.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $es = MasterEselon::where('id', $id)->first();
        $es->delete();
        return redirect()->route('eselons.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
