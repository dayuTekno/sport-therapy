<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class MasterReferral extends Model
{
    use BelongsToClinic;

    protected $table = 'master_referrals';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_amount' => 'decimal:2',
    ];

    /**
     * Pasien yang terafiliasi dengan referal ini
     */
    public function patients()
    {
        return $this->hasMany(MasterPatients::class, 'referral_id');
    }

    /**
     * Seluruh sesi terapi yang dijalani oleh pasien terafiliasi dengan referal ini
     */
    public function therapySessions()
    {
        return $this->hasManyThrough(
            TherapySession::class,
            MasterPatients::class,
            'referral_id', // Foreign key on master_patients table...
            'patient_id',  // Foreign key on therapy_sessions table...
            'id',          // Local key on master_referrals table...
            'id'           // Local key on master_patients table...
        );
    }

    /**
     * Sesi terapi berstatus selesai (completed) untuk acuan perhitungan komisi
     */
    public function completedTherapySessions()
    {
        return $this->therapySessions()->where('therapy_sessions.status', 'completed');
    }
}
