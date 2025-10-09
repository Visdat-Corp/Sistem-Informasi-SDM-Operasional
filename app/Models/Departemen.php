<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';
    protected $fillable = ['nama_departemen'];
    protected $primaryKey = 'id_departemen';

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'id_departemen');
    }

    public function posisis()
    {
        return $this->hasMany(Posisi::class, 'id_departemen');
    }
}
