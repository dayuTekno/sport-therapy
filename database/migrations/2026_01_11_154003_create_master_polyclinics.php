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
        Schema::create('master_polyclinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')
                  ->references('id')
                  ->on('master_clinics')
                  ->cascadeOnDelete();
            $table->string("polyclinic_code");
            $table->string("name");
            $table->timestamps();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_polyclinics');
    }
};
