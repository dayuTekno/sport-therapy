<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClinicSubscriptionExpirationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Role::where('name', 'clinic_admin')->exists()) {
            Role::create(['name' => 'clinic_admin']);
        }
        if (!Role::where('name', 'saas_admin')->exists()) {
            Role::create(['name' => 'saas_admin']);
        }
    }

    public function test_active_clinic_user_can_access_dashboard()
    {
        $clinic = Clinic::firstOrCreate(
            ['clinic_code' => 'CLN-TEST-ACTIVE'],
            [
                'name' => 'Klinik Aktif Test',
                'slug' => 'klinik-aktif-test',
                'status' => 'active',
                'subscription_ends_at' => now()->addDays(30),
                'is_active' => true,
            ]
        );
        $clinic->update([
            'status' => 'active',
            'subscription_ends_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        $user = User::firstOrCreate(
            ['email' => 'active.user@klinik.com'],
            [
                'name' => 'Active User',
                'password' => bcrypt('password'),
                'clinic_id' => $clinic->id,
            ]
        );
        $user->clinic_id = $clinic->id;
        $user->save();
        $user->syncRoles(['clinic_admin']);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_expired_clinic_user_is_blocked_and_redirected_to_subscription_expired()
    {
        $clinic = Clinic::firstOrCreate(
            ['clinic_code' => 'CLN-TEST-EXPIRED'],
            [
                'name' => 'Klinik Expired Test',
                'slug' => 'klinik-expired-test',
                'status' => 'active',
                'subscription_ends_at' => now()->subDay(),
                'is_active' => true,
            ]
        );
        $clinic->update([
            'status' => 'active',
            'subscription_ends_at' => now()->subDay(),
            'is_active' => true,
        ]);

        $user = User::firstOrCreate(
            ['email' => 'expired.user@klinik.com'],
            [
                'name' => 'Expired User',
                'password' => bcrypt('password'),
                'clinic_id' => $clinic->id,
            ]
        );
        $user->clinic_id = $clinic->id;
        $user->save();
        $user->syncRoles(['clinic_admin']);

        // Mencoba akses dashboard -> diblokir
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('subscription.expired'));

        // Mencoba akses master pasien -> diblokir
        $responsePatients = $this->actingAs($user)->get(route('patients.index'));
        $responsePatients->assertRedirect(route('subscription.expired'));

        // Halaman subscription-expired bisa dibuka dan menampilkan informasi terkunci
        $responseExpiredPage = $this->actingAs($user)->get(route('subscription.expired'));
        $responseExpiredPage->assertStatus(200);
        $responseExpiredPage->assertSee('Masa Langganan Berakhir');
        $responseExpiredPage->assertSee('Akses Menu Terkunci');
        $responseExpiredPage->assertSee($clinic->name);
    }

    public function test_saas_admin_is_not_blocked_even_if_clinic_is_expired()
    {
        $saasAdmin = User::firstOrCreate(
            ['email' => 'superadmin@smartclinictherapy.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'clinic_id' => null,
            ]
        );
        $saasAdmin->syncRoles(['saas_admin']);

        $response = $this->actingAs($saasAdmin)->get(route('saas.dashboard'));
        $response->assertStatus(200);

        $responseClinics = $this->actingAs($saasAdmin)->get(route('saas.clinics.index'));
        $responseClinics->assertStatus(200);
    }
}
