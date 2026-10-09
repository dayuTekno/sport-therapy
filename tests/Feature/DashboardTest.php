<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Clinic;
use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_renders_successfully_with_antrian_model()
    {
        $clinic = Clinic::firstOrCreate(
            ['subdomain' => 'test-rstbdi'],
            [
                'clinic_code' => 'CLN-TEST-99',
                'name' => 'Klinik RSTBDI Test',
                'slug' => 'klinik-rstbdi-test',
                'phone_number' => '08123456789',
                'address' => 'Jl. Kesehatan No. 1',
                'is_active' => true,
            ]
        );

        $user = User::factory()->create([
            'clinic_id' => $clinic->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);

        // Pastikan Antrian dapat diakses tanpa query error
        $count = Antrian::count();
        $this->assertIsInt($count);
    }
}
