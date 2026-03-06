<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterPatients;


class RegistrasiController extends Controller
{
    public function index()
    {
        return view('admin.pages.registrasi.index');
    }

    public function check(Request $request)
    {
        // 1️⃣ Validasi
        $validated = $request->validate([
            'nik' => 'required|digits:16'
        ]);

        // 2️⃣ Cek apakah pasien sudah ada
        $pasien = MasterPatients::where('nik', $validated['nik'])->first();

        // ❗ Jika NIK tidak ditemukan
        if (!$pasien) {
            return redirect()
                ->route('registrasi.create', ['nik' => $validated['nik']])
                ->with('warning', 'NIK belum terdaftar. Silakan lengkapi data pasien.');
        }else{

        }
        
       
        return redirect()
                ->route('antrian.show', $antrian->id)
                ->with('success', 'Nomor antrian berhasil dibuat.');
    }

        public function check(Request $request)
    {
        // 1️⃣ Validasi
        $validated = $request->validate([
            'nik' => 'required|digits:16'
        ]);

        // 2️⃣ Cek apakah pasien sudah ada
        $pasien = MasterPatients::where('nik', $validated['nik'])->first();

        // ❗ Jika NIK tidak ditemukan
        if (!$pasien) {
            return redirect()
                ->route('registrasi.create', ['nik' => $validated['nik']])
                ->with('warning', 'NIK belum terdaftar. Silakan lengkapi data pasien.');
        }else{

        }
        
       
        return redirect()
                ->route('antrian.show', $antrian->id)
                ->with('success', 'Nomor antrian berhasil dibuat.');
    }

    

    public function create(Request $request)
    {
        $nik = $request->nik;
        return view('admin.pages.registrasi.inputform', compact('nik'));
    }
}
