<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClinicScope implements Scope
{
    /**
     * Terapkan scope isolasi data tenant berbasis clinic_id
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Jika user adalah saas_admin atau superadmin global (clinic_id null)
            if ($user->hasRole('saas_admin') || ($user->clinic_id === null && $user->hasRole('admin'))) {
                // Jika superadmin sedang meninjau klinik tertentu melalui session filter
                if (session()->has('active_clinic_id')) {
                    $builder->where($model->getTable() . '.clinic_id', session('active_clinic_id'));
                }
                return;
            }

            // Untuk admin dan staf klinik terapi, isolasi ketat sesuai clinic_id miliknya
            if ($user->clinic_id) {
                $builder->where($model->getTable() . '.clinic_id', $user->clinic_id);
            }
        }
    }
}
