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
        Schema::create('master_doctor_polyclinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')
                  ->references('id')
                  ->on('master_doctors')
                  ->cascadeOnDelete();
            $table->foreignId('polyclinic_id')
                  ->references('id')
                  ->on('master_polyclinics')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_doctor_polyclinics');
    }
};
