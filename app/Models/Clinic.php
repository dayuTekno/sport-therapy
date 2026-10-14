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
