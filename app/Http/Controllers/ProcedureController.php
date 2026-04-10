<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterProcedure;

class ProcedureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-procedures.view')->only('index');
        $this->middleware('permission:master-procedures.create')->only(['create', 'store']);
        $this->middleware('permission:master-procedures.edit')->only(['edit', 'update']);
        $this->middleware('permission:master-procedures.delete')->only('destroy');
    }

    public function index()
    {
        $procedures = MasterProcedure::
        orderBy('created_at', 'desc')
        ->paginate(10); // 👈 ini kuncinya

        return view('admin.pages.master-procedure.index', compact('procedures'));
    }

    public function create()
    {
        return view('admin.pages.master-procedure.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:master_procedures,name',
            'procedure_code' => 'required|unique:master_procedures,procedure_code',
        ]);

        $es = MasterProcedure::create([
            'procedure_code' => $request->procedure_code,
            'name' => $request->name,
            'desc' => $request->desc,
        ]);

        return redirect()->route('procedures.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $es = MasterProcedure::where('id', $id)->first();
        return view('admin.pages.master-procedure.edit', compact('es'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'procedure_code' => 'required|unique:master_procedures,procedure_code,' . $id,
            'name' => 'required|unique:master_procedures,name,' . $id,
        ]);

        $es = MasterProcedure::where('id', $id)->first();

        $es->update([
            'procedure_code' => $request->procedure_code,
            'name' => $request->name,
            'desc' => $request->desc,
        ]);

        return redirect()->route('procedures.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $es = MasterProcedure::where('id', $id)->first();
        $es->delete();
        return redirect()->route('procedures.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
