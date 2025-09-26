<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
    protected $table = 'karyawan';
    protected $fillable = ['id_departemen', 'id_posisi', 'nama_karyawan', 'username_karyawan', 'email_karyawan', 'password_karyawan', 'status'];
    protected $primaryKey = 'id_karyawan';
    protected $hidden = ['password_karyawan'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'id_posisi');
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
}
