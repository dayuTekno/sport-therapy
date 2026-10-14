<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('subscription_plans') && !Schema::hasColumn('subscription_plans', 'trial_days')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->integer('trial_days')->nullable()->after('max_patients');
            });
        }

        // Sinkronkan trial_days ke landing_page_settings hero section jika belum ada
        $heroSetting = DB::table('landing_page_settings')->where('section_key', 'hero')->first();
        if ($heroSetting) {
            $content = json_decode($heroSetting->content_json ?? '{}', true) ?: [];
            if (!isset($content['trial_days'])) {
                // Deteksi angka dari cta_primary_text jika ada (misal: "30 Hari" -> 30)
                $detected = 14;
                if (!empty($content['cta_primary_text']) && preg_match('/(\d+)\s*(?:hari|day)/i', $content['cta_primary_text'], $m)) {
                    $detected = (int) $m[1];
                }
                $content['trial_days'] = $detected;
                DB::table('landing_page_settings')->where('id', $heroSetting->id)->update([
                    'content_json' => json_encode($content),
                ]);

                // Update juga subscription_plans trial_days dengan detected value
                DB::table('subscription_plans')->update(['trial_days' => $detected]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('subscription_plans') && Schema::hasColumn('subscription_plans', 'trial_days')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->dropColumn('trial_days');
            });
        }
    }
};
