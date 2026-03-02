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
        Schema::create('master_icds', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['9', '10']);
            $table->string("icd_code");
            $table->text("name");
            $table->string("version");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_icds');
    }
};
