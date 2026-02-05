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
        Schema::create('perizinans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
            $table->string("nomor_surat");
            $table->string("judul_perizinan");
            $table->foreignId('diapprove_oleh')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
            $table->enum("status", ["waiting", "approved", "rejected"]);
            $table->date("tgl_approval");
            $table->enum("read_st", [0,1])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};
