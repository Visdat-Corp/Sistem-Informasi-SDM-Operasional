<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    protected $table = 'jam_kerja';
    protected $fillable = ['jam_masuk_normal', 'jam_keluar_normal', 'toleransi_keterlambatan', 'toleransi_pulang_cepat', 'jam_lembur', 'total_jam'];
    protected $primaryKey = 'id_jamKerja';

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_jamKerja');
    }
}
