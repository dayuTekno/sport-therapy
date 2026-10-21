<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\MasterPatients;
use App\Models\MasterReferral;
use App\Models\User;
use Tests\TestCase;

class MasterReferralTest extends TestCase
{
    protected $clinic;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::firstOrCreate(
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

        $this->user = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);
    }

    public function test_referral_crud_operations()
    {
        $this->actingAs($this->user);

        // 1. Index page
        $resIndex = $this->get(route('referrals.index'));
        $resIndex->assertStatus(200);

        // 2. Create page
        $resCreate = $this->get(route('referrals.create'));
        $resCreate->assertStatus(200);

        // 3. Store
        $resStore = $this->post(route('referrals.store'), [
            'name' => 'dr. Perujuk Test',
            'code' => 'REF-T01',
            'agency' => 'RSUD Test',
            'phone' => '081234567890',
            'email' => 'perujuk@test.com',
            'commission_amount' => 50000,
            'is_active' => '1',
        ]);
        $resStore->assertRedirect(route('referrals.index'));

        $referral = MasterReferral::where('code', 'REF-T01')->first();
        $this->assertNotNull($referral);
        $this->assertEquals('dr. Perujuk Test', $referral->name);
        $this->assertEquals(50000, (float) $referral->commission_amount);

        // 4. Show page
        $resShow = $this->get(route('referrals.show', $referral->id));
        $resShow->assertStatus(200);

        // 5. Update
        $resUpdate = $this->put(route('referrals.update', $referral->id), [
            'name' => 'dr. Perujuk Updated',
            'code' => 'REF-T01',
            'agency' => 'Klinik Test',
            'commission_amount' => 60000,
            'is_active' => '1',
        ]);
        $resUpdate->assertRedirect(route('referrals.index'));
        $this->assertEquals('dr. Perujuk Updated', $referral->fresh()->name);

        // 6. Delete
        $resDelete = $this->delete(route('referrals.destroy', $referral->id));
        $resDelete->assertRedirect(route('referrals.index'));
        $this->assertNull(MasterReferral::find($referral->id));
    }

    public function test_patient_association_with_referral_and_lookup()
    {
        $this->actingAs($this->user);

        $referral = MasterReferral::create([
            'clinic_id' => $this->clinic->id,
            'code' => 'REF-T02',
            'name' => 'Mitra Rujukan Sejahtera',
            'agency' => 'Klub Atletik',
            'commission_amount' => 30000,
            'is_active' => true,
        ]);

        $patient = MasterPatients::create([
            'clinic_id' => $this->clinic->id,
            'patient_code' => 'RM-TEST-001',
            'full_name' => 'Pasien Referal Test',
            'phone_number' => '08999888777',
            'age' => 25,
            'gender' => 'male',
            'occupation' => 'Atlet',
            'address' => 'Jl. Olahraga No. 10',
            'referral_id' => $referral->id,
        ]);

        $this->assertNotNull($patient->referral);
        $this->assertEquals('Mitra Rujukan Sejahtera', $patient->referral->name);

        // Lookup test by id
        $response = $this->getJson(route('api.patients.lookup', ['id' => $patient->id]));
        $response->assertStatus(200);
        $response->assertJson([
            'found' => true,
            'patient' => [
                'id' => $patient->id,
                'full_name' => 'Pasien Referal Test',
                'referral_id' => $referral->id,
                'referral_name' => 'Mitra Rujukan Sejahtera',
            ]
        ]);

        // Cleanup
        $patient->delete();
        $referral->delete();
    }

    public function test_reservation_show_displays_referral_name()
    {
        $this->actingAs($this->user);

        $referral = MasterReferral::create([
            'clinic_id' => $this->clinic->id,
            'code' => 'REF-T03',
            'name' => 'Klub Sepakbola Persik',
            'agency' => 'Official Club',
            'commission_amount' => 50000,
            'is_active' => true,
        ]);

        $patient = MasterPatients::create([
            'clinic_id' => $this->clinic->id,
            'patient_code' => 'RM-TEST-002',
            'full_name' => 'Bintang Lapangan',
            'phone_number' => '081122334455',
            'age' => 22,
            'gender' => 'male',
            'occupation' => 'Pemain Bola',
            'address' => 'Jl. Stadion No. 1',
            'referral_id' => $referral->id,
        ]);

        $reservation = \App\Models\Reservation::create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
            'reservation_code' => 'RES-TEST-REF-99',
            'chief_complaint' => 'Cedera Hamstring saat bertanding',
            'complaint_duration' => '3 hari',
            'preferred_schedule' => 'Besok Pagi Pukul 09.00',
            'status' => 'confirmed',
        ]);

        $response = $this->get(route('reservations.show', $reservation->id));
        $response->assertStatus(200);
        $response->assertSee('Klub Sepakbola Persik');
        $response->assertSee('Official Club');

        // Cleanup
        $reservation->delete();
        $patient->delete();
        $referral->delete();
    }
}
