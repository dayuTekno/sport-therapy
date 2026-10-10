<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapySession;
use App\Models\TherapyType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['patient', 'therapist']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: Hanya menampilkan reservasi aktif (Pending, Terkonfirmasi, Sedang Terapi)
            $query->whereIn('status', ['pending_confirmation', 'confirmed', 'in_progress']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reservation_code', 'ilike', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('full_name', 'ilike', "%{$search}%")
                         ->orWhere('phone_number', 'ilike', "%{$search}%");
                  });
            });
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);

        // Counter untuk Tabs dan Badges
        $activeCount = Reservation::whereIn('status', ['pending_confirmation', 'confirmed', 'in_progress'])->count();
        $historyCount = Reservation::whereIn('status', ['completed', 'cancelled'])->count();
        $pendingCount = Reservation::where('status', 'pending_confirmation')->count();
        $confirmedCount = Reservation::where('status', 'confirmed')->count();
        $inProgressCount = Reservation::where('status', 'in_progress')->count();

        return view('admin.pages.reservations.index', compact(
            'reservations',
            'activeCount',
            'historyCount',
            'pendingCount',
            'confirmedCount',
            'inProgressCount'
        ));
    }

    public function create()
    {
        $therapists = Therapist::where('is_active', true)->get();
        return view('admin.pages.reservations.create', compact('therapists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string',
            'chief_complaint' => 'required|string',
            'complaint_duration' => 'required|string',
            'medical_history' => 'nullable|string',
            'preferred_schedule' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string',
            'therapist_id' => 'nullable|exists:therapists,id',
            'patient_id' => 'nullable|exists:master_patients,id',
        ]);

        // Clean phone number
        $cleanedPhone = preg_replace('/\D/', '', $request->phone_number);
        $searchPhone = str_starts_with($cleanedPhone, '0') ? substr($cleanedPhone, 1) : $cleanedPhone;

        // Find Master Patient
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = MasterPatients::find($request->patient_id);
        }

        if (!$patient) {
            $patient = MasterPatients::where('phone_number', 'ilike', "%{$searchPhone}%")
                ->orWhere('phone_number', 'ilike', "%{$cleanedPhone}%")
                ->first();
        }

        if (!$patient) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nomor HP belum terdaftar di Master Data Pasien. Silakan daftarkan pasien terlebih dahulu.');
        }

        // Optional sync if details were passed
        if ($request->filled('full_name') || $request->filled('age') || $request->filled('occupation') || $request->filled('address')) {
            $patient->update(array_filter([
                'full_name' => $request->full_name,
                'age' => $request->age,
                'gender' => $request->gender,
                'occupation' => $request->occupation,
                'address' => $request->address,
            ]));
        }

        // Generate reservation code
        $reservationCode = Reservation::generateReservationCode();

        // Format preferred schedule from datepicker & timepicker
        $preferredSchedule = $request->preferred_schedule;
        $targetDateTime = null;

        if ($request->filled('preferred_date')) {
            $time = $request->preferred_time ?: '09:00';
            try {
                $targetDateTime = \Carbon\Carbon::parse($request->preferred_date . ' ' . $time);
                $preferredSchedule = $targetDateTime->locale('id')->isoFormat('dddd, D MMMM Y - [Pukul] HH:mm [WIB]');
            } catch (\Exception $e) {
                $preferredSchedule = $request->preferred_date . ' - ' . $time;
            }
        } elseif ($request->filled('preferred_schedule')) {
            try {
                $targetDateTime = \Carbon\Carbon::parse($request->preferred_schedule);
            } catch (\Exception $e) {
                $targetDateTime = null;
            }
        }

        if (empty($preferredSchedule)) {
            $targetDateTime = now();
            $preferredSchedule = now()->locale('id')->isoFormat('dddd, D MMMM Y - [Pukul] HH:mm [WIB]');
        }

        // VALIDASI: Tolak jika terapis yang sama sudah memiliki jadwal pada jam yang sama
        if ($request->filled('therapist_id') && $targetDateTime) {
            $therapist = Therapist::find($request->therapist_id);
            if ($therapist) {
                $conflict = $therapist->getScheduleConflict($targetDateTime);
                if ($conflict) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['therapist_id' => $conflict['message']])
                        ->with('error', $conflict['message']);
                }
            }
        }

        $reservation = Reservation::create([
            'reservation_code' => $reservationCode,
            'patient_id' => $patient->id,
            'therapist_id' => $request->therapist_id,
            'chief_complaint' => $request->chief_complaint,
            'complaint_duration' => $request->complaint_duration,
            'medical_history' => $request->medical_history,
            'preferred_schedule' => $preferredSchedule,
            'preferred_datetime' => $targetDateTime,
            'status' => 'pending_confirmation',
        ]);

        return redirect()->route('reservations.show', $reservation->id)
            ->with('success', 'Reservasi berhasil dikirim! Informasi booking tersimpan dan siap dikonfirmasi.');
    }

    public function show($id)
    {
        $reservation = Reservation::with(['patient', 'therapist'])->findOrFail($id);
        $therapists = Therapist::where('is_active', true)->get();
        $therapySessions = TherapySession::where('patient_id', $reservation->patient_id)
            ->with(['therapist', 'therapyType'])
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('daily_session_order', 'desc')
            ->get();

        return view('admin.pages.reservations.show', compact('reservation', 'therapists', 'therapySessions'));
    }

    public function confirm(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'confirmed_schedule' => 'required',
            'therapist_id' => 'nullable|exists:therapists,id',
            'notes' => 'nullable',
        ]);

        $therapistId = $request->therapist_id ?: $reservation->therapist_id;
        $targetDateTime = null;
        try {
            $targetDateTime = \Carbon\Carbon::parse($request->confirmed_schedule);
        } catch (\Exception $e) {
            $targetDateTime = null;
        }

        // VALIDASI: Tolak jika terapis bentrok pada jam yang dikonfirmasi
        if ($therapistId && $targetDateTime) {
            $therapist = Therapist::find($therapistId);
            if ($therapist) {
                $conflict = $therapist->getScheduleConflict($targetDateTime, 60, $reservation->id);
                if ($conflict) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['therapist_id' => $conflict['message']])
                        ->with('error', $conflict['message']);
                }
            }
        }

        $reservation->update([
            'status' => 'confirmed',
            'confirmed_schedule' => $request->confirmed_schedule,
            'preferred_datetime' => $reservation->preferred_datetime ?: $targetDateTime,
            'therapist_id' => $therapistId,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Jadwal reservasi terapi berhasil dikonfirmasi.');
    }

    public function advanceStage(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'current_stage_evaluation' => 'required',
            'next_stage_therapy_id' => 'nullable|exists:therapy_types,id',
            'scheduled_at' => 'nullable',
            'therapist_id' => 'nullable|exists:therapists,id',
        ]);

        $therapistId = $request->therapist_id ?: $reservation->therapist_id;
        if ($therapistId && $request->filled('scheduled_at')) {
            try {
                $advanceDateTime = \Carbon\Carbon::parse($request->scheduled_at);
                $therapist = Therapist::find($therapistId);
                if ($therapist) {
                    $conflict = $therapist->getScheduleConflict($advanceDateTime, 60, $reservation->id);
                    if ($conflict) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['therapist_id' => $conflict['message']])
                            ->with('error', $conflict['message']);
                    }
                }
            } catch (\Exception $e) {
                // Ignore parse errors if any
            }
        }

        $currentStage = $reservation->current_stage;
        $nextStageNumber = $currentStage + 1;

        // 1. Mark current stage completed
        $currentSession = $reservation->sessions()->where('stage_number', $currentStage)->first();
        if ($currentSession) {
            $currentSession->update([
                'status' => 'completed',
                'evaluation_notes' => $request->current_stage_evaluation,
                'recommended_next_stage' => "Lanjut ke Tahap {$nextStageNumber}",
                'completed_at' => now(),
            ]);
        }

        // 2. Next therapy type
        $nextType = null;
        if ($request->next_stage_therapy_id) {
            $nextType = TherapyType::find($request->next_stage_therapy_id);
        } else {
            $nextType = TherapyType::where('stage_order', $nextStageNumber)->first();
        }

        $nextStageName = $nextType 
            ? $nextType->name 
            : 'Terapi Tahap ' . chr(64 + $nextStageNumber) . ': Latihan Lanjutan';

        // 3. Create next stage session
        TherapySession::create([
            'reservation_id' => $reservation->id,
            'patient_id' => $reservation->patient_id,
            'therapist_id' => $therapistId,
            'therapy_type_id' => $nextType?->id,
            'stage_number' => $nextStageNumber,
            'stage_name' => $nextStageName,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'scheduled' : 'pending',
        ]);

        // 4. Update current stage in reservation
        $reservation->update([
            'current_stage' => $nextStageNumber,
            'status' => 'in_progress',
        ]);

        return redirect()->back()->with('success', "Tahap {$currentStage} selesai dievaluasi! Berhasil membuka {$nextStageName}.");
    }

    /**
     * API untuk memeriksa ketersediaan dan jadwal bentrok terapis secara realtime
     */
    public function checkTherapistAvailability(Request $request)
    {
        $therapistId = $request->get('therapist_id');
        $date = $request->get('date');
        $time = $request->get('time');
        $excludeReservationId = $request->get('exclude_reservation_id');
        $excludeSessionId = $request->get('exclude_session_id');

        if (!$therapistId) {
            return response()->json([
                'available' => true,
                'message' => 'Terapis belum ditentukan.',
                'booked_slots' => [],
            ]);
        }

        $therapist = Therapist::find($therapistId);
        if (!$therapist) {
            return response()->json([
                'available' => false,
                'message' => 'Terapis tidak ditemukan.',
                'booked_slots' => [],
            ], 404);
        }

        $bookedSlots = $date ? $therapist->getBookedSlotsForDate($date, $excludeReservationId, $excludeSessionId) : [];

        $conflict = null;
        if ($date && $time) {
            try {
                $targetDateTime = \Carbon\Carbon::parse($date . ' ' . $time);
                $conflict = $therapist->getScheduleConflict($targetDateTime, 60, $excludeReservationId, $excludeSessionId);
            } catch (\Exception $e) {
                $conflict = null;
            }
        }

        return response()->json([
            'available' => $conflict === null,
            'therapist_name' => $therapist->full_name,
            'conflict' => $conflict,
            'booked_slots' => $bookedSlots,
        ]);
    }

    public function lookupPatient(Request $request)
    {
        $phone = $request->get('phone');
        if (!$phone) {
            return response()->json(['found' => false]);
        }

        $cleaned = preg_replace('/\D/', '', $phone);
        $search = str_starts_with($cleaned, '0') ? substr($cleaned, 1) : $cleaned;

        $patient = MasterPatients::where('phone_number', 'ilike', "%{$search}%")
            ->orWhere('phone_number', 'ilike', "%{$cleaned}%")
            ->first();

        if ($patient) {
            return response()->json([
                'found' => true,
                'patient' => [
                    'id' => $patient->id,
                    'patient_code' => $patient->patient_code,
                    'full_name' => $patient->full_name,
                    'age' => $patient->age,
                    'gender' => $patient->gender,
                    'gender_label' => $patient->gender === 'female' ? 'Perempuan' : 'Laki-laki',
                    'occupation' => $patient->occupation,
                    'address' => $patient->address,
                    'phone_number' => $patient->phone_number,
                    'nik' => $patient->nik,
                ],
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'Nomor HP tidak ditemukan di Master Data Pasien.',
            'create_url' => route('patients.create', ['phone' => $phone, 'from' => 'reservation']),
        ]);
    }

    /**
     * Riwayat Terapi (Berisi reservasi yang Selesai & Dibatalkan)
     */
    public function history(Request $request)
    {
        $query = Reservation::with(['patient', 'therapist'])
            ->whereIn('status', ['completed', 'cancelled']);

        if ($request->filled('status') && in_array($request->status, ['completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reservation_code', 'ilike', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('full_name', 'ilike', "%{$search}%")
                         ->orWhere('phone_number', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reservations = $query->orderBy('updated_at', 'desc')->paginate(10);

        $totalHistoryCount = Reservation::whereIn('status', ['completed', 'cancelled'])->count();
        $completedCount = Reservation::where('status', 'completed')->count();
        $cancelledCount = Reservation::where('status', 'cancelled')->count();

        return view('admin.pages.reservations.history', compact(
            'reservations',
            'totalHistoryCount',
            'completedCount',
            'cancelledCount'
        ));
    }

    /**
     * Batalkan Reservasi Terapi
     */
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $reason = $request->cancellation_reason ?: 'Dibatalkan oleh klinik / pasien';

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        // Batalkan juga sesi terapi yang masih pending atau terjadwal jika ada
        $reservation->sessions()
            ->whereIn('status', ['pending', 'scheduled'])
            ->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', "Reservasi {$reservation->reservation_code} berhasil dibatalkan dan telah dipindahkan ke Riwayat Terapi.");
    }

    /**
     * Selesaikan Reservasi Terapi
     */
    public function complete(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', "Reservasi {$reservation->reservation_code} berhasil diselesaikan dan dipindahkan ke Riwayat Terapi.");
    }
}

