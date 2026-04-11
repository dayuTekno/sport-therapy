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
    
    Route::resource('registrasi', RegistrasiController::class);
    Route::post('registrasi/check', [RegistrasiController::class, 'check'])->name('registrasi.check');
    Route::post('registrasi/create', [RegistrasiController::class, 'store'])->name('registrasi.store');
});
