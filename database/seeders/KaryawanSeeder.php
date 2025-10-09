<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Departemen;
use App\Models\Posisi;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        // Delete all existing absensi and karyawan data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Absensi::truncate();
        Karyawan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get first department and position for test data
        $departemen = Departemen::first();
        $posisi = Posisi::first();

        // Create test employees
        $testEmployees = [
            [
                'nama_karyawan' => 'Test Employee 1',
                'username_karyawan' => 'test1',
                'email_karyawan' => 'test1@company.com',
                'password_karyawan' => 'password123', // Will be hashed by mutator
                'id_departemen' => $departemen ? $departemen->id_departemen : null,
                'id_posisi' => $posisi ? $posisi->id_posisi : null,
                'status' => 'aktif'
            ],
            [
                'nama_karyawan' => 'Test Employee 2',
                'username_karyawan' => 'test2',
                'email_karyawan' => 'test2@company.com',
                'password_karyawan' => 'password123', // Will be hashed by mutator
                'id_departemen' => $departemen ? $departemen->id_departemen : null,
                'id_posisi' => $posisi ? $posisi->id_posisi : null,
                'status' => 'aktif'
            ],
            [
                'nama_karyawan' => 'John Doe',
                'username_karyawan' => 'john.doe',
                'email_karyawan' => 'john.doe@company.com',
                'password_karyawan' => 'password123', // Will be hashed by mutator
                'id_departemen' => $departemen ? $departemen->id_departemen : null,
                'id_posisi' => $posisi ? $posisi->id_posisi : null,
                'status' => 'aktif'
            ],
            [
                'nama_karyawan' => 'Jane Smith',
                'username_karyawan' => 'jane.smith',
                'email_karyawan' => 'jane.smith@company.com',
                'password_karyawan' => 'password123', // Will be hashed by mutator
                'id_departemen' => $departemen ? $departemen->id_departemen : null,
                'id_posisi' => $posisi ? $posisi->id_posisi : null,
                'status' => 'aktif'
            ]
        ];

        foreach ($testEmployees as $employee) {
            Karyawan::create($employee);
        }

        echo "Created " . count($testEmployees) . " test employees with username/password: test1/password123, test2/password123, etc.\n";
    }
}
