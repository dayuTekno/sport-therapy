<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code')->unique();
            $table->foreignId('patient_id')->constrained('master_patients')->cascadeOnDelete();
            $table->foreignId('therapist_id')->nullable()->constrained('master_doctors')->nullOnDelete();
            $table->text('chief_complaint'); // Keluhan Utama
            $table->string('complaint_duration'); // Sudah berapa lama keluhan dirasakan
            $table->text('medical_history')->nullable(); // Riwayat penyakit (jika ada)
            $table->string('preferred_schedule'); // Hari dan jam yang diinginkan untuk terapi
            $table->dateTime('confirmed_schedule')->nullable(); // Jadwal terkonfirmasi
            $table->enum('status', [
                'pending_confirmation', // Menunggu konfirmasi
                'confirmed',            // Terkonfirmasi
                'in_progress',          // Sedang terapi
                'completed',            // Selesai
                'cancelled'             // Batal
            ])->default('pending_confirmation');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
