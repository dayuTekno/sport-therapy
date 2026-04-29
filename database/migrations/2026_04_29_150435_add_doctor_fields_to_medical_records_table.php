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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('doctor_name')->nullable()->after('icd9_id');
            $table->text('doctor_diagnosis')->nullable()->after('doctor_name');
            $table->text('doctor_notes')->nullable()->after('doctor_diagnosis');
            $table->foreignId('doctor_icd10_id')->nullable()->constrained('master_icds')->nullOnDelete()->after('doctor_notes');
            $table->foreignId('doctor_icd9_id')->nullable()->constrained('master_icds')->nullOnDelete()->after('doctor_icd10_id');
            $table->text('treatment')->nullable()->after('doctor_icd9_id');
            $table->text('prescription')->nullable()->after('treatment');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['doctor_icd10_id']);
            $table->dropForeign(['doctor_icd9_id']);
            $table->dropColumn([
                'doctor_name', 'doctor_diagnosis', 'doctor_notes',
                'doctor_icd10_id', 'doctor_icd9_id', 'treatment', 'prescription'
            ]);
        });
    }
};
