<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordTreatment extends Model
{
    protected $table = 'medical_record_treatments';
    protected $guarded = [];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function procedure()
    {
        return $this->belongsTo(MasterProcedure::class, 'procedure_id');
    }
}
