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
        Schema::table('queues', function (Blueprint $table) {
            $table->string('patient_category')->nullable()->after('patient_id');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('nurse_name')->nullable()->after('patient_id');
            $table->renameColumn('icd_id', 'icd10_id');
            $table->foreignId('icd9_id')->nullable()->constrained('master_icds')->nullOnDelete()->after('icd10_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('patient_category');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('nurse_name');
            $table->dropForeign(['icd9_id']);
            $table->dropColumn('icd9_id');
            $table->renameColumn('icd10_id', 'icd_id');
        });
    }
};
