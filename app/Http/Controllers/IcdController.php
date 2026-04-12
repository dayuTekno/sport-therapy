<?php

namespace App\Http\Controllers;

use App\Models\MasterIcd;
use Illuminate\Http\Request;
use App\Imports\MedicineImport;
use Maatwebsite\Excel\Facades\Excel;

class IcdController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-icd.view')->only('index');
        $this->middleware('permission:master-icd.create')->only(['create', 'store']);
        $this->middleware('permission:master-icd.edit')->only(['edit', 'update']);
        $this->middleware('permission:master-icd.delete')->only('destroy');
    }

    public function index(Request $request)
    {

        $category = $request->category ?? 9;
        $query = $this->getAllByCategory($category);
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        $icds = $query->paginate(10)->withQueryString();

        return view('admin.pages.master-icd.index', compact('icds', 'category'));
    }

    private function getAllByCategory($category)
    {
        return MasterIcd::where('category', $category);
    }

    public function create()
    {
        return view('admin.pages.master-icd.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:master_icds,name',
        ]);

        $icd = MasterIcd::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('icds.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $icd = MasterIcd::where('id', $id)->first();

        return view('admin.pages.master-icd.edit', compact('icd'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:master_icds,name,'.$id,
        ]);

        $icd = MasterIcd::where('id', $id)->first();

        $icd->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('icds.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MedicineImport, $request->file('file'));

            return redirect()->route('icds.index')
            ->with('success', 'Data berhasil diimport');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $icd = MasterIcd::where('id', $id)->first();
        $icd->delete();

        return redirect()->route('icds.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
