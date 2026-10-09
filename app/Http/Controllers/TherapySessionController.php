<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapySession;
use App\Models\TherapyType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TherapySessionController extends Controller
{
    /**
     * Dashboard & Daftar Sesi Terapi Pasien (Mendukung >1 sesi per hari & alur berjenjang)
     */
    public function index(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->toDateString());
        $statusFilter = $request->get('status', 'all');
        $patientId = $request->get('patient_id');
        $search = $request->get('q');

        $query = TherapySession::with(['patient', 'therapist', 'therapyType', 'reservation']);

        if ($request->filled('date') && $request->date !== 'all') {
            $query->whereDate('scheduled_at', $selectedDate);
        }

        if ($statusFilter !== 'all' && !empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($patientId)) {
            $query->where('patient_id', $patientId);
        }

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('phone_number', 'ilike', "%{$search}%");
            });
        }

        $sessions = $query->orderBy('scheduled_at', 'asc')
            ->orderBy('daily_session_order', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Statistik Hari Ini
        $today = Carbon::today()->toDateString();
        $todayTotal = TherapySession::whereDate('scheduled_at', $today)->count();
        $todayCompleted = TherapySession::whereDate('scheduled_at', $today)->where('status', 'completed')->count();
        $todayScheduled = TherapySession::whereDate('scheduled_at', $today)->whereIn('status', ['scheduled', 'in_progress', 'pending'])->count();

        // Deteksi pasien yang memiliki lebih dari 1 sesi dalam 1 hari
        $multiSessionPatientIds = TherapySession::whereDate('scheduled_at', $today)
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('patient_id')
            ->toArray();
        $multiSessionCount = count($multiSessionPatientIds);

        $therapists = Therapist::where('is_active', true)->get();
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();

        return view('admin.pages.therapy-sessions.index', compact(
            'sessions',
            'selectedDate',
            'statusFilter',
            'search',
            'patientId',
            'todayTotal',
            'todayCompleted',
            'todayScheduled',
            'multiSessionCount',
            'multiSessionPatientIds',
            'therapists',
            'therapyTypes'
        ));
    }

    /**
     * Form Tambah Sesi Terapi Pasien (Bisa >1 sesi dalam 1 hari & pilih jenjang)
     */
    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $reservationId = $request->get('reservation_id');

        $selectedPatient = null;
        $selectedReservation = null;
        $nextStageSuggestion = 1;
        $suggestedDailyOrder = 1;

        if ($patientId) {
            $selectedPatient = MasterPatients::find($patientId);
            if ($selectedPatient) {
                // Hitung urutan sesi harian jika tanggal sudah ada
                $targetDate = $request->get('date', Carbon::today()->toDateString());
                $countToday = TherapySession::where('patient_id', $selectedPatient->id)
                    ->whereDate('scheduled_at', $targetDate)
                    ->count();
                $suggestedDailyOrder = $countToday + 1;

                // Cek tahap tertinggi yang sudah dicapai
                $highestSession = TherapySession::where('patient_id', $selectedPatient->id)
                    ->orderBy('stage_number', 'desc')
                    ->first();
                if ($highestSession) {
                    $nextStageSuggestion = $highestSession->status == 'completed' 
                        ? $highestSession->stage_number + 1 
                        : $highestSession->stage_number;
                }
            }
        }

        if ($reservationId) {
            $selectedReservation = Reservation::find($reservationId);
            if ($selectedReservation && !$selectedPatient) {
                $selectedPatient = $selectedReservation->patient;
            }
        }

        $patients = MasterPatients::orderBy('full_name')->get();
        $therapists = Therapist::where('is_active', true)->get();
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();

        return view('admin.pages.therapy-sessions.create', compact(
            'patients',
            'therapists',
            'therapyTypes',
            'selectedPatient',
            'selectedReservation',
            'nextStageSuggestion',
            'suggestedDailyOrder'
        ));
    }

    /**
     * Simpan Sesi Terapi Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:master_patients,id',
            'therapist_id' => 'nullable|exists:therapists,id',
            'therapy_type_id' => 'nullable|exists:therapy_types,id',
            'stage_number' => 'required|integer|min:1',
            'stage_name' => 'required|string',
            'scheduled_at' => 'required|date',
            'daily_session_order' => 'nullable|integer',
            'session_notes' => 'nullable|string',
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        $scheduledAt = Carbon::parse($request->scheduled_at);
        $sessionDate = $scheduledAt->toDateString();

        // Hitung otomatis nomor sesi harian jika tidak diinput
        $dailyOrder = $request->daily_session_order;
        if (empty($dailyOrder)) {
            $existingCount = TherapySession::where('patient_id', $request->patient_id)
                ->whereDate('scheduled_at', $sessionDate)
                ->count();
            $dailyOrder = $existingCount + 1;
        }

        // Tentukan nama tahap dari Master Jenjang Terapi jika tersedia
        $stageName = $request->stage_name;
        if ($request->therapy_type_id) {
            $tType = TherapyType::find($request->therapy_type_id);
            if ($tType) {
                $stageName = $tType->name;
            }
        }

        $session = TherapySession::create([
            'patient_id' => $request->patient_id,
            'reservation_id' => $request->reservation_id,
            'therapist_id' => $request->therapist_id,
            'therapy_type_id' => $request->therapy_type_id,
            'stage_number' => $request->stage_number,
            'stage_name' => $stageName,
            'daily_session_order' => $dailyOrder,
            'scheduled_at' => $scheduledAt,
            'status' => 'scheduled',
            'evaluation_notes' => $request->session_notes,
            'total_price' => $tType?->price ?? 0,
            'payment_status' => 'unpaid',
        ]);

        if ($session->reservation) {
            $session->reservation->calculateTotalPrice();
        }

        return redirect()->route('therapy-sessions.show', $session->id)
            ->with('success', "Sesi terapi berhasil dibuat! (Sesi Ke-{$dailyOrder} untuk pasien pada {$sessionDate}).");
    }

    /**
     * Lembar Kerja Sesi Terapi & Evaluasi Perkembangan Berjenjang
     */
    public function show($id)
    {
        $session = TherapySession::with(['patient', 'therapist', 'therapyType', 'reservation'])->findOrFail($id);

        // Ambil riwayat lengkap seluruh tahapan terapi pasien ini
        $allSessions = TherapySession::where('patient_id', $session->patient_id)
            ->with(['therapist', 'therapyType'])
            ->orderBy('scheduled_at', 'asc')
            ->orderBy('daily_session_order', 'asc')
            ->get();

        // Ambil sesi lain pasien ini pada HARI YANG SAMA
        $sessionDate = $session->scheduled_at ? $session->scheduled_at->toDateString() : Carbon::today()->toDateString();
        $sameDaySessions = TherapySession::where('patient_id', $session->patient_id)
            ->whereDate('scheduled_at', $sessionDate)
            ->where('id', '!=', $session->id)
            ->orderBy('scheduled_at', 'asc')
            ->orderBy('daily_session_order', 'asc')
            ->get();

        $therapists = Therapist::where('is_active', true)->get();
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();

        return view('admin.pages.therapy-sessions.show', compact(
            'session',
            'allSessions',
            'sameDaySessions',
            'therapists',
            'therapyTypes'
        ));
    }

    /**
     * Selesaikan Sesi Terapi & Opsi Lanjut Tahap Berikutnya (Bisa di Hari yang Sama)
     */
    public function complete(Request $request, $id)
    {
        $session = TherapySession::findOrFail($id);

        $request->validate([
            'actions_taken' => 'required|string',
            'evaluation_notes' => 'required|string',
            'recommended_next_stage' => 'nullable|string',
            'advance_to_next' => 'nullable|boolean',
            'next_therapy_type_id' => 'nullable|exists:therapy_types,id',
            'next_scheduled_at' => 'nullable|date',
            'next_therapist_id' => 'nullable|exists:therapists,id',
            'next_is_same_day' => 'nullable|boolean',
        ]);

        $price = $session->therapyType?->price ?? 0;

        $session->update([
            'status' => 'completed',
            'actions_taken' => $request->actions_taken,
            'evaluation_notes' => $request->evaluation_notes,
            'recommended_next_stage' => $request->recommended_next_stage,
            'completed_at' => now(),
            'total_price' => $session->total_price > 0 ? $session->total_price : $price,
            'payment_status' => $session->payment_status ?: 'unpaid',
        ]);

        // Jika dicentang opsi untuk langsung membuat sesi terapi berikutnya
        if ($request->boolean('advance_to_next')) {
            $nextStageNumber = $session->stage_number + 1;

            $nextType = null;
            if ($request->filled('next_therapy_type_id')) {
                $nextType = TherapyType::find($request->next_therapy_type_id);
            } else {
                $nextType = TherapyType::where('stage_order', $nextStageNumber)->first();
            }

            $nextStageName = $nextType 
                ? $nextType->name 
                : 'Terapi Tahap ' . chr(64 + $nextStageNumber) . ': Pemulihan & Penguatan Lanjutan';

            // Jadwal berikutnya: bisa beberapa jam kemudian di hari yang sama (misal sesi sore)
            $nextScheduledAt = $request->filled('next_scheduled_at')
                ? Carbon::parse($request->next_scheduled_at)
                : now()->addHours(3);

            $nextDate = $nextScheduledAt->toDateString();
            $existingCount = TherapySession::where('patient_id', $session->patient_id)
                ->whereDate('scheduled_at', $nextDate)
                ->count();
            $dailyOrder = $existingCount + 1;

            $newSession = TherapySession::create([
                'patient_id' => $session->patient_id,
                'reservation_id' => $session->reservation_id,
                'therapist_id' => $request->next_therapist_id ?: $session->therapist_id,
                'therapy_type_id' => $nextType?->id,
                'stage_number' => $nextStageNumber,
                'stage_name' => $nextStageName,
                'daily_session_order' => $dailyOrder,
                'scheduled_at' => $nextScheduledAt,
                'status' => 'scheduled',
                'total_price' => $nextType?->price ?? 0,
                'payment_status' => 'unpaid',
            ]);

            if ($session->reservation) {
                $session->reservation->calculateTotalPrice();
            }

            return redirect()->route('therapy-sessions.show', $newSession->id)
                ->with('success', "Tahap {$session->stage_number} berhasil diselesaikan! Sesi lanjutan ({$nextStageName}) telah dibuat sebagai Sesi Ke-{$dailyOrder} untuk pasien.");
        } else {
            // Jika tidak melanjutkan ke sesi baru, tandai reservasi sebagai selesai jika tidak ada sesi aktif lainnya
            if ($session->reservation_id) {
                $hasIncomplete = TherapySession::where('reservation_id', $session->reservation_id)
                    ->whereIn('status', ['pending', 'scheduled', 'in_progress'])
                    ->where('id', '!=', $session->id)
                    ->exists();

                if (!$hasIncomplete) {
                    Reservation::where('id', $session->reservation_id)
                        ->where('status', '!=', 'cancelled')
                        ->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                }

                $session->reservation->calculateTotalPrice();
            }
        }

        return redirect()->route('therapy-sessions.show', $session->id)
            ->with('success', 'Sesi terapi berhasil diselesaikan dan catatan evaluasi telah disimpan.');
    }

    /**
     * Riwayat Seluruh Jejak Terapi Berjenjang Pasien
     */
    public function patientHistory($patientId)
    {
        $patient = MasterPatients::with(['therapySessions.therapist', 'therapySessions.therapyType'])->findOrFail($patientId);
        $sessions = $patient->therapySessions;

        return view('admin.pages.therapy-sessions.patient-history', compact('patient', 'sessions'));
    }

    /**
     * Hapus Sesi Terapi
     */
    public function destroy($id)
    {
        $session = TherapySession::findOrFail($id);
        $session->delete();

        return redirect()->route('therapy-sessions.index')
            ->with('success', 'Sesi terapi berhasil dihapus.');
    }

    /**
     * API: Cek status tahap terakhir dan riwayat hari ini pasien
     */
    public function getPatientLastStage($patientId, Request $request)
    {
        $targetDate = $request->get('date', Carbon::today()->toDateString());
        $patient = MasterPatients::find($patientId);

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan']);
        }

        $highestSession = TherapySession::where('patient_id', $patientId)
            ->orderBy('stage_number', 'desc')
            ->first();

        $dailyCount = TherapySession::where('patient_id', $patientId)
            ->whereDate('scheduled_at', $targetDate)
            ->count();

        $allSessionsCount = TherapySession::where('patient_id', $patientId)->count();

        return response()->json([
            'success' => true,
            'patient' => [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'phone_number' => $patient->phone_number,
                'age' => $patient->age,
                'gender' => $patient->gender,
            ],
            'highest_stage' => $highestSession ? $highestSession->stage_number : 0,
            'suggested_stage' => $highestSession ? ($highestSession->status == 'completed' ? $highestSession->stage_number + 1 : $highestSession->stage_number) : 1,
            'sessions_today_count' => $dailyCount,
            'next_daily_order' => $dailyCount + 1,
            'total_sessions_history' => $allSessionsCount,
        ]);
    }
}
