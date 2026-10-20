<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\MasterPatients;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapySession;
use App\Models\TherapyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierOtherFeeTest extends TestCase
{
    public function test_cashier_process_page_displays_other_fee_input(): void
    {
        $clinic = Clinic::first() ?? Clinic::factory()->create();
        $user = User::where('clinic_id', $clinic->id)->first() ?? User::first();
        $patient = MasterPatients::first() ?? MasterPatients::create([
            'name' => 'Pasien Test Kasir',
            'medical_record_number' => 'RM-99999',
            'clinic_id' => $clinic->id,
        ]);

        $reservation = Reservation::create([
            'clinic_id' => $clinic->id,
            'reservation_code' => Reservation::generateReservationCode(),
            'patient_id' => $patient->id,
            'chief_complaint' => 'Sakit lutut kiri saat lari',
            'complaint_duration' => '1 minggu',
            'preferred_schedule' => 'Senin 10:00 WIB',
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_price' => 150000,
        ]);

        $response = $this->actingAs($user)->get(route('cashier.process', $reservation->id));

        $response->assertStatus(200);
        $response->assertSee('Biaya Lain-lain (Di Luar Penanganan)');
        $response->assertSee('name="other_fee"', false);
        $response->assertSee('name="other_fee_description"', false);
    }

    public function test_cashier_store_saves_other_fee_and_calculates_total_correctly(): void
    {
        $clinic = Clinic::first() ?? Clinic::factory()->create();
        $user = User::where('clinic_id', $clinic->id)->first() ?? User::first();
        $patient = MasterPatients::first() ?? MasterPatients::create([
            'name' => 'Pasien Test Kasir 2',
            'medical_record_number' => 'RM-99998',
            'clinic_id' => $clinic->id,
        ]);

        $reservation = Reservation::create([
            'clinic_id' => $clinic->id,
            'reservation_code' => Reservation::generateReservationCode(),
            'patient_id' => $patient->id,
            'chief_complaint' => 'Nyeri engkel',
            'complaint_duration' => '3 hari',
            'preferred_schedule' => 'Selasa 14:00 WIB',
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_price' => 200000,
        ]);

        // Base 200.000 + Other Fee 35.000 - Discount 10.000 = 225.000
        $payload = [
            'other_fee' => 35000,
            'other_fee_description' => 'Kinesio Tape 1 Roll & Kasa Steril',
            'discount' => 10000,
            'discount_type' => 'rupiah',
            'discount_value' => 10000,
            'paid_amount' => 250000,
            'payment_method' => 'Cash',
            'cashier_notes' => 'Pembayaran tunai di loket kasir',
        ];

        $response = $this->actingAs($user)->post(route('cashier.store', $reservation->id), $payload);

        $response->assertRedirect(route('cashier.receipt', $reservation->id));

        $reservation->refresh();
        $this->assertEquals('paid', $reservation->payment_status);
        $this->assertEquals(200000, (float)$reservation->total_price);
        $this->assertEquals(35000, (float)$reservation->other_fee);
        $this->assertEquals('Kinesio Tape 1 Roll & Kasa Steril', $reservation->other_fee_description);
        $this->assertEquals(10000, (float)$reservation->discount);
        $this->assertEquals(250000, (float)$reservation->paid_amount);
        $this->assertEquals(225000, $reservation->final_total);

        // Check receipt page displays other fee
        $receiptResponse = $this->actingAs($user)->get(route('cashier.receipt', $reservation->id));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('Kinesio Tape 1 Roll & Kasa Steril');
        $receiptResponse->assertSee('Biaya Lain-lain');
    }

    public function test_cashier_index_paid_tab_displays_other_fee(): void
    {
        $clinic = Clinic::first() ?? Clinic::factory()->create();
        $user = User::where('clinic_id', $clinic->id)->first() ?? User::first();
        $patient = MasterPatients::first() ?? MasterPatients::create([
            'name' => 'Pasien Riwayat Kasir',
            'medical_record_number' => 'RM-99997',
            'clinic_id' => $clinic->id,
        ]);

        $reservation = Reservation::create([
            'clinic_id' => $clinic->id,
            'reservation_code' => Reservation::generateReservationCode(),
            'invoice_code' => Reservation::generateInvoiceCode(),
            'patient_id' => $patient->id,
            'chief_complaint' => 'Nyeri bahu',
            'complaint_duration' => '2 hari',
            'preferred_schedule' => 'Rabu 09:00 WIB',
            'status' => 'completed',
            'payment_status' => 'paid',
            'total_price' => 175000,
            'other_fee' => 25000,
            'other_fee_description' => 'Sewa Ice Pack',
            'discount' => 0,
            'paid_amount' => 200000,
            'payment_method' => 'QRIS',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('cashier.index', ['tab' => 'paid']));

        $response->assertStatus(200);
        $response->assertSee('Biaya Lain:');
        $response->assertSee('Sewa Ice Pack');
        $response->assertSee('25.000');
    }
}
