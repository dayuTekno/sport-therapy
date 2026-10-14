<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicSubscriptionActive
{
    /**
     * Handle an incoming request.
     * Memastikan seluruh menu tidak bisa dibuka jika masa langganan (subscribe) klinik sudah habis.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jika belum login, biarkan proses auth berjalan normal
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // 2. SaaS Admin global tidak boleh terkunci agar tetap bisa memperpanjang langganan
        if ($user->isSaasAdmin()) {
            return $next($request);
        }

        // 3. Cek klinik yang dimiliki oleh user
        $clinic = $user->clinic;

        // Jika user tidak terikat klinik apapun, biarkan melewati middleware
        if (!$clinic) {
            return $next($request);
        }

        // 4. Periksa apakah masa langganan klinik telah habis
        if ($clinic->isSubscriptionExpired()) {
            // Rute yang diizinkan saat langganan habis (Logout dan halaman pemberitahuan expired)
            if ($request->routeIs('subscription.expired') || 
                $request->routeIs('logout') || 
                $request->is('logout')) {
                return $next($request);
            }

            // Jika request berupa API / AJAX
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'SUBSCRIPTION_EXPIRED',
                    'message' => 'Masa langganan klinik Anda telah berakhir. Seluruh menu tidak dapat diakses.',
                ], 403);
            }

            // Redirect ke halaman pemberitahuan langganan habis
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
