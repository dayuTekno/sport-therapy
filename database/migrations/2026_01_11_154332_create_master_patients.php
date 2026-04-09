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
        Schema::create('master_patients', function (Blueprint $table) {
            $table->id();
            $table->string("patient_code");
            $table->string("nik");
            $table->string("full_name");
            $table->date("date_of_birth")->nullable();
            $table->enum("gender", ["male", "female"])->default("male");
            $table->string('phone_number', 20)->change();
            $table->string("address")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_patients');
    }
};
