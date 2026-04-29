<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use App\Models\MasterPolyclinic;
use App\Models\MasterEselon;
use App\Models\Antrian;
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
        }

        // Jika ditemukan, arahkan ke form pemilihan poli
        return redirect()->route('registrasi.queue', $pasien->id)
            ->with('success', 'Data pasien ditemukan. Silakan pilih tujuan poliklinik.');
    }

    public function create(Request $request)
    {
        $nik = $request->nik;
        $eselons = MasterEselon::where('is_active', true)->get();

        return view('admin.pages.registrasi.inputform', compact('nik', 'eselons'));
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
            'patient_code' => $uuid,
            'nik' => $request->nik,
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'eselon_id' => $request->eselon_id,
        ]);

        return redirect()->route('registrasi.queue', $patient->id)
            ->with('success', 'Data pasien berhasil disimpan. Silakan pilih tujuan poliklinik.');
    }

    public function queueForm($patient_id)
    {
        $patient = MasterPatients::findOrFail($patient_id);
        $polyclinics = MasterPolyclinic::all();
        $eselons = MasterEselon::where('is_active', true)->get();

        return view('admin.pages.registrasi.queue', compact('patient', 'polyclinics', 'eselons'));
    }

    public function queueStore(Request $request, $patient_id)
    {
        $request->validate([
            'eselon_id' => 'required|exists:master_eselons,id',
            'poly_id' => 'required|exists:master_polyclinics,id',
            'doctor_id' => 'nullable|exists:master_doctors,id',
        ]);

        $patient = MasterPatients::findOrFail($patient_id);

        $today = now()->format('Y-m-d');
        $lastQueue = Antrian::whereDate('created_at', $today)->orderBy('queue_number', 'desc')->first();
        $nextNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

        Antrian::create([
            'queue_number' => $nextNumber,
            'patient_id' => $patient->id,
            'eselon_id' => $request->eselon_id,
            'poly_id' => $request->poly_id,
            'doctor_id' => $request->doctor_id,
            'status' => 'waiting', // Menunggu amnesa oleh perawat
        ]);

        return redirect()->route('registrasi.index')
            ->with('success', 'Pasien berhasil didaftarkan ke antrian klinik.');
    }
}
