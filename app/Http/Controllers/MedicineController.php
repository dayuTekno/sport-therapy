<?php

namespace App\Http\Controllers;

use App\Models\MasterMedicine;
use Illuminate\Http\Request;
use App\Imports\MedicineImport;
use Maatwebsite\Excel\Facades\Excel;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-medicine.view')->only('index');
        $this->middleware('permission:master-medicine.create')->only(['create', 'store']);
        $this->middleware('permission:master-medicine.edit')->only(['edit', 'update']);
        $this->middleware('permission:master-medicine.delete')->only('destroy');
    }

    public function index(Request $request)
    {

        $query = MasterMedicine::query();
    
        if ($request->filled('search')) {
            $query->where('medicine_name', 'like', '%' . $request->search . '%');
        }
    
        $medicines = $query
            ->orderBy('medicine_name', 'asc')
            ->paginate(10)
            ->withQueryString(); // biar pagination tetap bawa search

        return view('admin.pages.master-medicine.index', compact('medicines'));
    }

    public function create()
    {
        return view('admin.pages.master-medicine.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required|unique:master_medicines,medicine_name',
        ]);

        $med = MasterMedicine::create([
            'medicine_name' => $request->medicine_name,
            'medicine_international_name' => $request->medicine_international_name,
            'price' => $request->price,
            'discount_from_source' => $request->discount_from_source,
        ]);

        return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $med = MasterMedicine::where('id', $id)->first();

        return view('admin.pages.master-medicine.edit', compact('med'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|unique:master_medicines,medicine_name,'.$id,
        ]);

        $med = MasterMedicine::where('id', $id)->first();

        $med->update([
            'medicine_name' => $request->medicine_name,
            'medicine_international_name' => $request->medicine_international_name,
            'price' => $request->price,
            'discount_from_source' => $request->discount_from_source,
        ]);

        return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MedicineImport, $request->file('file'));

            return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil diimport');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $med = MasterMedicine::where('id', $id)->first();
        $med->delete();

        return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
