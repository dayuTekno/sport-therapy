<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $table = 'medical_records';
    protected $guarded = [];

    public function queue()
    {
        return $this->belongsTo(Antrian::class, 'queue_id');
    }

    public function patient()
    {
        return $this->belongsTo(MasterPatients::class, 'patient_id');
    }

    public function icd()
    {
        return $this->belongsTo(MasterIcd::class, 'icd_id');
    }
}
