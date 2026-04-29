<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProcedure extends Model
{
    protected $table = 'master_procedures';

    protected $guarded = [];

    public function polyclinics()
    {
        return $this->belongsToMany(MasterPolyclinic::class, 'master_procedure_polyclinics', 'procedure_id', 'polyclinic_id');
    }
}
