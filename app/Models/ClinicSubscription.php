<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSubscription extends Model
{
    protected $table = 'clinic_subscriptions';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public static function generateInvoiceCode(): string
    {
        $prefix = 'SUB-' . now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count();
        $next = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return "{$prefix}-{$next}";
    }
}
