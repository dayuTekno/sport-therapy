<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // 👈 INI KUNCINYA
use App\Models\User;

class DataRegister extends Model
{
    protected $table = 'data_registers';

    protected $fillable = [
        'user_id',
        'kota_perusahaan',
        'nama_perusahaan',
        'bidang_usaha',
        'alamat',
        'no_telp_kantor',
        'keterangan',
        'npwp',
        'tenaga_kerja',
        'jml_kapal',
        'kantor_cabang',
        'penanggungjawab',
        'jabatan_penanggungjawab',
        'alamat_rumah_penanggungjawab',
        'no_telp_penanggungjawab',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
