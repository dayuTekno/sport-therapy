<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;

class SaasCmsController extends Controller
{
    /**
     * Tampilan Menu Khusus Admin untuk Mengelola Bagian Luar Website (CMS Landing Page)
     */
    public function index()
    {
        $hero = LandingPageSetting::getSection('hero');
        $features = LandingPageSetting::getSection('features');
        $contact = LandingPageSetting::getSection('contact');

        return view('admin.saas.cms.index', compact('hero', 'features', 'contact'));
    }

    /**
     * Simpan Perubahan Hero Section
     */
    public function updateHero(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string',
            'badge_text' => 'nullable|string|max:100',
            'cta_primary_text' => 'required|string|max:100',
            'cta_secondary_text' => 'required|string|max:100',
        ]);

        $hero = LandingPageSetting::getSection('hero');
        $hero->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'content_json' => [
                'badge_text' => $request->badge_text,
                'cta_primary_text' => $request->cta_primary_text,
                'cta_primary_url' => '/register-clinic',
                'cta_secondary_text' => $request->cta_secondary_text,
                'cta_secondary_url' => '#pricing',
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
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string',
            'features' => 'required|array',
            'features.*.icon' => 'required|string',
            'features.*.title' => 'required|string',
            'features.*.description' => 'required|string',
        ]);

        $features = LandingPageSetting::getSection('features');
        $features->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
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
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $contact = LandingPageSetting::getSection('contact');
        $contact->update([
            'content_json' => [
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'hours' => $request->hours ?? 'Senin - Sabtu: 08.00 - 20.00 WIB',
            ],
        ]);

        return redirect()->back()->with('success', 'Informasi Kontak Landing Page berhasil diperbarui!');
    }
}
