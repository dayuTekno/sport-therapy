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
        Schema::create('data_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
            // $table->string("nomor_surat");
            $table->string("kota_perusahaann");
            $table->string("nama_perusahaan");
            $table->string("bidang_usaha");
            $table->string("alamat");
            $table->string("no_telp_kantor");
            $table->string("akte_pendirian");
            $table->string("npwp");
            $table->integer("tenaga_kerja");
            $table->integer("jml_kapal");
            $table->string("kantor_cabang");
            $table->string("penanggungjawab");
            $table->string("jabatan_penanggungjawab");
            $table->string("alamat_rumah_penanggungjawab");
            $table->string("no_telp_penanggungjawab");
            $table->double("modal");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_registers');
    }
};
