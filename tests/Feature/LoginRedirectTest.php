<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    public function test_superadmin_login_redirects_to_saas_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'saas_admin', 'guard_name' => 'web']);

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin.test@example.com'],
            [
                'name' => 'Superadmin Test',
                'password' => Hash::make('password123'),
                'clinic_id' => null,
            ]
        );
        $superadmin->syncRoles(['saas_admin']);

        $response = $this->post('/login', [
            'email' => 'superadmin.test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/saas/dashboard');
    }

    public function test_clinic_admin_login_redirects_to_admin_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $clinic = \App\Models\Clinic::first() ?? \App\Models\Clinic::create([
            'clinic_code' => 'CLN-TEST-001',
            'name' => 'Test Clinic',
            'slug' => 'test-clinic',
            'subdomain' => 'testclinic',
            'phone_number' => '08123456789',
            'email' => 'testclinic@example.com',
            'address' => 'Jl. Test No. 1',
            'status' => 'active',
            'is_active' => true,
        ]);

        $clinicAdmin = User::firstOrCreate(
            ['email' => 'clinicadmin.test@example.com'],
            [
                'name' => 'Clinic Admin Test',
                'password' => Hash::make('password123'),
                'clinic_id' => $clinic->id,
            ]
        );
        $clinicAdmin->syncRoles(['admin']);

        $response = $this->post('/login', [
            'email' => 'clinicadmin.test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_home_route_redirects_superadmin_to_saas_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'saas_admin', 'guard_name' => 'web']);

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin.test@example.com'],
            [
                'name' => 'Superadmin Test',
                'password' => Hash::make('password123'),
                'clinic_id' => null,
            ]
        );
        $superadmin->syncRoles(['saas_admin']);

        $response = $this->actingAs($superadmin)->get('/home');
        $response->assertRedirect(route('saas.dashboard'));
    }

    protected function tearDown(): void
    {
        User::whereIn('email', ['superadmin.test@example.com', 'clinicadmin.test@example.com'])->delete();
        parent::tearDown();
    }
}
