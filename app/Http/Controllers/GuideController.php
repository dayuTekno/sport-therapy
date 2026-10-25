<?php

namespace App\Http\Controllers;

class GuideController extends Controller
{
    /**
     * Panduan penggunaan aplikasi sesuai alur layanan klinik (dengan screenshot)
     */
    public function index()
    {
        $isSaasAdmin = auth()->user()->isSaasAdmin();

        return view('admin.pages.guide.index', compact('isSaasAdmin'));
    }
}
