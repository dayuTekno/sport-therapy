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
        Schema::create('master_medicines', function (Blueprint $table) {
            $table->id();
            $table->string("medicine_code");
            $table->string("medicine_name");
            $table->string("medicine_international_name")->nullable();
            $table->decimal('price', 20, 2)->nullable();
            $table->decimal('discount_from_source', 20, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_medicines');
    }
};
