<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class TherapyType extends Model
{
    use BelongsToClinic;

    protected $table = 'therapy_types';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'stage_order' => 'integer',
    ];

    public function sessions()
    {
        return $this->hasMany(TherapySession::class, 'therapy_type_id');
    }
}
