<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionExpiredController extends Controller
{
    /**
     * Tampilan layar kunci pemberitahuan masa langganan klinik telah berakhir
     */
    public function index()
    {
        $user = auth()->user();
        $clinic = $user?->clinic;

        // Jika langganan klinik masih aktif dan user tidak expired, redirect ke dashboard
        if ($clinic && !$clinic->isSubscriptionExpired()) {
            return redirect()->route('dashboard');
        }

        return view('auth.subscription-expired', compact('clinic', 'user'));
    }
}
