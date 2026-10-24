<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicReferralCommission extends Model
{
    protected $table = 'clinic_referral_commissions';

    protected $guarded = [];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public const SETTING_KEY = 'clinic_referral';

    /**
     * Klinik pemilik kode referal (penerima komisi)
     */
    public function referrer()
    {
        return $this->belongsTo(Clinic::class, 'referrer_clinic_id');
    }

    /**
     * Klinik baru yang mendaftar menggunakan kode referal
     */
    public function referred()
    {
        return $this->belongsTo(Clinic::class, 'referred_clinic_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Setting program referal klinik (disimpan di landing_page_settings, section 'clinic_referral')
     */
    public static function settings(): array
    {
        $content = LandingPageSetting::where('section_key', self::SETTING_KEY)->value('content_json');
        $content = is_array($content) ? $content : (json_decode($content ?? '', true) ?: []);

        return [
            'is_enabled' => (bool) ($content['is_enabled'] ?? true),
            'commission_type' => in_array($content['commission_type'] ?? null, ['rupiah', 'percent']) ? $content['commission_type'] : 'percent',
            'commission_value' => (float) ($content['commission_value'] ?? 10),
        ];
    }

    public static function saveSettings(array $settings): void
    {
        $setting = LandingPageSetting::getSection(self::SETTING_KEY, ['title' => 'Program Referal Klinik']);
        $setting->update(['content_json' => $settings]);
    }

    /**
     * Hitung nominal komisi dari harga paket berdasarkan setting aktif
     */
    public static function calculate(float $baseAmount, ?array $settings = null): float
    {
        $settings = $settings ?? self::settings();
        $value = max(0, $settings['commission_value']);

        if ($settings['commission_type'] === 'percent') {
            return round($baseAmount * min(100, $value) / 100);
        }

        return round($value);
    }

    public static function formatSetting(?array $settings = null): string
    {
        $settings = $settings ?? self::settings();

        if ($settings['commission_type'] === 'percent') {
            return rtrim(rtrim(number_format($settings['commission_value'], 2, ',', '.'), '0'), ',') . '% dari harga paket';
        }

        return 'Rp ' . number_format($settings['commission_value'], 0, ',', '.') . ' per klinik';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Sudah Dibayar',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu Pembayaran',
        };
    }
}
