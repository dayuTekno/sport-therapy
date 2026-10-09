<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class Therapist extends Model
{
    use BelongsToClinic;

    protected $table = 'therapists';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'therapist_id');
    }

    public function sessions()
    {
        return $this->hasMany(TherapySession::class, 'therapist_id');
    }
}
