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
        Schema::table('reservations', function (Blueprint $table) {
            $table->dateTime('preferred_datetime')->nullable()->after('preferred_schedule');
            $table->index(['therapist_id', 'preferred_datetime']);
        });

        // Backfill preferred_datetime for records that have confirmed_schedule
        \DB::table('reservations')
            ->whereNull('preferred_datetime')
            ->whereNotNull('confirmed_schedule')
            ->update([
                'preferred_datetime' => \DB::raw('confirmed_schedule')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['therapist_id', 'preferred_datetime']);
            $table->dropColumn('preferred_datetime');
        });
    }
};
