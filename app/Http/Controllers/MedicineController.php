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
            $query->where('medicine_name', 'ilike', '%' . $request->search . '%');
        }
    
        $medicines = $query
            ->orderBy('medicine_name', 'asc')
            ->paginate(10)
            ->withQueryString();

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
            'stock' => 'nullable|numeric|min:0',
        ]);

        MasterMedicine::create([
            'medicine_name' => $request->medicine_name,
            'medicine_international_name' => $request->medicine_international_name,
            'price' => $request->price ?? 0,
            'discount' => $request->discount ?? 0,
            'discount_from_source' => $request->discount_from_source,
            'stock' => $request->stock ?? 0,
            'uom' => $request->uom ?? 'Tablet',
        ]);

        return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil dibuat');
    }

    public function edit($id)
    {
        $med = MasterMedicine::findOrFail($id);
        return view('admin.pages.master-medicine.edit', compact('med'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|unique:master_medicines,medicine_name,'.$id,
            'stock' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $med = MasterMedicine::findOrFail($id);

        $med->update([
            'medicine_name' => $request->medicine_name,
            'medicine_international_name' => $request->medicine_international_name,
            'price' => $request->price ?? 0,
            'discount' => $request->discount ?? 0,
            'discount_from_source' => $request->discount_from_source,
            'stock' => $request->stock ?? 0,
            'uom' => $request->uom ?? 'Tablet',
        ]);

        return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Fungsi Stock Opname (Penyesuaian Stok Langsung)
     */
    public function stockOpname(Request $request, $id)
    {
        $request->validate([
            'actual_stock' => 'required|numeric|min:0',
        ]);

        $med = MasterMedicine::findOrFail($id);
        $oldStock = $med->stock;
        $med->update(['stock' => $request->actual_stock]);

        return redirect()->route('medicines.index')
            ->with('success', "Stock Opname berhasil: {$med->medicine_name} diupdate dari {$oldStock} ke {$med->stock} {$med->uom}");
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
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $med = MasterMedicine::findOrFail($id);
        $med->delete();

        return redirect()->route('medicines.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
