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
            $table->foreignId('referral_therapist_id')
                ->nullable()
                ->after('occupation')
                ->constrained('therapists')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_patients', function (Blueprint $table) {
            $table->dropForeign(['referral_therapist_id']);
            $table->dropColumn('referral_therapist_id');
        });
    }
};
