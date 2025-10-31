<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPengecualian extends Model
{
    protected $table = 'jadwal_pengecualian';
    protected $fillable = ['tanggal', 'nama_hari_libur', 'keterangan', 'jenis'];
    protected $primaryKey = 'id_jadwal_pengecualian';

    /**
     * Check if a specific date is an exception day (holiday)
     */
    public static function isHoliday($date)
    {
        return self::where('tanggal', $date)->exists();
    }

    /**
     * Get holiday name for a specific date
     */
    public static function getHolidayName($date)
    {
        $holiday = self::where('tanggal', $date)->first();
        return $holiday ? $holiday->nama_hari_libur : null;
    }
}
