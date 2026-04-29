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
        Schema::table('master_patients', function (Blueprint $table) {
            $table->foreignId('eselon_id')->nullable()->constrained('master_eselons')->nullOnDelete()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_patients', function (Blueprint $table) {
            $table->dropForeign(['eselon_id']);
            $table->dropColumn('eselon_id');
        });
    }
};
