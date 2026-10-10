<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\EselonController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\PolyclinicController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\Icd9Controller;
use App\Http\Controllers\Icd10Controller;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PolyIcd9Controller;
use App\Http\Controllers\PolyIcd10Controller;
use App\Http\Controllers\PolyDoctorController;
use App\Http\Controllers\PolyProcedureController;
use App\Http\Controllers\NurseAnamnesisController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PharmacyController;

Route::get('/', [App\Http\Controllers\PublicController::class, 'landing'])->name('public.landing');
Route::get('/register-clinic', [App\Http\Controllers\PublicController::class, 'registerClinicForm'])->name('register.clinic');
Route::post('/register-clinic', [App\Http\Controllers\PublicController::class, 'registerClinicSubmit'])->name('register.clinic.submit');

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('/admin/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');
Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('admin');

Route::get('antrian', [AntrianController::class, 'index'])->name('antrian');
Route::post('antrian/generate', [AntrianController::class, 'generate'])->name('antrian.generate');
Route::get('antrian/ticket/{id}', [AntrianController::class, 'ticket'])->name('antrian.ticket');


Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('news', NewsController::class);
    Route::resource('eselons', EselonController::class);
    // Route::resource('procedures', ProcedureController::class);
    Route::resource('polyclinics', PolyclinicController::class);
    Route::resource('medicines', MedicineController::class);
    Route::post('medicines/import', [MedicineController::class, 'import'])->name('medicines.import');
    Route::post('medicines/{id}/stock-opname', [MedicineController::class, 'stockOpname'])->name('medicines.stock_opname');
    Route::resource('doctors', DoctorController::class);
    Route::resource('therapists', \App\Http\Controllers\TherapistController::class);
    Route::resource('therapy-equipments', \App\Http\Controllers\TherapyEquipmentController::class);
    Route::resource('therapy-types', \App\Http\Controllers\TherapyTypeController::class);
    Route::get('reservations-history', [\App\Http\Controllers\ReservationController::class, 'history'])->name('reservations.history');
    Route::resource('reservations', \App\Http\Controllers\ReservationController::class);
    Route::post('reservations/{id}/confirm', [\App\Http\Controllers\ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{id}/cancel', [\App\Http\Controllers\ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('reservations/{id}/complete', [\App\Http\Controllers\ReservationController::class, 'complete'])->name('reservations.complete');
    Route::post('reservations/{id}/advance', [\App\Http\Controllers\ReservationController::class, 'advanceStage'])->name('reservations.advance');
    Route::get('api/patients/lookup', [\App\Http\Controllers\ReservationController::class, 'lookupPatient'])->name('api.patients.lookup');
    Route::get('api/therapists/check-availability', [\App\Http\Controllers\ReservationController::class, 'checkTherapistAvailability'])->name('api.therapists.check-availability');

    // Sesi Terapi Pasien (Berjenjang & Mendukung >1 Terapi per Hari)
    Route::resource('therapy-sessions', \App\Http\Controllers\TherapySessionController::class);
    Route::post('therapy-sessions/{id}/complete', [\App\Http\Controllers\TherapySessionController::class, 'complete'])->name('therapy-sessions.complete');
    Route::get('therapy-sessions/patient/{patient_id}/history', [\App\Http\Controllers\TherapySessionController::class, 'patientHistory'])->name('therapy-sessions.patient-history');
    Route::get('api/patients/{id}/last-stage', [\App\Http\Controllers\TherapySessionController::class, 'getPatientLastStage'])->name('api.patients.last-stage');

    // Route::resource('icds', IcdController::class);
    // Route::post('icds.import', [IcdController::class, 'import'])->name('icds.import');

    // Route::resource('icds9', Icd9Controller::class);
    // Route::post('icds9.import', [Icd9Controller::class, 'import'])->name('icds9.import');

    // Route::resource('icds10', Icd10Controller::class);
    // Route::post('icds10.import', [Icd10Controller::class, 'import'])->name('icds10.import');

    // Route::resource('poly-icds9', PolyIcd9Controller::class);
    // Route::resource('poly-icds10', PolyIcd10Controller::class);
    Route::resource('poly-doctors', PolyDoctorController::class);
    // Route::resource('poly-procedures', PolyProcedureController::class);
    Route::resource('doctor-schedules', \App\Http\Controllers\DoctorScheduleController::class);
    
    Route::resource('patients', PatientController::class);

    Route::get('api/icds/search', function(Illuminate\Http\Request $request) {
        $category = $request->category;
        $search = $request->q;
        
        $query = \App\Models\MasterIcd::query();
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('icd_code', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%");
            });
        }
        
        $icds = $query->limit(20)->get();
        
        $formatted = $icds->map(function($icd) {
            return [
                'id' => $icd->id,
                'text' => $icd->icd_code . ' - ' . $icd->name
            ];
        });
        
        return response()->json(['results' => $formatted]);
    })->name('api.icds.search');

    // API: Search master procedures for Select2 (Filtered by Polyclinic if poly_id provided)
    Route::get('api/procedures/search', function(Request $request) {
        $q = $request->get('q', '');
        $polyId = $request->get('poly_id');
        
        $query = \App\Models\MasterProcedure::query();
        
        // If we want to show all but filter by poly if available
        // For now, let's just allow all procedures to be searchable to avoid "not found" issues
        // But we can still filter if we want strictness. 
        // The user says "tidak bisa di pilih", which often means the list is empty.
        
        $procedures = $query->where(function($sub) use ($q) {
                $sub->where('name', 'ilike', "%{$q}%")
                    ->orWhere('procedure_code', 'ilike', "%{$q}%");
            })
            ->limit(20)
            ->get();
        
        $formatted = $procedures->map(function($proc) {
            return [
                'id' => $proc->id,
                'text' => $proc->procedure_code . ' - ' . $proc->name
            ];
        });
        
        return response()->json(['results' => $formatted]);
    })->name('api.procedures.search');

    // API: Search master medicines for Select2
    Route::get('api/medicines/search', function(Request $request) {
        $q = $request->get('q', '');
        
        $medicines = \App\Models\MasterMedicine::where('medicine_name', 'ilike', "%{$q}%")
            ->orWhere('medicine_international_name', 'ilike', "%{$q}%")
            ->limit(20)
            ->get();
        
        $formatted = $medicines->map(function($med) {
            return [
                'id' => $med->id,
                'text' => $med->medicine_name . ($med->medicine_international_name ? ' (' . $med->medicine_international_name . ')' : '')
            ];
        });
        
        return response()->json(['results' => $formatted]);
    })->name('api.medicines.search');

    Route::get('api/polyclinics/{id}/doctors', function($id) {
        $dayOfWeek = now()->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        
        $schedules = \App\Models\MasterDoctorSchedule::with('doctor')
            ->where('poly_id', $id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();
            
        $doctors = $schedules->map(function($schedule) {
            $doctor = $schedule->doctor;
            if ($doctor) {
                // Return doctor with schedule formatted
                return [
                    'id' => $doctor->id,
                    'full_name' => $doctor->full_name . ' (' . \Carbon\Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($schedule->end_time)->format('H:i') . ')'
                ];
            }
            return null;
        })->filter()->values();

        return response()->json($doctors);
    })->name('api.poly.doctors');

    Route::resource('registrasi', RegistrasiController::class);
    Route::post('registrasi/check', [RegistrasiController::class, 'check'])->name('registrasi.check');
    Route::post('registrasi/create', [RegistrasiController::class, 'store'])->name('registrasi.create.post');
    Route::get('registrasi/queue/{patient_id}', [RegistrasiController::class, 'queueForm'])->name('registrasi.queue');
    Route::post('registrasi/queue/{patient_id}', [RegistrasiController::class, 'queueStore'])->name('registrasi.queue.store');
    
    Route::get('admin/antrian', [AntrianController::class, 'dashboard'])->name('admin.antrian.dashboard');
    Route::post('admin/antrian/{id}/call', [AntrianController::class, 'call'])->name('admin.antrian.call');

    // Legacy Poliklinik (Skrining Pasien / Anamnesa Perawat - Tidak digunakan di skema Sport Therapist)
    // Route::get('anamnesis', [NurseAnamnesisController::class, 'index'])->name('anamnesis.index');
    // Route::get('anamnesis/{queue_id}/process', [NurseAnamnesisController::class, 'process'])->name('anamnesis.process');
    // Route::post('anamnesis/{queue_id}/process', [NurseAnamnesisController::class, 'store'])->name('anamnesis.store');

    Route::get('doctor-exam', [\App\Http\Controllers\TherapySessionController::class, 'index'])->name('doctor-exam.index');
    Route::get('doctor-exam/{queue_id}/process', [\App\Http\Controllers\TherapySessionController::class, 'show'])->name('doctor-exam.process');
    Route::post('doctor-exam/{queue_id}/process', [\App\Http\Controllers\TherapySessionController::class, 'complete'])->name('doctor-exam.store');

    Route::get('cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::get('cashier/{id}/process', [CashierController::class, 'process'])->name('cashier.process');
    Route::post('cashier/{id}/process', [CashierController::class, 'store'])->name('cashier.store');
    Route::get('cashier/{id}/receipt', [CashierController::class, 'receipt'])->name('cashier.receipt');

    Route::get('pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');
    Route::get('pharmacy/{id}/process', [PharmacyController::class, 'process'])->name('pharmacy.process');
    Route::post('pharmacy/{id}/process', [PharmacyController::class, 'store'])->name('pharmacy.store');

    // Reports
    Route::get('reports/medical-records', [\App\Http\Controllers\MedicalRecordReportController::class, 'index'])->name('reports.medical_records.index');
    Route::get('reports/medical-records/{id}', [\App\Http\Controllers\MedicalRecordReportController::class, 'show'])->name('reports.medical_records.show');

    // ==========================================
    // SAAS ADMINISTRATOR PLATFORM ROUTES
    // ==========================================
    Route::prefix('admin/saas')->name('saas.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\SaasDashboardController::class, 'index'])->name('dashboard');
        
        // Tenant Clinics Management
        Route::get('clinics', [\App\Http\Controllers\SaasClinicController::class, 'index'])->name('clinics.index');
        Route::get('clinics/create', [\App\Http\Controllers\SaasClinicController::class, 'create'])->name('clinics.create');
        Route::post('clinics', [\App\Http\Controllers\SaasClinicController::class, 'store'])->name('clinics.store');
        Route::get('clinics/{id}/edit', [\App\Http\Controllers\SaasClinicController::class, 'edit'])->name('clinics.edit');
        Route::put('clinics/{id}', [\App\Http\Controllers\SaasClinicController::class, 'update'])->name('clinics.update');
        Route::delete('clinics/{id}', [\App\Http\Controllers\SaasClinicController::class, 'destroy'])->name('clinics.destroy');
        Route::get('clinics/{id}/impersonate', [\App\Http\Controllers\SaasClinicController::class, 'impersonate'])->name('clinics.impersonate');
        Route::get('exit-impersonation', [\App\Http\Controllers\SaasClinicController::class, 'exitImpersonation'])->name('clinics.exit-impersonation');

        // Subscription Plans
        Route::get('plans', [\App\Http\Controllers\SaasPlanController::class, 'index'])->name('plans.index');
        Route::post('plans', [\App\Http\Controllers\SaasPlanController::class, 'store'])->name('plans.store');
        Route::put('plans/{id}', [\App\Http\Controllers\SaasPlanController::class, 'update'])->name('plans.update');

        // CMS Landing Page (Bagian Luar Website)
        Route::get('landing-page', [\App\Http\Controllers\SaasCmsController::class, 'index'])->name('cms.index');
        Route::post('landing-page/hero', [\App\Http\Controllers\SaasCmsController::class, 'updateHero'])->name('cms.hero');
        Route::post('landing-page/features', [\App\Http\Controllers\SaasCmsController::class, 'updateFeatures'])->name('cms.features');
        Route::post('landing-page/contact', [\App\Http\Controllers\SaasCmsController::class, 'updateContact'])->name('cms.contact');
    });
});
