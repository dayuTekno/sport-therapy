<?php

namespace App\Http\Controllers;

use App\Models\MasterIcd;
use Illuminate\Http\Request;
use App\Imports\ICDImport;
use Maatwebsite\Excel\Facades\Excel;

class Icd10Controller extends Controller
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

        $category = (string) ($request->category ?? '10');
        $query = MasterIcd::where('category', $category);
        $query = $this->getAllByCategory($category);
        if ($request->search) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }    
        $icds = $query->orderBy('icd_code', 'ASC')->paginate(10)->withQueryString();

        return view('admin.pages.master-icd.icd10.index', compact('icds', 'category'));
    }

    private function getAllByCategory($category)
    {
        $data = MasterIcd::where('category', $category); 
        return $data;
    }

    public function create(Request $request)
    {
        $category = $request->category;
        return view('admin.pages.master-icd.icd10.create', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'icd_code' => 'required|unique:master_icds,icd_code',
            'name' => 'required|unique:master_icds,name',
            'category' => 'required',
            'version' => 'required',
            'is_active' => 'required',
        ]);

        $icd = MasterIcd::create([
            'icd_code' => $request->icd_code,
            'name' => $request->name,
            'category' => $request->category,
            'version' => $request->version,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('icds10.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $icd = MasterIcd::where('id', $id)->first();

        return view('admin.pages.master-icd.icd10.edit', compact('icd'));
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

        return redirect()->route('icds10.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function import(Request $request){

        set_time_limit(300); // 5 menit
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'category' => 'required'
        ]);

        try {
            Excel::import(new ICDImport($request->category), $request->file('file'));


            return redirect()->route('icds10.index')
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

        return redirect()->route('icds10.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
