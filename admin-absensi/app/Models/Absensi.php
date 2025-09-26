<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = ['id_karyawan', 'tanggal_absen', 'jam_masuk', 'jam_keluar', 'lokasi_absen_masuk', 'lokasi_absen_keluar', 'foto_masuk', 'foto_keluar', 'mode', 'keterangan', 'id_jamKerja'];
    protected $primaryKey = 'id_absensi';

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function jamKerja()
    {
        return $this->belongsTo(JamKerja::class, 'id_jamKerja');
    }
}
