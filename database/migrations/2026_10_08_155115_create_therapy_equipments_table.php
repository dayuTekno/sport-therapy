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
        Schema::create('therapy_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_code')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->integer('stock')->default(0);
            $table->string('uom')->default('Unit');
            $table->enum('condition', ['baik', 'perlu_perbaikan', 'rusak'])->default('baik');
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_equipments');
    }
};
