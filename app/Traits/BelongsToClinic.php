<?php

namespace App\Traits;

use App\Models\Clinic;
use App\Scopes\ClinicScope;

trait BelongsToClinic
{
    /**
     * Boot the trait to attach global scope and auto-assign clinic_id
     */
    public static function bootBelongsToClinic(): void
    {
        static::addGlobalScope(new ClinicScope());

        static::creating(function ($model) {
            // Otomatis inject clinic_id saat record baru dibuat
            if (empty($model->clinic_id) && auth()->check()) {
                $user = auth()->user();
                if ($user->clinic_id) {
                    $model->clinic_id = $user->clinic_id;
                } elseif (session()->has('active_clinic_id')) {
                    $model->clinic_id = session('active_clinic_id');
                } else {
                    $model->clinic_id = 1; // Default tenant
                }
            } elseif (empty($model->clinic_id)) {
                $model->clinic_id = 1; // Default tenant untuk seeding / CLI
            }
        });
    }

    /**
     * Relasi ke tenant klinik
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
