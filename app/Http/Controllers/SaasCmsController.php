<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaasCmsController extends Controller
{
    /**
     * Tampilan Menu Khusus Admin untuk Mengelola Bagian Luar Website (CMS Landing Page)
     */
    public function index()
    {
        $hero     = LandingPageSetting::getSection('hero');
        $features = LandingPageSetting::getSection('features');
        $contact  = LandingPageSetting::getSection('contact');
        $branding = LandingPageSetting::getSection('branding', [
            'title'        => 'Sport Therapist',
            'subtitle'     => 'Platform Control Center',
            'content_json' => [
                'platform_name'    => 'Sport Therapist',
                'platform_tagline' => 'Platform Control Center',
                'logo_path'        => null,
            ],
        ]);

        return view('admin.saas.cms.index', compact('hero', 'features', 'contact', 'branding'));
    }

    /**
     * Simpan Perubahan Hero Section
     */
    public function updateHero(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'subtitle'           => 'required|string',
            'badge_text'         => 'nullable|string|max:100',
            'trial_days'         => 'nullable|integer|min:1|max:365',
            'cta_primary_text'   => 'required|string|max:100',
            'cta_secondary_text' => 'required|string|max:100',
        ]);

        $trialDays = $request->filled('trial_days') ? (int) $request->trial_days : LandingPageSetting::getTrialDays();

        $hero = LandingPageSetting::getSection('hero');
        $hero->update([
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'content_json' => [
                'badge_text'         => $request->badge_text,
                'trial_days'         => $trialDays,
                'cta_primary_text'   => $request->cta_primary_text,
                'cta_primary_url'    => '/register-clinic',
                'cta_secondary_text' => $request->cta_secondary_text,
                'cta_secondary_url'  => '#pricing',
            ],
        ]);

        return redirect()->back()->with('success', 'Konten Hero Banner Landing Page berhasil diperbarui!');
    }

    /**
     * Simpan Perubahan Fitur Unggulan
     */
    public function updateFeatures(Request $request)
    {
        $request->validate([
            'title'                   => 'required|string|max:255',
            'subtitle'                => 'required|string',
            'features'                => 'required|array',
            'features.*.icon'         => 'required|string',
            'features.*.title'        => 'required|string',
            'features.*.description'  => 'required|string',
        ]);

        $features = LandingPageSetting::getSection('features');
        $features->update([
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'content_json' => array_values($request->features),
        ]);

        return redirect()->back()->with('success', 'Daftar Fitur Unggulan Landing Page berhasil diperbarui!');
    }

    /**
     * Simpan Perubahan Kontak & Footer
     */
    public function updateContact(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'phone'   => 'required|string',
            'address' => 'required|string',
        ]);

        $contact = LandingPageSetting::getSection('contact');
        $contact->update([
            'content_json' => [
                'email'   => $request->email,
                'phone'   => $request->phone,
                'address' => $request->address,
                'hours'   => $request->hours ?? 'Senin - Sabtu: 08.00 - 20.00 WIB',
            ],
        ]);

        return redirect()->back()->with('success', 'Informasi Kontak Landing Page berhasil diperbarui!');
    }

    /**
     * Simpan Perubahan Branding Header Sidebar (Nama Platform & Logo)
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            'platform_name'    => 'required|string|max:100',
            'platform_tagline' => 'nullable|string|max:100',
            'logo'             => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'remove_logo'      => 'nullable|boolean',
        ]);

        $branding    = LandingPageSetting::getSection('branding');
        $contentJson = $branding->content_json ?? [];

        // Hapus logo jika diminta
        if ($request->boolean('remove_logo')) {
            if (!empty($contentJson['logo_path'])) {
                Storage::disk('public')->delete($contentJson['logo_path']);
            }
            $contentJson['logo_path'] = null;
        }

        // Upload logo baru jika ada
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            // Hapus logo lama
            if (!empty($contentJson['logo_path'])) {
                Storage::disk('public')->delete($contentJson['logo_path']);
            }

            $path = $request->file('logo')->store('branding', 'public');
            $contentJson['logo_path'] = $path;
        }

        $contentJson['platform_name']    = $request->platform_name;
        $contentJson['platform_tagline'] = $request->platform_tagline ?? 'Platform Control Center';

        $branding->update([
            'title'        => $request->platform_name,
            'subtitle'     => $request->platform_tagline,
            'content_json' => $contentJson,
        ]);

        return redirect()->back()->with('success', 'Branding Header Sidebar berhasil diperbarui!');
    }
}
