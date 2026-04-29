<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordMedicine extends Model
{
    protected $table = 'medical_record_medicines';
    protected $guarded = [];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function medicine()
    {
        return $this->belongsTo(MasterMedicine::class, 'medicine_id');
    }
}
