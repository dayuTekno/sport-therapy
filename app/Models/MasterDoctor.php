<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDoctor extends Model
{
    protected $table = 'master_doctors';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];    
}
