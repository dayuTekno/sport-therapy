<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Non-destructive: menambahkan kolom pengaturan surat keterangan sakit (tanda tangan, cap, nama tertanda, kota)
     * tanpa menghapus/mengubah data existing di production.
     */
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('logo_path');
            }
            if (!Schema::hasColumn('clinics', 'stamp_path')) {
                $table->string('stamp_path')->nullable()->after('signature_path');
            }
            if (!Schema::hasColumn('clinics', 'signer_name')) {
                $table->string('signer_name')->nullable()->after('stamp_path');
            }
            if (!Schema::hasColumn('clinics', 'signer_title')) {
                $table->string('signer_title')->nullable()->default('Sport Therapis Pemeriksa')->after('signer_name');
            }
            if (!Schema::hasColumn('clinics', 'city')) {
                $table->string('city')->nullable()->default('Indramayu')->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $columns = ['signature_path', 'stamp_path', 'signer_name', 'signer_title', 'city'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('clinics', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
