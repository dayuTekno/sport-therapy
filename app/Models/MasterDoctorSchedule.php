<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDoctorSchedule extends Model
{
    protected $table = 'master_doctor_schedules';
    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo(MasterDoctor::class, 'doctor_id');
    }

    public function polyclinic()
    {
        return $this->belongsTo(MasterPolyclinic::class, 'poly_id');
    }
}
