<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'admins';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama_admin',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
