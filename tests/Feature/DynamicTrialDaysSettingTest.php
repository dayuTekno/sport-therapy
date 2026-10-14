<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\LandingPageSetting;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DynamicTrialDaysSettingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Memverifikasi tombol navbar dan teks trial di landing page membaca setting jumlah hari dari database.
     */
    public function test_landing_page_displays_trial_days_from_database(): void
    {
        // Set setting trial_days di database menjadi 30 hari
        $hero = LandingPageSetting::getSection('hero');
        $content = $hero->content_json ?? [];
        $content['trial_days'] = 30;
        $content['cta_primary_text'] = 'Daftar Uji Coba Gratis (30 Hari)';
        $hero->update(['content_json' => $content]);

        $this->assertEquals(30, LandingPageSetting::getTrialDays());

        $response = $this->get('/');
        $response->assertStatus(200);

        // Navbar harus menampilkan "Coba Gratis 30 Hari"
        $response->assertSee('Coba Gratis 30 Hari', false);

        // Bagian pricing & kartu harga harus menampilkan 30 Hari
        $response->assertSee('30 Hari', false);

        // FAQ harus menampilkan 30 hari
        $response->assertSee('30 hari penuh', false);
    }

    /**
     * Memverifikasi ketika database disetting ke jumlah hari lain (misal 7 hari), tampilan otomatis berubah.
     */
    public function test_landing_page_updates_dynamically_when_database_setting_changes(): void
    {
        // Ubah setting trial_days di database menjadi 7 hari
        $hero = LandingPageSetting::getSection('hero');
        $content = $hero->content_json ?? [];
        $content['trial_days'] = 7;
        $content['cta_primary_text'] = 'Daftar Uji Coba Gratis (7 Hari)';
        $hero->update(['content_json' => $content]);

        $this->assertEquals(7, LandingPageSetting::getTrialDays());

        $response = $this->get('/');
        $response->assertStatus(200);

        // Navbar & tombol sekarang harus menampilkan 7 Hari
        $response->assertSee('Coba Gratis 7 Hari', false);
        $response->assertSee('7 hari penuh', false);
        $response->assertDontSee('Coba Gratis 14 Hari', false);
    }

    /**
     * Memverifikasi halaman register-clinic dan tombol submit menampilkan jumlah hari trial dinamis.
     */
    public function test_register_clinic_page_uses_dynamic_trial_days(): void
    {
        $hero = LandingPageSetting::getSection('hero');
        $content = $hero->content_json ?? [];
        $content['trial_days'] = 21;
        $hero->update(['content_json' => $content]);

        $response = $this->get('/register-clinic');
        $response->assertStatus(200);

        $response->assertSee('21 hari masa uji coba gratis', false);
        $response->assertSee('Mulai Uji Coba Gratis 21 Hari', false);
    }

    /**
     * Memverifikasi saat klinik baru mendaftar, trial_ends_at dihitung berdasarkan trial_days di database.
     */
    public function test_new_clinic_registration_calculates_trial_ends_at_from_database(): void
    {
        $hero = LandingPageSetting::getSection('hero');
        $content = $hero->content_json ?? [];
        $content['trial_days'] = 25;
        $hero->update(['content_json' => $content]);

        $plan = SubscriptionPlan::first();

        $response = $this->post(route('register.clinic.submit'), [
            'clinic_name' => 'Klinik Dinamis Test',
            'admin_name' => 'Dr. Budi',
            'email' => 'budi_dynamic_test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone_number' => '081299998888',
            'address' => 'Jl. Merdeka No 1',
            'subscription_plan_id' => $plan->id,
        ]);

        $response->assertRedirect(route('dashboard'));

        $clinic = Clinic::where('email', 'budi_dynamic_test@example.com')->first();
        $this->assertNotNull($clinic);
        $this->assertEquals('trial', $clinic->status);

        // Verifikasi durasi trial tepat 25 hari
        $daysDifference = (int) round(now()->diffInDays($clinic->trial_ends_at, false));
        $this->assertEquals(25, $daysDifference);
    }
}
