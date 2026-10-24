<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $table = 'clinics';

    protected $guarded = [];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'max_therapists' => 'integer',
        'max_patients' => 'integer',
    ];

    protected static function booted(): void
    {
        // Setiap klinik baru otomatis memiliki kode referal sendiri
        static::creating(function (Clinic $clinic) {
            if (empty($clinic->referral_code)) {
                $clinic->referral_code = self::generateReferralCode($clinic->name);
            }
        });
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(ClinicSubscription::class, 'clinic_id')->orderBy('created_at', 'desc');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'clinic_id');
    }

    public function patients()
    {
        return $this->hasMany(MasterPatients::class, 'clinic_id');
    }

    public function therapists()
    {
        return $this->hasMany(Therapist::class, 'clinic_id');
    }

    public function therapyTypes()
    {
        return $this->hasMany(TherapyType::class, 'clinic_id');
    }

    public function equipments()
    {
        return $this->hasMany(TherapyEquipment::class, 'clinic_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'clinic_id');
    }

    public function sessions()
    {
        return $this->hasMany(TherapySession::class, 'clinic_id');
    }

    /**
     * Klinik perujuk (pemilik kode referal yang dipakai saat klinik ini mendaftar)
     */
    public function referrer()
    {
        return $this->belongsTo(Clinic::class, 'referred_by_clinic_id');
    }

    /**
     * Klinik-klinik yang mendaftar menggunakan kode referal klinik ini
     */
    public function referredClinics()
    {
        return $this->hasMany(Clinic::class, 'referred_by_clinic_id');
    }

    /**
     * Komisi yang diterima klinik ini dari klinik yang direferensikan
     */
    public function referralCommissions()
    {
        return $this->hasMany(ClinicReferralCommission::class, 'referrer_clinic_id');
    }

    /**
     * Komisi yang dihasilkan klinik ini untuk klinik perujuknya
     */
    public function referralCommission()
    {
        return $this->hasOne(ClinicReferralCommission::class, 'referred_clinic_id');
    }

    public static function generateReferralCode(?string $name = null): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $name), 0, 5)) ?: 'KLINIK';

        // Tanpa karakter yang mirip (0/O, 1/I/L) agar mudah dibaca & diketik ulang
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $suffix = '';
            for ($i = 0; $i < 4; $i++) {
                $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $code = $prefix . '-' . $suffix;
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Cari klinik pemilik kode referal (tidak peka huruf besar/kecil), hanya klinik yang tidak di-suspend
     */
    public static function findByReferralCode(?string $code): ?self
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '') {
            return null;
        }

        return self::where('referral_code', $code)
            ->where('is_active', true)
            ->where('status', '!=', 'suspended')
            ->first();
    }

    /**
     * Klinik sedang berlangganan paket berbayar (bukan trial, tidak suspended, belum lewat masa aktif)
     */
    public function isSubscribed(): bool
    {
        return $this->status === 'active' && $this->is_active && !$this->isSubscriptionExpired();
    }

    /**
     * Catat komisi untuk klinik perujuk saat klinik ini berlangganan paket (bukan saat mendaftar / trial).
     * Aman dipanggil berulang: komisi hanya dicatat satu kali per klinik terundang.
     */
    public function recordReferralCommission(): ?ClinicReferralCommission
    {
        if (!$this->referred_by_clinic_id || !$this->isSubscribed()) {
            return null;
        }

        if ($existing = $this->referralCommission()->first()) {
            return $existing;
        }

        if (!ClinicReferralCommission::settings()['is_enabled']) {
            return null;
        }

        // Besaran komisi mengikuti setting paket yang dilanggan klinik terundang
        $plan = SubscriptionPlan::find($this->subscription_plan_id);
        $amount = $plan?->calculateReferralCommission() ?? 0;
        if ($amount <= 0) {
            return null;
        }

        return ClinicReferralCommission::create([
            'referrer_clinic_id' => $this->referred_by_clinic_id,
            'referred_clinic_id' => $this->id,
            'subscription_plan_id' => $plan->id,
            'base_amount' => (float) $plan->price,
            'commission_type' => $plan->referral_commission_type,
            'commission_value' => $plan->referral_commission_value,
            'commission_amount' => $amount,
            'status' => 'pending',
        ]);
    }

    public function isSubscriptionExpired(): bool
    {
        if ($this->status === 'expired' || $this->status === 'suspended') {
            return true;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return true;
        }

        if ($this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isPast()) {
            return true;
        }

        return false;
    }

    public function daysRemaining(): ?int
    {
        $target = $this->subscription_ends_at ?? ($this->status === 'trial' ? $this->trial_ends_at : null);
        if (!$target) {
            return null;
        }
        // diffInDays(target, false): positif = target di masa depan, negatif = sudah lewat
        return (int) now()->diffInDays($target, false);
    }

    public function getEndDateAttribute()
    {
        return $this->subscription_ends_at;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['subscription_ends_at'] = $value;
    }

    public function isOperational(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return !$this->isSubscriptionExpired();
    }

    public static function generateClinicCode(): string
    {
        $prefix = 'CLN-' . now()->format('ymd');
        $countToday = self::whereDate('created_at', now()->toDateString())->count();
        $next = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        return "{$prefix}-{$next}";
    }
}
