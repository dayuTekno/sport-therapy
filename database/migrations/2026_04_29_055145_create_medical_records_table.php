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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('master_patients')->cascadeOnDelete();
            $table->integer('systolic')->nullable();
            $table->integer('diastolic')->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->text('symptoms')->nullable(); // Keluhan pasien
            $table->text('diagnosis')->nullable(); // Catatan diagnosa dari perawat
            $table->foreignId('icd_id')->nullable()->constrained('master_icds')->nullOnDelete(); // ICD pilihan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
