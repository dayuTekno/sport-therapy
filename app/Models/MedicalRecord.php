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

    public function icd10()
    {
        return $this->belongsTo(MasterIcd::class, 'icd10_id');
    }

    public function icd9()
    {
        return $this->belongsTo(MasterIcd::class, 'icd9_id');
    }

    public function doctorIcd10()
    {
        return $this->belongsTo(MasterIcd::class, 'doctor_icd10_id');
    }

    public function doctorIcd9()
    {
        return $this->belongsTo(MasterIcd::class, 'doctor_icd9_id');
    }

    // Multi-ICD relationships
    public function diagnoses()
    {
        return $this->hasMany(MedicalRecordDiagnosis::class);
    }

    public function nurseDiagnoses()
    {
        return $this->hasMany(MedicalRecordDiagnosis::class)->where('source', 'nurse');
    }

    public function doctorDiagnoses()
    {
        return $this->hasMany(MedicalRecordDiagnosis::class)->where('source', 'doctor');
    }

    public function procedures()
    {
        return $this->hasMany(MedicalRecordProcedure::class);
    }

    public function nurseProcedures()
    {
        return $this->hasMany(MedicalRecordProcedure::class)->where('source', 'nurse');
    }

    public function doctorProcedures()
    {
        return $this->hasMany(MedicalRecordProcedure::class)->where('source', 'doctor');
    }

    public function treatments()
    {
        return $this->hasMany(MedicalRecordTreatment::class);
    }

    public function medicines()
    {
        return $this->hasMany(MedicalRecordMedicine::class);
    }
}
