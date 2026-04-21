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


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');;


Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('antrian', [AntrianController::class, 'index'])->name('antrian');
Route::get('antrian/update', [AntrianController::class, 'update'])->name('antrian-update');


Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('news', NewsController::class);
    Route::resource('eselons', EselonController::class);
    Route::resource('procedures', ProcedureController::class);
    Route::resource('polyclinics', PolyclinicController::class);
    Route::resource('medicines', MedicineController::class);
    Route::post('medicines.import', [MedicineController::class, 'import'])->name('medicines.import');

    // Route::resource('icds', IcdController::class);
    // Route::post('icds.import', [IcdController::class, 'import'])->name('icds.import');

    Route::resource('icds9', Icd9Controller::class);
    Route::post('icds9.import', [Icd9Controller::class, 'import'])->name('icds9.import');

    Route::resource('icds10', Icd10Controller::class);
    Route::post('icds10.import', [Icd10Controller::class, 'import'])->name('icds10.import');

    
    Route::resource('registrasi', RegistrasiController::class);
    Route::post('registrasi/check', [RegistrasiController::class, 'check'])->name('registrasi.check');
    Route::post('registrasi/create', [RegistrasiController::class, 'store'])->name('registrasi.store');
});
