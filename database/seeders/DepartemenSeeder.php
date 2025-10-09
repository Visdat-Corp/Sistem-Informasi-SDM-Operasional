<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departemen;
use App\Models\Posisi;
use App\Models\LokasiKerja;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create departments
        $itDept = Departemen::create([
            'nama_departemen' => 'IT'
        ]);

        $networkDept = Departemen::create([
            'nama_departemen' => 'Jaringan'
        ]);

        // Create positions for IT department
        Posisi::create([
            'id_departemen' => $itDept->id_departemen,
            'nama_posisi' => 'Programmer'
        ]);

        Posisi::create([
            'id_departemen' => $itDept->id_departemen,
            'nama_posisi' => 'System Analyst'
        ]);

        // Create positions for Network department
        Posisi::create([
            'id_departemen' => $networkDept->id_departemen,
            'nama_posisi' => 'Network Administrator'
        ]);

        Posisi::create([
            'id_departemen' => $networkDept->id_departemen,
            'nama_posisi' => 'Network Engineer'
        ]);

        // Create work locations
        LokasiKerja::create([
            'lokasi_kerja' => 'Kantor Pusat',
            'latitude' => -5.15814278,
            'longitude' => 119.47840512,
            'radius' => 100
        ]);

        LokasiKerja::create([
            'lokasi_kerja' => 'Kantor Cabang',
            'latitude' => -5.16000000,
            'longitude' => 119.48000000,
            'radius' => 150
        ]);
    }
}
