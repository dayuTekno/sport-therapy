<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $table = 'landing_page_settings';

    protected $guarded = [];

    protected $casts = [
        'content_json' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Dapatkan konten section berdasarkan key dengan fallback
     */
    public static function getSection(string $key, array $default = []): self
    {
        return self::firstOrCreate(
            ['section_key' => $key],
            [
                'title' => $default['title'] ?? null,
                'subtitle' => $default['subtitle'] ?? null,
                'content_json' => $default['content_json'] ?? null,
                'image_path' => $default['image_path'] ?? null,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }

    /**
     * Dapatkan jumlah hari masa uji coba gratis dari setting database
     */
    public static function getTrialDays(): int
    {
        $hero = self::where('section_key', 'hero')->first();
        $content = $hero->content_json ?? [];

        if (!empty($content['trial_days']) && is_numeric($content['trial_days'])) {
            return (int) $content['trial_days'];
        }

        // Cek fallback jika ada di cta_primary_text
        if (!empty($content['cta_primary_text']) && preg_match('/(\d+)\s*(?:hari|day)/i', $content['cta_primary_text'], $matches)) {
            return (int) $matches[1];
        }

        // Cek dari subscription_plans
        if (\Illuminate\Support\Facades\Schema::hasTable('subscription_plans') && \Illuminate\Support\Facades\Schema::hasColumn('subscription_plans', 'trial_days')) {
            $planDays = SubscriptionPlan::where('is_active', true)->whereNotNull('trial_days')->value('trial_days');
            if ($planDays) {
                return (int) $planDays;
            }
        }

        return 14;
    }
}
