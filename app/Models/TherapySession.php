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

    /**
     * Cek apakah pasien sudah punya sesi aktif yang waktunya tumpang tindih (terapis mana pun).
     * Mengembalikan pesan bentrok, atau null bila aman.
     */
    public static function patientScheduleConflict(int $patientId, $dateTime, int $durationMinutes = 60, ?int $excludeSessionId = null): ?string
    {
        $start = $dateTime instanceof \Carbon\Carbon ? $dateTime->copy() : \Carbon\Carbon::parse($dateTime);
        $end = $start->copy()->addMinutes($durationMinutes > 0 ? $durationMinutes : 60);

        $sessions = self::with(['therapyType', 'patient'])
            ->where('patient_id', $patientId)
            ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
            ->whereNotNull('scheduled_at')
            ->whereDate('scheduled_at', $start->toDateString())
            ->when($excludeSessionId, fn ($q) => $q->where('id', '!=', $excludeSessionId))
            ->get();

        foreach ($sessions as $session) {
            $sessStart = \Carbon\Carbon::parse($session->scheduled_at);
            $sessEnd = $sessStart->copy()->addMinutes($session->therapyType?->duration_minutes ?: 60);

            if ($start < $sessEnd && $end > $sessStart) {
                $patientName = $session->patient?->full_name ?? 'Pasien';
                return "Pasien {$patientName} sudah memiliki sesi terapi ({$session->stage_name}) pada "
                    . $sessStart->locale('id')->isoFormat('D MMMM Y') . " pukul {$sessStart->format('H:i')}–{$sessEnd->format('H:i')} WIB. "
                    . 'Silakan pilih jam lain yang tidak tumpang tindih.';
            }
        }

        return null;
    }
}
