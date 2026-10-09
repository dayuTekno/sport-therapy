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
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
        });

        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id')->nullable()->change();
            $table->foreign('reservation_id')->references('id')->on('reservations')->nullOnDelete();
            $table->integer('daily_session_order')->default(1)->after('stage_number');
            $table->text('actions_taken')->nullable()->after('evaluation_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropColumn(['daily_session_order', 'actions_taken']);
        });

        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id')->nullable(false)->change();
            $table->foreign('reservation_id')->references('id')->on('reservations')->cascadeOnDelete();
        });
    }
};
