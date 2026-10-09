<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'duration_in_months' => 'integer',
        'is_active' => 'boolean',
        'features_json' => 'array',
        'max_therapists' => 'integer',
        'max_patients' => 'integer',
        'sort_order' => 'integer',
    ];

    public function clinics()
    {
        return $this->hasMany(Clinic::class, 'subscription_plan_id');
    }

    public function getFormattedMonthlyRateAttribute(): string
    {
        $rate = $this->monthly_rate > 0 ? $this->monthly_rate : ($this->billing_cycle === 'yearly' ? $this->price / 12 : $this->price / 6);
        return 'Rp ' . number_format($rate, 0, ',', '.') . ',- / bulan';
    }

    public function getFormattedCommitmentAttribute(): string
    {
        if (!empty($this->commitment_label)) {
            return $this->commitment_label;
        }

        if ($this->duration_in_months === 12 || $this->billing_cycle === 'yearly') {
            return 'Total Periode 1 Tahun Penuh: Rp ' . number_format($this->price, 0, ',', '.') . ',- / tahun';
        }

        return 'Total Periode 6 Bulan Pertama: Rp ' . number_format($this->price, 0, ',', '.') . ',- (All-in)';
    }
}
