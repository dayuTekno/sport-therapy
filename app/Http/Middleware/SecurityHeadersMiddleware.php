<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     * Mengamankan web app ketika diakses dari Google Chrome mobile / smartphone.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Mencegah Clickjacking dan embedding iframe dari webview/aplikasi luar (Mobile Protection)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Mencegah MIME Sniffing pada Chrome Mobile
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. XSS Filter protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Mencegah kebocoran URL referrer / query params sensitif
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Batasi akses hardware smartphone (Kamera, Mic, Geolocation) jika tidak diizinkan
        $response->headers->set(
            'Permissions-Policy',
            'camera=(self), microphone=(), geolocation=(), payment=(), usb=()'
        );

        // 6. HSTS (Enforce HTTPS) jika diakses via HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 7. Keamanan Data Medis pada Chrome Mobile (Mencegah caching data rekam medis saat dibuka di HP)
        // Jika user telah login, larang cache browser agar riwayat rekam medis tidak tersimpan di riwayat browser HP
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
