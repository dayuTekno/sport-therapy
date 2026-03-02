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
        Schema::create('master_procedure_polyclinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polyclinic_id')
                  ->references('id')
                  ->on('master_polyclinics')
                  ->cascadeOnDelete();
            $table->foreignId('procedure_id')
                  ->references('id')
                  ->on('master_procedures')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_procedure_polyclinics');
    }
};
