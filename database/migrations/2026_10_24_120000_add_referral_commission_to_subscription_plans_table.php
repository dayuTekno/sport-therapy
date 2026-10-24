<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Non-destructive: komisi referal klinik kini diatur per paket langganan (rupiah / persen).
     */
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_plans', 'referral_commission_type')) {
                $table->string('referral_commission_type', 10)->default('rupiah')->after('price'); // rupiah | percent
            }
            if (!Schema::hasColumn('subscription_plans', 'referral_commission_value')) {
                $table->decimal('referral_commission_value', 15, 2)->default(0)->after('referral_commission_type');
            }
        });

        // Salin setting komisi global sebelumnya (jika pernah disimpan) sebagai nilai awal setiap paket
        $content = DB::table('landing_page_settings')->where('section_key', 'clinic_referral')->value('content_json');
        $content = json_decode($content ?? '', true) ?: [];

        if (isset($content['commission_type'], $content['commission_value'])) {
            DB::table('subscription_plans')->update([
                'referral_commission_type' => $content['commission_type'] === 'percent' ? 'percent' : 'rupiah',
                'referral_commission_value' => (float) $content['commission_value'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('subscription_plans', 'referral_commission_value')) {
                $columnsToDrop[] = 'referral_commission_value';
            }
            if (Schema::hasColumn('subscription_plans', 'referral_commission_type')) {
                $columnsToDrop[] = 'referral_commission_type';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
