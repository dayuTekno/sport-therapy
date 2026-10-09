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
            $table->string('nik')->nullable()->change();
            $table->integer('age')->nullable()->after('full_name');
            $table->string('occupation')->nullable()->after('gender');
            $table->index('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_patients', function (Blueprint $table) {
            $table->dropIndex(['phone_number']);
            $table->dropColumn(['age', 'occupation']);
        });
    }
};
