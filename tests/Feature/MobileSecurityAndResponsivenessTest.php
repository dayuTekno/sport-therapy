<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MobileSecurityAndResponsivenessTest extends TestCase
{
    /**
     * Memverifikasi header keamanan web saat diakses dari Chrome Mobile / HP.
     */
    public function test_public_pages_have_mobile_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
    }

    /**
     * Memverifikasi halaman login memiliki meta viewport responsive dan atribut mobile-friendly.
     */
    public function test_login_page_is_responsive_and_secure(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('viewport', false);
        $response->assertSee('width=device-width', false);
        $response->assertSee('csrf-token', false);
        $response->assertSee('inputmode="email"', false);
        $response->assertSee('autocomplete="current-password"', false);
    }

    /**
     * Memverifikasi data sensitif klinik tidak disimpan di cache mobile browser saat user login.
     */
    public function test_authenticated_routes_prevent_mobile_cache(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }
}
