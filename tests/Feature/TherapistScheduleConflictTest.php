<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\MasterPatients;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapySession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TherapistScheduleConflictTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $therapist1;
    protected $therapist2;
    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->therapist1 = Therapist::first();
        $clinicId = $this->therapist1 ? ($this->therapist1->clinic_id ?: 1) : 1;

        if (!$this->therapist1) {
            $this->therapist1 = Therapist::create([
                'therapist_code' => 'TRP-TEST-1',
                'full_name' => 'Terapis Test A',
                'specialization' => 'Sport Physiotherapy',
                'is_active' => true,
                'clinic_id' => $clinicId,
            ]);
        }

        $this->therapist2 = Therapist::where('id', '!=', $this->therapist1->id)->first();
        if (!$this->therapist2) {
            $this->therapist2 = Therapist::create([
                'therapist_code' => 'TRP-TEST-2',
                'full_name' => 'Terapis Test B',
                'specialization' => 'Injury Rehabilitation',
                'is_active' => true,
                'clinic_id' => $clinicId,
            ]);
        }

        $this->user = User::factory()->create([
            'clinic_id' => $clinicId,
        ]);

        $this->patient = MasterPatients::where('clinic_id', $clinicId)->first() ?: MasterPatients::create([
            'patient_code' => 'PAT-TEST-01',
            'full_name' => 'Pasien Uji Coba',
            'phone_number' => '081234567890',
            'gender' => 'male',
            'age' => 25,
            'clinic_id' => $clinicId,
        ]);
    }

    public function test_same_therapist_at_same_time_is_rejected_on_reservation_creation()
    {
        $this->actingAs($this->user);

        $targetDate = '2026-11-28';
        $targetTime = '09:00';

        // 1. Reservasi Pertama berhasil
        $res1 = $this->post(route('reservations.store'), [
            'patient_id' => $this->patient->id,
            'phone_number' => $this->patient->phone_number,
            'chief_complaint' => 'Nyeri ligamen lutut',
            'complaint_duration' => '4 hari',
            'preferred_date' => $targetDate,
            'preferred_time' => $targetTime,
            'therapist_id' => $this->therapist1->id,
        ]);

        $res1->assertSessionHasNoErrors();
        $res1->assertStatus(302);

        // 2. Request kedua dengan Terapis yang sama di Jam yang sama: HARUS TERTOLAK!
        $res2 = $this->post(route('reservations.store'), [
            'patient_id' => $this->patient->id,
            'phone_number' => $this->patient->phone_number,
            'chief_complaint' => 'Nyeri otot betis',
            'complaint_duration' => '2 hari',
            'preferred_date' => $targetDate,
            'preferred_time' => $targetTime,
            'therapist_id' => $this->therapist1->id,
        ]);

        // Verifikasi tertolak dan ada error validasi therapist_id
        $res2->assertSessionHasErrors(['therapist_id']);
        $this->assertTrue(session()->has('error'));
        $this->assertStringContainsString('sudah memiliki jadwal', session('error'));

        // 3. Request ketiga dengan Terapis yang BERBEDA di Jam yang sama: HARUS BERHASIL
        $res3 = $this->post(route('reservations.store'), [
            'patient_id' => $this->patient->id,
            'phone_number' => $this->patient->phone_number,
            'chief_complaint' => 'Nyeri bahu',
            'complaint_duration' => '1 minggu',
            'preferred_date' => $targetDate,
            'preferred_time' => $targetTime,
            'therapist_id' => $this->therapist2->id,
        ]);

        $res3->assertSessionHasNoErrors();
        $res3->assertStatus(302);
    }

    public function test_availability_api_detects_conflict()
    {
        $this->actingAs($this->user);

        $targetDate = '2026-11-29';
        $targetTime = '10:00';

        // Buat reservasi aktif
        $reservation = Reservation::create([
            'reservation_code' => 'RSV-CONF-TEST-99',
            'patient_id' => $this->patient->id,
            'therapist_id' => $this->therapist1->id,
            'chief_complaint' => 'Nyeri pergelangan kaki',
            'complaint_duration' => '3 hari',
            'preferred_schedule' => "{$targetDate} {$targetTime}:00",
            'preferred_datetime' => "{$targetDate} {$targetTime}:00",
            'status' => 'confirmed',
        ]);

        // Cek API pada jam 10:00 -> available harus false
        $response1 = $this->getJson(route('api.therapists.check-availability', [
            'therapist_id' => $this->therapist1->id,
            'date' => $targetDate,
            'time' => $targetTime,
        ]));

        $response1->assertStatus(200);
        $response1->assertJson([
            'available' => false,
        ]);
        $this->assertNotNull($response1->json('conflict'));

        // Cek API pada jam 11:00 -> available harus true
        $response2 = $this->getJson(route('api.therapists.check-availability', [
            'therapist_id' => $this->therapist1->id,
            'date' => $targetDate,
            'time' => '11:00',
        ]));

        $response2->assertStatus(200);
        $response2->assertJson([
            'available' => true,
        ]);
    }

    public function test_therapy_session_store_rejects_conflicting_therapist_schedule()
    {
        $this->actingAs($this->user);

        $scheduledAt = '2026-11-30 14:00:00';

        // Sesi pertama berhasil
        $sess1 = $this->post(route('therapy-sessions.store'), [
            'patient_id' => $this->patient->id,
            'therapist_id' => $this->therapist1->id,
            'therapy_type_id' => 1,
            'stage_number' => 1,
            'stage_name' => 'Terapi A',
            'scheduled_at' => $scheduledAt,
        ]);

        $sess1->assertSessionHasNoErrors();

        // Sesi kedua pada waktu yang sama untuk terapis yang sama: HARUS TERTOLAK!
        $sess2 = $this->post(route('therapy-sessions.store'), [
            'patient_id' => $this->patient->id,
            'therapist_id' => $this->therapist1->id,
            'therapy_type_id' => 1,
            'stage_number' => 2,
            'stage_name' => 'Terapi B',
            'scheduled_at' => $scheduledAt,
        ]);

        $sess2->assertSessionHasErrors(['therapist_id']);
        $this->assertTrue(session()->has('error'));
    }
}
