<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class TherapySession extends Model
{
    use BelongsToClinic;

    protected $table = 'therapy_sessions';

    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'stage_number' => 'integer',
        'total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function patient()
    {
        return $this->belongsTo(MasterPatients::class, 'patient_id');
    }

    public function therapist()
    {
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }

    public function therapyType()
    {
        return $this->belongsTo(TherapyType::class, 'therapy_type_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    public static function generateInvoiceCode(): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $countToday = self::whereDate('paid_at', now()->toDateString())->whereNotNull('invoice_code')->count();
        $nextNumber = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        return "{$prefix}-{$nextNumber}";
    }
}
