<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'karyawan';
    protected $fillable = ['id_departemen', 'id_posisi', 'id_lokasi_kerja', 'nama_karyawan', 'username_karyawan', 'email_karyawan', 'password_karyawan', 'status'];
    protected $primaryKey = 'id_karyawan';
    protected $hidden = ['password_karyawan'];
    
    /**
     * Get the name of the unique identifier for the user.
     * Must map to integer primary key used by sessions.user_id.
     */
    public function getAuthIdentifierName()
    {
        return 'id_karyawan';
    }

    /**
     * Get the unique identifier for the user.
     * Return integer primary key value.
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute('id_karyawan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'id_posisi');
    }

    public function lokasiKerja()
    {
        return $this->belongsTo(LokasiKerja::class, 'id_lokasi_kerja', 'id_lokasi');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_karyawan');
    }

    public function setPasswordKaryawanAttribute($value)
    {
        $this->attributes['password_karyawan'] = Hash::make($value);
    }

    public function getAuthPassword()
    {
        return $this->password_karyawan;
    }

    public function getAuthPasswordName()
    {
        return 'password_karyawan';
    }
}
