<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPolyclinic extends Model
{
    protected $table = 'master_polyclinics';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function icds()
    {
        return $this->belongsToMany(MasterIcd::class, 'master_poly_icds', 'polyclinic_id', 'icd_id');
    }

    public function icds9()
    {
        return $this->belongsToMany(MasterIcd::class, 'master_poly_icds', 'polyclinic_id', 'icd_id')
                    ->where('category', '9');
    }

    public function icds10()
    {
        return $this->belongsToMany(MasterIcd::class, 'master_poly_icds', 'polyclinic_id', 'icd_id')
                    ->where('category', '10');
    }

    public function doctors()
    {
        return $this->belongsToMany(MasterDoctor::class, 'master_doctor_polyclinics', 'polyclinic_id', 'doctor_id');
    }

    public function procedures()
    {
        return $this->belongsToMany(MasterProcedure::class, 'master_procedure_polyclinics', 'polyclinic_id', 'procedure_id');
    }    
}
