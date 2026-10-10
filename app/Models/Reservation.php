<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class Reservation extends Model
{
    use BelongsToClinic;

    protected $table = 'reservations';

    protected $guarded = [];

    protected $casts = [
        'preferred_datetime' => 'datetime',
        'confirmed_schedule' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'current_stage' => 'integer',
        'total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(MasterPatients::class, 'patient_id');
    }

    public function therapist()
    {
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }

    public function sessions()
    {
        return $this->hasMany(TherapySession::class, 'reservation_id')->orderBy('stage_number', 'asc');
    }

    public function therapySessions()
    {
        return $this->sessions();
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    /**
     * Hitung akumulasi total tarif dari semua sesi terapi dalam reservasi ini
     */
    public function calculateTotalPrice(): float
    {
        $sum = 0;
        foreach ($this->sessions as $session) {
            $sessionPrice = $session->total_price > 0 ? (float)$session->total_price : (float)($session->therapyType?->price ?? 0);
            $sum += $sessionPrice;
        }

        $this->update(['total_price' => $sum]);
        return (float) $sum;
    }

    public static function generateReservationCode(): string
    {
        $datePrefix = 'RSV-' . now()->format('Ymd');
        $countToday = self::whereDate('created_at', now()->toDateString())->count();
        $nextNumber = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        return "{$datePrefix}-{$nextNumber}";
    }

    public static function generateInvoiceCode(): string
    {
        $datePrefix = 'INV-' . now()->format('Ymd');
        $countToday = self::whereNotNull('invoice_code')
            ->whereDate('paid_at', now()->toDateString())
            ->count();
        $nextNumber = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        return "{$datePrefix}-{$nextNumber}";
    }
}
