<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEselon extends Model
{
    protected $table = 'master_eselons';

    protected $guarded = [];    

    public function queues()
    {
        return $this->hasMany(Antrian::class, 'eselon_id');
    }
}
