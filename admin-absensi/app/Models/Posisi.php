<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    protected $table = 'posisi';
    protected $fillable = ['nama_posisi', 'id_departemen'];
    protected $primaryKey = 'id_posisi';

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }
}
