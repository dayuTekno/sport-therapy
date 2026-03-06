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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->integer('queue_number');

            $table->foreignId('patient_id')
                ->constrained('master_patients')
                ->cascadeOnDelete();

            $table->foreignId('eselon_id')
                ->nullable()
                ->constrained('master_eselons')
                ->cascadeOnDelete();

            $table->foreignId('poly_id')
                ->nullable()
                ->constrained('master_polyclinics')
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained('master_doctors')
                ->cascadeOnDelete();

            $table->enum("status", [
                "waiting",
                "called",
                "nurse_exam",
                "waiting_doctor",
                "doctor_exam",
                "pharmacy",
                "payment",
                "done",
                "cancelled"
            ])->default("waiting");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
