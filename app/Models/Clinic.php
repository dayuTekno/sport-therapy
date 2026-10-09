<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $table = 'clinics';

    protected $guarded = [];

    protected $casts = [
        'trial_ends_at' => 'datetime',
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

    public function isOperational(): bool
    {
        if (!$this->is_active || $this->status === 'suspended') {
            return false;
        }

        if ($this->status === 'trial') {
            return $this->trial_ends_at ? $this->trial_ends_at->isFuture() : true;
        }

        return $this->status === 'active';
    }

    public static function generateClinicCode(): string
    {
        $prefix = 'CLN-' . now()->format('ymd');
        $countToday = self::whereDate('created_at', now()->toDateString())->count();
        $next = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        return "{$prefix}-{$next}";
    }
}
