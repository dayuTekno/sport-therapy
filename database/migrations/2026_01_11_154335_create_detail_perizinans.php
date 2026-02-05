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
        Schema::create('detail_perizinans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')
                  ->references('id')
                  ->on('perizinans')
                  ->cascadeOnDelete();
            $table->string("nama_perizinan");
            $table->foreignId('diapprove_oleh')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
            $table->enum("status", ["waiting", "approved", "rejected"]);
            $table->date("tgl_approval");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_perizinans');
    }
};
