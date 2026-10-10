<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class Therapist extends Model
{
    use BelongsToClinic;

    protected $table = 'therapists';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'therapist_id');
    }

    public function sessions()
    {
        return $this->hasMany(TherapySession::class, 'therapist_id');
    }

    /**
     * Cek apakah terapis memiliki jadwal bentrok pada rentang waktu tertentu.
     *
     * @param \Carbon\Carbon|string $dateTime Waktu mulai yang diminta
     * @param int $durationMinutes Durasi sesi dalam menit (default 60)
     * @param int|null $excludeReservationId ID reservasi yang dikecualikan (misal saat edit/konfirmasi)
     * @param int|null $excludeSessionId ID sesi yang dikecualikan (misal saat update sesi)
     * @return array|null Mengembalikan info konflik atau null jika tersedia
     */
    public function getScheduleConflict($dateTime, int $durationMinutes = 60, ?int $excludeReservationId = null, ?int $excludeSessionId = null): ?array
    {
        if (!$dateTime) {
            return null;
        }

        try {
            $start = $dateTime instanceof \Carbon\Carbon ? $dateTime->copy() : \Carbon\Carbon::parse($dateTime);
        } catch (\Exception $e) {
            return null;
        }

        $end = $start->copy()->addMinutes($durationMinutes > 0 ? $durationMinutes : 60);
        $dateStr = $start->toDateString();

        // 1. Cek bentrok dengan Sesi Terapi aktif
        $sessionQuery = TherapySession::with(['patient', 'therapyType'])
            ->where('therapist_id', $this->id)
            ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
            ->whereNotNull('scheduled_at')
            ->whereDate('scheduled_at', $dateStr);

        if ($excludeSessionId) {
            $sessionQuery->where('id', '!=', $excludeSessionId);
        }
        if ($excludeReservationId) {
            $sessionQuery->where(function ($q) use ($excludeReservationId) {
                $q->whereNull('reservation_id')->orWhere('reservation_id', '!=', $excludeReservationId);
            });
        }

        $sessions = $sessionQuery->get();
        foreach ($sessions as $session) {
            $sessDuration = $session->therapyType?->duration_minutes ?: 60;
            $sessStart = \Carbon\Carbon::parse($session->scheduled_at);
            $sessEnd = $sessStart->copy()->addMinutes($sessDuration);

            // Interval Overlap: start1 < end2 && end1 > start2
            if ($start < $sessEnd && $end > $sessStart) {
                $patientName = $session->patient?->full_name ?? 'Pasien Terdaftar';
                $formattedDate = $sessStart->locale('id')->isoFormat('D MMMM Y');
                $timeFormatted = $sessStart->format('H:i');
                return [
                    'type' => 'therapy_session',
                    'id' => $session->id,
                    'code' => $session->stage_name,
                    'patient_name' => $patientName,
                    'start_time' => $sessStart->format('H:i'),
                    'end_time' => $sessEnd->format('H:i'),
                    'scheduled_at' => $sessStart->toDateTimeString(),
                    'message' => "Terapis {$this->full_name} sudah memiliki jadwal sesi terapi ({$session->stage_name}) pada tanggal {$formattedDate} pukul {$timeFormatted} WIB (Pasien: {$patientName}). Silakan pilih jam lain atau terapis yang berbeda.",
                ];
            }
        }

        // 2. Cek bentrok dengan Reservasi Terapi aktif
        $reservationQuery = Reservation::with(['patient'])
            ->where('therapist_id', $this->id)
            ->whereIn('status', ['pending_confirmation', 'confirmed', 'in_progress'])
            ->where(function ($q) use ($dateStr) {
                $q->whereDate('preferred_datetime', $dateStr)
                  ->orWhereDate('confirmed_schedule', $dateStr);
            });

        if ($excludeReservationId) {
            $reservationQuery->where('id', '!=', $excludeReservationId);
        }

        $reservations = $reservationQuery->get();
        foreach ($reservations as $res) {
            $resStart = $res->confirmed_schedule
                ? \Carbon\Carbon::parse($res->confirmed_schedule)
                : ($res->preferred_datetime ? \Carbon\Carbon::parse($res->preferred_datetime) : null);

            if (!$resStart) {
                continue;
            }

            $resDuration = 60;
            $resEnd = $resStart->copy()->addMinutes($resDuration);

            if ($start < $resEnd && $end > $resStart) {
                $patientName = $res->patient?->full_name ?? 'Pasien';
                $formattedDate = $resStart->locale('id')->isoFormat('D MMMM Y');
                $timeFormatted = $resStart->format('H:i');
                return [
                    'type' => 'reservation',
                    'id' => $res->id,
                    'code' => $res->reservation_code,
                    'patient_name' => $patientName,
                    'start_time' => $resStart->format('H:i'),
                    'end_time' => $resEnd->format('H:i'),
                    'scheduled_at' => $resStart->toDateTimeString(),
                    'message' => "Terapis {$this->full_name} sudah memiliki jadwal reservasi ({$res->reservation_code}) pada tanggal {$formattedDate} pukul {$timeFormatted} WIB (Pasien: {$patientName}). Silakan pilih jam lain atau terapis yang berbeda.",
                ];
            }
        }

        return null;
    }

    /**
     * Cek apakah terapis tersedia (tidak ada bentrok)
     */
    public function isAvailableAt($dateTime, int $durationMinutes = 60, ?int $excludeReservationId = null, ?int $excludeSessionId = null): bool
    {
        return $this->getScheduleConflict($dateTime, $durationMinutes, $excludeReservationId, $excludeSessionId) === null;
    }

    /**
     * Ambil daftar slot jam yang sudah terisi untuk tanggal tertentu
     */
    public function getBookedSlotsForDate($date, ?int $excludeReservationId = null, ?int $excludeSessionId = null): array
    {
        try {
            $dateStr = \Carbon\Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            return [];
        }

        $slots = [];

        // Dari Sessions
        $sessionQuery = TherapySession::with(['patient', 'therapyType'])
            ->where('therapist_id', $this->id)
            ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
            ->whereNotNull('scheduled_at')
            ->whereDate('scheduled_at', $dateStr);

        if ($excludeSessionId) {
            $sessionQuery->where('id', '!=', $excludeSessionId);
        }
        if ($excludeReservationId) {
            $sessionQuery->where(function ($q) use ($excludeReservationId) {
                $q->whereNull('reservation_id')->orWhere('reservation_id', '!=', $excludeReservationId);
            });
        }

        foreach ($sessionQuery->get() as $s) {
            $start = \Carbon\Carbon::parse($s->scheduled_at);
            $duration = $s->therapyType?->duration_minutes ?: 60;
            $end = $start->copy()->addMinutes($duration);
            $slots[] = [
                'type' => 'session',
                'id' => $s->id,
                'time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'title' => $s->stage_name,
                'patient_name' => $s->patient?->full_name ?? 'Pasien',
            ];
        }

        // Dari Reservations
        $reservationQuery = Reservation::with(['patient'])
            ->where('therapist_id', $this->id)
            ->whereIn('status', ['pending_confirmation', 'confirmed', 'in_progress'])
            ->where(function ($q) use ($dateStr) {
                $q->whereDate('preferred_datetime', $dateStr)
                  ->orWhereDate('confirmed_schedule', $dateStr);
            });

        if ($excludeReservationId) {
            $reservationQuery->where('id', '!=', $excludeReservationId);
        }

        foreach ($reservationQuery->get() as $r) {
            $start = $r->confirmed_schedule
                ? \Carbon\Carbon::parse($r->confirmed_schedule)
                : ($r->preferred_datetime ? \Carbon\Carbon::parse($r->preferred_datetime) : null);

            if (!$start) {
                continue;
            }

            $end = $start->copy()->addMinutes(60);
            $slots[] = [
                'type' => 'reservation',
                'id' => $r->id,
                'time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'title' => $r->reservation_code,
                'patient_name' => $r->patient?->full_name ?? 'Pasien',
            ];
        }

        return $slots;
    }
}
