<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');;


Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('antrian', [AntrianController::class, 'index'])->name('antrian');
Route::post('antrian/generate', [AntrianController::class, 'generate'])->name('antrian.generate');
Route::get('antrian/ticket/{id}', [AntrianController::class, 'ticket'])->name('antrian.ticket');


Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('news', NewsController::class);
    Route::resource('eselons', EselonController::class);
    Route::resource('procedures', ProcedureController::class);
    Route::resource('polyclinics', PolyclinicController::class);
    Route::resource('medicines', MedicineController::class);
    Route::resource('doctors', DoctorController::class);
    Route::post('medicines.import', [MedicineController::class, 'import'])->name('medicines.import');

    // Route::resource('icds', IcdController::class);
    // Route::post('icds.import', [IcdController::class, 'import'])->name('icds.import');

    Route::resource('icds9', Icd9Controller::class);
    Route::post('icds9.import', [Icd9Controller::class, 'import'])->name('icds9.import');

    Route::resource('icds10', Icd10Controller::class);
    Route::post('icds10.import', [Icd10Controller::class, 'import'])->name('icds10.import');

    Route::resource('poly-icds9', PolyIcd9Controller::class);
    Route::resource('poly-icds10', PolyIcd10Controller::class);
    Route::resource('poly-doctors', PolyDoctorController::class);
    Route::resource('poly-procedures', PolyProcedureController::class);
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
                $q->where('icd_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
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
    Route::post('registrasi/create', [RegistrasiController::class, 'store'])->name('registrasi.store');
    Route::get('registrasi/queue/{patient_id}', [RegistrasiController::class, 'queueForm'])->name('registrasi.queue');
    Route::post('registrasi/queue/{patient_id}', [RegistrasiController::class, 'queueStore'])->name('registrasi.queue.store');
    
    Route::get('admin/antrian', [AntrianController::class, 'dashboard'])->name('admin.antrian.dashboard');
    Route::post('admin/antrian/{id}/call', [AntrianController::class, 'call'])->name('admin.antrian.call');

    Route::get('anamnesis', [NurseAnamnesisController::class, 'index'])->name('anamnesis.index');
    Route::get('anamnesis/{queue_id}/process', [NurseAnamnesisController::class, 'process'])->name('anamnesis.process');
    Route::post('anamnesis/{queue_id}/process', [NurseAnamnesisController::class, 'store'])->name('anamnesis.store');
});
