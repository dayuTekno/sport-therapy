<?php

namespace App\Http\Controllers;

use App\Models\MasterPatients;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapySession;
use App\Models\TherapyType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $todayStats = TherapySession::whereDate('scheduled_at', $today)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("COUNT(*) FILTER (WHERE status = 'completed') as completed_count")
            ->selectRaw("COUNT(*) FILTER (WHERE status IN ('scheduled', 'in_progress', 'pending')) as scheduled_count")
            ->first();
        $todayTotal = (int) $todayStats->total_count;
        $todayCompleted = (int) $todayStats->completed_count;
        $todayScheduled = (int) $todayStats->scheduled_count;

        // Deteksi pasien yang memiliki lebih dari 1 sesi dalam 1 hari
        $multiSessionPatientIds = TherapySession::whereDate('scheduled_at', $today)
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('patient_id')
            ->toArray();
        $multiSessionCount = count($multiSessionPatientIds);

        $therapists = Therapist::where('is_active', true)->get();
        if ($therapists->isEmpty()) {
            $therapists = Therapist::all();
        }
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();
        if ($therapyTypes->isEmpty()) {
            $therapyTypes = TherapyType::orderBy('stage_order')->get();
        }

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
     * Form Tambah Sesi Terapi Pasien (Bisa >1 sesi dalam 1 hari & pilih tindakan)
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
        if ($therapists->isEmpty()) {
            $therapists = Therapist::all();
        }
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();
        if ($therapyTypes->isEmpty()) {
            $therapyTypes = TherapyType::orderBy('stage_order')->get();
        }

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

        // Tentukan nama tahap dari Master Tindakan jika tersedia
        $stageName = $request->stage_name;
        if ($request->therapy_type_id) {
            $tType = TherapyType::find($request->therapy_type_id);
            if ($tType) {
                $stageName = $tType->name;
            }
        }

        $duration = 60;
        if ($request->filled('therapy_type_id')) {
            $tTypeCheck = TherapyType::find($request->therapy_type_id);
            if ($tTypeCheck && $tTypeCheck->duration_minutes > 0) {
                $duration = $tTypeCheck->duration_minutes;
            }
        }

        // VALIDASI: Tolak jika pasien sudah punya sesi lain yang waktunya tumpang tindih
        if ($patientConflict = TherapySession::patientScheduleConflict((int) $request->patient_id, $scheduledAt, $duration)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['scheduled_at' => $patientConflict])
                ->with('error', $patientConflict);
        }

        // VALIDASI: Tolak jika terapis yang sama sudah memiliki jadwal pada jam yang sama
        // (sesi lain di reservasi yang sama tetap dicek; hanya slot reservasinya sendiri yang diabaikan)
        if ($request->filled('therapist_id')) {
            $therapist = Therapist::find($request->therapist_id);
            if ($therapist) {
                $conflict = $therapist->getScheduleConflict($scheduledAt, $duration, $request->reservation_id, null, false);
                if ($conflict) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['therapist_id' => $conflict['message']])
                        ->with('error', $conflict['message']);
                }
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
        $session = TherapySession::with(['patient.referral', 'therapist', 'therapyType', 'reservation'])->findOrFail($id);

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
        if ($therapists->isEmpty()) {
            $therapists = Therapist::all();
        }
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();
        if ($therapyTypes->isEmpty()) {
            $therapyTypes = TherapyType::orderBy('stage_order')->get();
        }

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

            $duration = $nextType?->duration_minutes ?: 60;

            // VALIDASI: Tolak jika pasien sudah punya sesi lain yang waktunya tumpang tindih dengan sesi lanjutan
            if ($patientConflict = TherapySession::patientScheduleConflict($session->patient_id, $nextScheduledAt, $duration, $session->id)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['next_scheduled_at' => $patientConflict])
                    ->with('error', $patientConflict);
            }

            $nextTherapistId = $request->next_therapist_id ?: $session->therapist_id;
            if ($nextTherapistId) {
                $therapist = Therapist::find($nextTherapistId);
                if ($therapist) {
                    $conflict = $therapist->getScheduleConflict($nextScheduledAt, $duration, $session->reservation_id, $session->id, false);
                    if ($conflict) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['next_therapist_id' => $conflict['message']])
                            ->with('error', $conflict['message']);
                    }
                }
            }

            $newSession = TherapySession::create([
                'patient_id' => $session->patient_id,
                'reservation_id' => $session->reservation_id,
                'therapist_id' => $nextTherapistId,
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

    /**
     * Generate Surat Keterangan Sakit (PDF) berdasarkan hasil terapi pasien yang telah selesai
     */
    public function generateSickLetter($id, Request $request)
    {
        $session = TherapySession::with(['patient', 'therapist', 'therapyType', 'clinic', 'reservation'])->findOrFail($id);
        $patient = $session->patient;

        if (!$patient) {
            return redirect()->back()->with('error', 'Data pasien untuk sesi terapi ini tidak ditemukan.');
        }

        $clinic = $session->clinic ?? auth()->user()?->clinic ?? \App\Models\Clinic::first();

        // 1. Nomor Surat (default otomatis atau input kustom)
        $monthNum = (int) now()->format('n');
        $monthRoman = [1=>'01', 2=>'02', 3=>'03', 4=>'04', 5=>'05', 6=>'06', 7=>'07', 8=>'08', 9=>'09', 10=>'10', 11=>'11', 12=>'12'][$monthNum];
        $defaultLetterNumber = sprintf('%03d/TOI/%s/%s', $session->id, $monthRoman, now()->format('Y'));
        $letterNumber = $request->input('letter_number', $defaultLetterNumber);

        // 2. Tanggal Surat & Tempat
        $city = $clinic?->city ?: 'Indramayu';
        $letterDate = $request->input('letter_date', $session->completed_at ? $session->completed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y'));

        // 3. Tanggal & Jam Kontrol Ulang
        $defaultControlDate = $session->completed_at ? $session->completed_at->copy()->addDays(7)->translatedFormat('d F Y') : now()->addDays(7)->translatedFormat('d F Y');
        $controlDate = $request->input('control_date', $defaultControlDate);
        $controlTime = $request->input('control_time', '18.00 - 20.30 WIB');

        // 4. Diagnosa
        $diagnosisInput = $request->input('diagnosis', $session->evaluation_notes ?: ($session->reservation?->chief_complaint ?: ($session->therapyType?->name ?? 'Cedera Fisik / Terapi Otot')));

        // 5. Nama & Jabatan Tertanda Pemeriksa
        $signerName = $request->input('signer_name', $clinic?->signer_name ?: ($session->therapist?->full_name ?: 'Iyon Pramulo, S.Or, M.Pd, AIFO'));
        $signerTitle = $request->input('signer_title', $clinic?->signer_title ?: 'Sport Therapis Pemeriksa');

        $logoBase64 = null;
        if ($clinic?->logo_path && file_exists(storage_path('app/public/' . $clinic->logo_path))) {
            $logoBase64 = 'data:image/' . pathinfo($clinic->logo_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $clinic->logo_path)));
        } elseif (file_exists(public_path('icons/hospital.png'))) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('icons/hospital.png')));
        }

        $stampBase64 = null;
        if ($clinic?->stamp_path && file_exists(storage_path('app/public/' . $clinic->stamp_path))) {
            $stampBase64 = 'data:image/' . pathinfo($clinic->stamp_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $clinic->stamp_path)));
        }

        $signatureBase64 = null;
        if ($clinic?->signature_path && file_exists(storage_path('app/public/' . $clinic->signature_path))) {
            $signatureBase64 = 'data:image/' . pathinfo($clinic->signature_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $clinic->signature_path)));
        }

        $pdf = Pdf::loadView('admin.pages.therapy-sessions.sick-letter-pdf', [
            'session' => $session,
            'patient' => $patient,
            'clinic' => $clinic,
            'letterNumber' => $letterNumber,
            'city' => $city,
            'letterDate' => $letterDate,
            'controlDate' => $controlDate,
            'controlTime' => $controlTime,
            'diagnosisInput' => $diagnosisInput,
            'signerName' => $signerName,
            'signerTitle' => $signerTitle,
            'logoBase64' => $logoBase64,
            'stampBase64' => $stampBase64,
            'signatureBase64' => $signatureBase64,
        ])->setPaper('a4', 'portrait');

        $filename = 'surat-keterangan-sakit-' . Str::slug($patient->full_name) . '-' . now()->format('Ymd') . '.pdf';

        if ($request->get('download') == 1) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
