<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterIcd extends Model
{
    protected $table = 'master_icds';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];    
}
