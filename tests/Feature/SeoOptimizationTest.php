<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoOptimizationTest extends TestCase
{
    /**
     * Memverifikasi Landing Page memiliki meta tags SEO, Open Graph, Twitter Cards, dan JSON-LD.
     */
    public function test_landing_page_has_complete_seo_tags(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Primary Meta Tags
        $response->assertSee('<meta name="title"', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="keywords"', false);
        $response->assertSee('<meta name="robots" content="index, follow', false);
        $response->assertSee('<link rel="canonical" href="' . url('/') . '">', false);
        $response->assertSee('<meta name="theme-color" content="#2563eb">', false);

        // Open Graph
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:site_name" content="Smart Clinic Therapy">', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:image"', false);

        // Twitter Card
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('<meta name="twitter:title"', false);
        $response->assertSee('<meta name="twitter:description"', false);

        // JSON-LD Structured Data
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type": "SoftwareApplication"', false);
        $response->assertSee('"@type": "MedicalBusiness"', false);
        $response->assertSee('"@type": "FAQPage"', false);
        $response->assertSee('Apakah data rekam medis pasien di klinik kami aman dan terisolasi?', false);
    }

    /**
     * Memverifikasi Halaman Pendaftaran Klinik memiliki meta SEO dan canonical link.
     */
    public function test_register_clinic_page_has_seo_tags(): void
    {
        $response = $this->get('/register-clinic');

        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<link rel="canonical" href="' . route('register.clinic') . '">', false);
        $response->assertSee('<meta property="og:site_name" content="Smart Clinic Therapy">', false);
    }

    /**
     * Memverifikasi endpoint /sitemap.xml menghasilkan XML yang valid dan memuat link publik.
     */
    public function test_dynamic_sitemap_is_accessible_and_valid(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false);
        $response->assertSee('<loc>' . url('/') . '</loc>', false);
        $response->assertSee('<loc>' . route('register.clinic') . '</loc>', false);
        $response->assertSee('<loc>' . route('login') . '</loc>', false);
        $response->assertSee('<changefreq>daily</changefreq>', false);
    }

    /**
     * Memverifikasi robots.txt mengizinkan halaman publik dan memproteksi rute privat/internal.
     */
    public function test_robots_txt_protects_internal_routes(): void
    {
        $robotsContent = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('User-agent: *', $robotsContent);
        $this->assertStringContainsString('Allow: /', $robotsContent);
        $this->assertStringContainsString('Allow: /register-clinic', $robotsContent);
        $this->assertStringContainsString('Disallow: /dashboard', $robotsContent);
        $this->assertStringContainsString('Disallow: /admin', $robotsContent);
        $this->assertStringContainsString('Disallow: /saas', $robotsContent);
        $this->assertStringContainsString('Disallow: /patients', $robotsContent);
        $this->assertStringContainsString('Disallow: /reservations', $robotsContent);
        $this->assertStringContainsString('Disallow: /therapy-sessions', $robotsContent);
        $this->assertStringContainsString('Sitemap: /sitemap.xml', $robotsContent);
    }

    /**
     * Memverifikasi halaman login memiliki canonical dan noindex directive.
     */
    public function test_login_page_has_crawler_directives(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
        $response->assertSee('<link rel="canonical"', false);
    }
}
