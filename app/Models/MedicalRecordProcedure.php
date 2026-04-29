<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordProcedure extends Model
{
    protected $table = 'medical_record_procedures';
    protected $guarded = [];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function icd()
    {
        return $this->belongsTo(MasterIcd::class, 'icd_id');
    }
}
