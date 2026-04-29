<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // 👈 INI KUNCINYA

class MasterPatients extends Model
{
    protected $table = 'master_patients';

    protected $guarded = [];    

    public function eselon()
    {
        return $this->belongsTo(MasterEselon::class, 'eselon_id');
    }
}
