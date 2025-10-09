<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = ['id_karyawan', 'tanggal_absen', 'jam_masuk', 'jam_keluar', 'lokasi_absen_masuk', 'lokasi_absen_keluar', 'foto_masuk', 'foto_keluar', 'keterangan', 'id_jamKerja', 'status', 'menit_keterlambatan', 'menit_pulang_cepat', 'is_lembur'];
    protected $primaryKey = 'id_absensi';

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function jamKerja()
    {
        return $this->belongsTo(JamKerja::class, 'id_jamKerja');
    }

    /**
     * Check if attendance is on time (hadir)
     */
    public function isHadir()
    {
        if (!$this->jam_masuk || !$this->jam_keluar || !$this->jamKerja) {
            return false;
        }

        $jamMasukNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_masuk_normal);
        $jamMasukActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_masuk);
        $toleransi = $this->jamKerja->toleransi_keterlambatan ?? 0;
        $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

        $isOnTimeIn = $jamMasukActual->lte($jamMasukNormalWithTolerance);

        $jamKeluarNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_keluar_normal);
        $jamKeluarActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_keluar);

        $isOnTimeOut = $jamKeluarActual->gte($jamKeluarNormal);

        return $isOnTimeIn && $isOnTimeOut;
    }

    /**
     * Check if attendance is late (terlambat)
     */
    public function isTerlambat()
    {
        if (!$this->jam_masuk || !$this->jam_keluar || !$this->jamKerja) {
            return false;
        }

        $jamMasukNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_masuk_normal);
        $jamMasukActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_masuk);
        $toleransi = $this->jamKerja->toleransi_keterlambatan ?? 0;
        $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

        $isLateIn = $jamMasukActual->gt($jamMasukNormalWithTolerance);

        $jamKeluarNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_keluar_normal);
        $jamKeluarActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_keluar);

        $isOnTimeOut = $jamKeluarActual->gte($jamKeluarNormal);

        return $isLateIn && $isOnTimeOut;
    }

    /**
     * Check if attendance is early departure (pulang cepat)
     */
    public function isPulangCepat()
    {
        if (!$this->jam_masuk || !$this->jam_keluar || !$this->jamKerja) {
            return false;
        }

        $jamMasukNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_masuk_normal);
        $jamMasukActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_masuk);
        $toleransi = $this->jamKerja->toleransi_keterlambatan ?? 0;
        $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

        $isOnTimeIn = $jamMasukActual->lte($jamMasukNormalWithTolerance);

        $jamKeluarNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_keluar_normal);
        $jamKeluarActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_keluar);
        $toleransiPulangCepat = $this->jamKerja->toleransi_pulang_cepat ?? 0;
        $jamKeluarNormalWithTolerance = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

        $isEarlyOut = $jamKeluarActual->lt($jamKeluarNormalWithTolerance);

        return $isOnTimeIn && $isEarlyOut;
    }

    /**
     * Check if attendance is inconsistent (late in and early out)
     */
    public function isTidakKonsisten()
    {
        if (!$this->jam_masuk || !$this->jam_keluar || !$this->jamKerja) {
            return false;
        }

        $jamMasukNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_masuk_normal);
        $jamMasukActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_masuk);
        $toleransi = $this->jamKerja->toleransi_keterlambatan ?? 0;
        $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

        $isLateIn = $jamMasukActual->gt($jamMasukNormalWithTolerance);

        $jamKeluarNormal = \Carbon\Carbon::createFromFormat('H:i:s', $this->jamKerja->jam_keluar_normal);
        $jamKeluarActual = \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_keluar);
        $toleransiPulangCepat = $this->jamKerja->toleransi_pulang_cepat ?? 0;
        $jamKeluarNormalWithTolerance = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

        $isEarlyOut = $jamKeluarActual->lt($jamKeluarNormalWithTolerance);

        return $isLateIn && $isEarlyOut;
    }

    /**
     * Check if attendance is absent (tidak hadir)
     */
    public function isTidakHadir()
    {
        return !$this->jam_masuk;
    }
}
