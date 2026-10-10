<?php

namespace App\Http\Controllers;

use App\Models\TherapySession;
use App\Models\Therapist;
use App\Models\TherapyType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MedicalRecordReportController extends Controller
{
    /**
     * Laporan & Rekap Rekam Terapi Pasien (Sport Therapy)
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $therapistId = $request->input('therapist_id');
        $therapyTypeId = $request->input('therapy_type_id');
        $status = $request->input('status', 'all');

        $query = TherapySession::with(['patient', 'therapist', 'therapyType', 'reservation'])
            ->whereBetween('scheduled_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        if ($request->filled('therapist_id')) {
            $query->where('therapist_id', $therapistId);
        }

        if ($request->filled('therapy_type_id')) {
            $query->where('therapy_type_id', $therapyTypeId);
        }

        if ($status !== 'all' && !empty($status)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('full_name', 'ilike', "%{$search}%")
                      ->orWhere('patient_code', 'ilike', "%{$search}%")
                      ->orWhere('phone_number', 'ilike', "%{$search}%");
                })->orWhereHas('therapist', function ($tq) use ($search) {
                    $tq->where('full_name', 'ilike', "%{$search}%");
                })->orWhere('stage_name', 'ilike', "%{$search}%");
            });
        }

        // Summary Statistics (sesuai rentang filter)
        $statQuery = TherapySession::whereBetween('scheduled_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
        if ($request->filled('therapist_id')) {
            $statQuery->where('therapist_id', $therapistId);
        }
        $totalSessions = (clone $statQuery)->count();
        $completedSessions = (clone $statQuery)->where('status', 'completed')->count();
        $inProgressSessions = (clone $statQuery)->whereIn('status', ['in_progress', 'scheduled'])->count();

        $records = $query->orderBy('scheduled_at', 'desc')->paginate(20)->withQueryString();

        $therapists = Therapist::where('is_active', true)->orderBy('full_name')->get();
        if ($therapists->isEmpty()) {
            $therapists = Therapist::orderBy('full_name')->get();
        }
        $therapyTypes = TherapyType::where('is_active', true)->orderBy('stage_order')->get();
        if ($therapyTypes->isEmpty()) {
            $therapyTypes = TherapyType::orderBy('stage_order')->get();
        }

        return view('admin.pages.reports.medical-records.index', compact(
            'records',
            'startDate',
            'endDate',
            'therapists',
            'therapyTypes',
            'totalSessions',
            'completedSessions',
            'inProgressSessions'
        ));
    }

    /**
     * Detail Lembar Rekam Terapi Pasien
     */
    public function show($id)
    {
        $session = TherapySession::with(['patient', 'therapist', 'therapyType', 'reservation'])->findOrFail($id);

        $patientSessions = TherapySession::with(['therapist', 'therapyType'])
            ->where('patient_id', $session->patient_id)
            ->orderBy('scheduled_at', 'asc')
            ->orderBy('daily_session_order', 'asc')
            ->get();

        return view('admin.pages.reports.medical-records.show', compact('session', 'patientSessions'));
    }
}
