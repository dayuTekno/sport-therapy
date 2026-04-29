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
        Schema::table('master_doctor_schedules', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained('master_doctors')->cascadeOnDelete();
            $table->foreignId('poly_id')->nullable()->constrained('master_polyclinics')->cascadeOnDelete();
            $table->tinyInteger('day_of_week')->nullable()->comment('0=Sunday to 6=Saturday');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('quota')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_doctor_schedules', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['poly_id']);
            $table->dropColumn(['doctor_id', 'poly_id', 'day_of_week', 'start_time', 'end_time', 'quota', 'is_active']);
        });
    }
};
