<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class TherapyEquipment extends Model
{
    use BelongsToClinic;

    protected $table = 'therapy_equipments';

    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
        'stock' => 'integer',
    ];
}
