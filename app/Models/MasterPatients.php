<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class MasterPatients extends Model
{
    use BelongsToClinic;

    protected $table = 'master_patients';

    protected $guarded = [];    

    public function eselon()
    {
        return $this->belongsTo(MasterEselon::class, 'eselon_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'patient_id');
    }

    public function therapySessions()
    {
        return $this->hasMany(TherapySession::class, 'patient_id')->orderBy('scheduled_at', 'desc')->orderBy('daily_session_order', 'desc');
    }
}
