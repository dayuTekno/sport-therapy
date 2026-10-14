<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClinicSettingController extends Controller
{
    /**
     * Tampilkan form pengaturan profil klinik, logo, stempel cap, dan tanda tangan
     */
    public function index()
    {
        $clinic = $this->resolveActiveClinic();

        if (!$clinic) {
            return redirect()->route('dashboard')->with('error', 'Data klinik tidak ditemukan.');
        }

        return view('admin.pages.clinic-settings.index', compact('clinic'));
    }

    /**
     * Simpan pembaruan pengaturan profil klinik dan dokumen surat
     */
    public function update(Request $request)
    {
        $clinic = $this->resolveActiveClinic();

        if (!$clinic) {
            return redirect()->route('dashboard')->with('error', 'Data klinik tidak ditemukan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'signer_name' => 'nullable|string|max:255',
            'signer_title' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'stamp' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city ?: 'Indramayu',
            'signer_name' => $request->signer_name,
            'signer_title' => $request->signer_title ?: 'Sport Therapis Pemeriksa',
        ];

        // 1. Upload Logo Klinik
        if ($request->hasFile('logo')) {
            if ($clinic->logo_path && Storage::disk('public')->exists($clinic->logo_path)) {
                Storage::disk('public')->delete($clinic->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('clinic-assets/logos', 'public');
        }

        // 2. Upload Tanda Tangan
        if ($request->hasFile('signature')) {
            if ($clinic->signature_path && Storage::disk('public')->exists($clinic->signature_path)) {
                Storage::disk('public')->delete($clinic->signature_path);
            }
            $data['signature_path'] = $request->file('signature')->store('clinic-assets/signatures', 'public');
        }

        // 3. Upload Cap / Stempel Resmi
        if ($request->hasFile('stamp')) {
            if ($clinic->stamp_path && Storage::disk('public')->exists($clinic->stamp_path)) {
                Storage::disk('public')->delete($clinic->stamp_path);
            }
            $data['stamp_path'] = $request->file('stamp')->store('clinic-assets/stamps', 'public');
        }

        $clinic->update($data);

        return redirect()->route('clinic.settings')->with('success', 'Pengaturan klinik, logo, cap stempel, dan tanda tangan tertanda berhasil diperbarui!');
    }

    /**
     * Resolusi klinik aktif berdasarkan session impersonate atau user klinik
     */
    protected function resolveActiveClinic(): ?Clinic
    {
        if (session()->has('active_clinic_id')) {
            return Clinic::find(session('active_clinic_id'));
        }

        if (auth()->check() && auth()->user()->clinic) {
            return auth()->user()->clinic;
        }

        return Clinic::first();
    }
}
