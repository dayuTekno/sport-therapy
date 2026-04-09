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
        Schema::create('master_doctors', function (Blueprint $table) {
            $table->id();
            $table->string("doctor_code");
            $table->string("employee_code");
            $table->string('license_number')->unique();
            $table->string('full_name');
            $table->string('specialization')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_doctors');
    }
};
