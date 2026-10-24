<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClinicSubscription extends Model
{
    protected $table = 'clinic_subscriptions';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public const PAYMENT_SETTING_KEY = 'subscription_payment';

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public static function generateInvoiceCode(): string
    {
        $prefix = 'SUB-' . now()->format('Ymd');
        $next = self::whereDate('created_at', now()->toDateString())->count() + 1;

        do {
            $code = $prefix . '-' . str_pad($next++, 3, '0', STR_PAD_LEFT);
        } while (self::where('invoice_code', $code)->exists());

        return $code;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Disetujui / Aktif',
            'rejected', 'failed' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
            default => 'Menunggu Verifikasi',
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-700',
            'rejected', 'failed' => 'bg-rose-100 text-rose-700',
            'cancelled', 'expired' => 'bg-gray-100 text-gray-500',
            default => 'bg-amber-100 text-amber-700',
        };
    }

    /**
     * Durasi paket dalam bulan (fallback dari billing_cycle untuk paket lama)
     */
    public static function planDurationInMonths(?SubscriptionPlan $plan): int
    {
        if ($plan && $plan->duration_in_months > 0) {
            return (int) $plan->duration_in_months;
        }

        return match ($plan?->billing_cycle) {
            'yearly' => 12,
            'monthly' => 1,
            default => 6,
        };
    }

    /**
     * Approve manual oleh SaaS admin: aktifkan paket klinik & perpanjang masa langganan.
     * Jika langganan klinik masih berjalan, masa aktif baru disambung dari tanggal berakhir yang lama.
     */
    public function approve(User $admin, ?string $notes = null): void
    {
        DB::transaction(function () use ($admin, $notes) {
            $clinic = Clinic::lockForUpdate()->findOrFail($this->clinic_id);
            $plan = SubscriptionPlan::find($this->subscription_plan_id);
            $wasSubscribed = $clinic->isSubscribed();

            $startsAt = ($clinic->status === 'active' && $clinic->subscription_ends_at?->isFuture())
                ? $clinic->subscription_ends_at->copy()
                : now();
            $expiresAt = $startsAt->copy()->addMonthsNoOverflow(self::planDurationInMonths($plan));

            $this->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'processed_by_user_id' => $admin->id,
                'processed_at' => now(),
                'admin_notes' => $notes,
            ]);

            $clinic->update([
                'status' => 'active',
                'is_active' => true,
                'subscription_plan_id' => $plan?->id ?? $clinic->subscription_plan_id,
                'subscription_ends_at' => $expiresAt,
                'max_therapists' => $plan?->max_therapists ?? $clinic->max_therapists,
                'max_patients' => $plan?->max_patients ?? $clinic->max_patients,
            ]);

            // Klinik undangan yang pertama kali berlangganan → catat komisi referal untuk klinik perujuk
            if (!$wasSubscribed && $clinic->isSubscribed()) {
                $clinic->recordReferralCommission();
            }
        });
    }

    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'payment_status' => 'rejected',
            'processed_by_user_id' => $admin->id,
            'processed_at' => now(),
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Instruksi pembayaran (rekening tujuan, dsb) yang ditampilkan ke klinik saat membeli paket
     */
    public static function paymentInstructions(): string
    {
        $setting = LandingPageSetting::where('section_key', self::PAYMENT_SETTING_KEY)->first();

        return (string) ($setting?->content_json['instructions'] ?? '');
    }

    public static function savePaymentInstructions(string $instructions): void
    {
        LandingPageSetting::getSection(self::PAYMENT_SETTING_KEY, ['title' => 'Instruksi Pembayaran Paket'])
            ->update(['content_json' => ['instructions' => $instructions]]);
    }
}
