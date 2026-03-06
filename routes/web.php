<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\AntrianController;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');;


Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('antrian', [AntrianController::class, 'index'])->name('antrian');
Route::get('antrian/update', [AntrianController::class, 'update'])->name('antrian-update');


Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('news', NewsController::class);


    Route::resource('registrasi', RegistrasiController::class);
    Route::get('registrasi/check/{nik}', [RegistrasiController::class, 'check'])->name('registrasi.check');
    Route::post('registrasi/create', [RegistrasiController::class, 'store'])->name('registrasi.store');


    Route::get('pengaju', [UserController::class, 'listPengaju'])->name('listpengaju');
    Route::get('pengaju/{user}', [UserController::class, 'showPengaju'])->name('pengaju.show');
    Route::get('pengaduan', [PengaduanController::class, 'index'])->name('pengaduan');
    Route::get('perizinan', [PerizinanController::class, 'index'])->name('perizinan');
    Route::get('perizinan/{id}', [PerizinanController::class, 'detail'])->name('perizinan.show');

});
