<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


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
            'nik' => 'required|digits:16',
        ]);

        // 2️⃣ Cek apakah pasien sudah ada
        $pasien = MasterPatients::where('nik', $validated['nik'])->first();

        // ❗ Jika NIK tidak ditemukan
        if (! $pasien) {
            return redirect()
                ->route('registrasi.create', ['nik' => $validated['nik']])
                ->with('warning', 'NIK belum terdaftar. Silakan lengkapi data pasien.');
        } else {

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

    public function store(Request $request)
    {

        // 1️⃣ Validasi
        $validated = $request->validate([
            'nik' => 'required|unique:master_patients,nik',
            'full_name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
        ]);

        $uuid = Str::uuid();

        $patient = MasterPatients::create([
            'clinic_id' => 1,
            'patient_code' => $uuid,
            'nik' => $request->nik,
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->back()->with('success', 'Data pasien berhasil disimpan');
    }
}
