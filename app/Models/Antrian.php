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

    public function eselon()
    {
        return $this->belongsTo(MasterEselon::class, 'eselon_id');
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class, 'queue_id');
    }

    public function getQueueCodeAttribute()
    {
        $prefix = strtoupper(substr($this->poly->polyclinic_code ?? 'A', 0, 1));
        return $prefix . '-' . str_pad($this->queue_number, 3, '0', STR_PAD_LEFT);
    }
}
