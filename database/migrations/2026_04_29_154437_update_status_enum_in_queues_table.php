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
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE queues DROP CONSTRAINT IF EXISTS queues_status_check");
            DB::statement("ALTER TABLE queues ADD CONSTRAINT queues_status_check CHECK (status::text IN ('waiting', 'called', 'nurse_exam', 'waiting_doctor', 'doctor_exam', 'pharmacy', 'payment', 'done', 'completed', 'cancelled', 'skipped'))");
        } else {
            Schema::table('queues', function (Blueprint $table) {
                $table->enum('status', [
                    'waiting',
                    'called',
                    'nurse_exam',
                    'waiting_doctor',
                    'doctor_exam',
                    'pharmacy',
                    'payment',
                    'done',
                    'completed',
                    'cancelled',
                    'skipped'
                ])->default('waiting')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            //
        });
    }
};
