<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $table = 'queues';
    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(MasterPatients::class, 'patient_id');
    }

    public function poly()
    {
        return $this->belongsTo(MasterPolyclinic::class, 'poly_id');
    }

    public function doctor()
    {
        return $this->belongsTo(MasterDoctor::class, 'doctor_id');
    }
}
