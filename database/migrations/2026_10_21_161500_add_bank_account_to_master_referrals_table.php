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
        Schema::table('master_referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('master_referrals', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('agency');
            }
            if (!Schema::hasColumn('master_referrals', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('master_referrals', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_account_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_referrals', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('master_referrals', 'bank_account_name')) {
                $columnsToDrop[] = 'bank_account_name';
            }
            if (Schema::hasColumn('master_referrals', 'bank_account_number')) {
                $columnsToDrop[] = 'bank_account_number';
            }
            if (Schema::hasColumn('master_referrals', 'bank_name')) {
                $columnsToDrop[] = 'bank_name';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
