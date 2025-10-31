<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\JamKerja;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all karyawan
        $karyawans = Karyawan::all();
        
        if ($karyawans->isEmpty()) {
            $this->command->warn('No karyawan found. Please run KaryawanSeeder first.');
            return;
        }

        // Get jam kerja settings
        $jamKerja = JamKerja::first();
        if (!$jamKerja) {
            // Create default jam kerja if not exists
            $jamKerja = JamKerja::create([
                'jam_masuk_normal' => '08:00:00',
                'jam_keluar_normal' => '17:00:00',
                'toleransi_keterlambatan' => 15,
                'toleransi_pulang_cepat' => 15,
                'jam_lembur' => '19:00:00',
            ]);
        }

        // Create sample coordinates (around office location)
        $locations = [
            ['lat' => -5.15814278, 'lng' => 119.47840512], // Kantor Pusat
            ['lat' => -5.15820000, 'lng' => 119.47845000], // Near office
            ['lat' => -5.15810000, 'lng' => 119.47838000], // Near office
        ];

        // Generate 20 absensi records for working days only (exclude Saturday & Sunday)
        $startDate = Carbon::now()->subDays(15); // Extended to 15 days to ensure enough working days
        $recordCount = 0;
        $maxDays = 30; // Maximum days to check

        for ($day = 0; $day < $maxDays && $recordCount < 20; $day++) {
            $date = $startDate->copy()->addDays($day);
            
            // Skip weekends (Saturday = 6, Sunday = 0) - HARI LIBUR DEFAULT
            if ($date->isWeekend()) {
                $this->command->comment("Skipping {$date->format('Y-m-d')} ({$date->format('l')}) - Weekend/Holiday");
                continue;
            }

            // Get 2-3 random karyawan for this day
            $dailyKaryawan = $karyawans->random(min(3, $karyawans->count()));
            
            foreach ($dailyKaryawan as $karyawan) {
                if ($recordCount >= 20) {
                    break;
                }

                // Random location from the list
                $location = $locations[array_rand($locations)];
                
                // Generate random check-in time (07:30 - 09:00)
                $checkInHour = rand(7, 8);
                $checkInMinute = rand(0, 59);
                if ($checkInHour === 9) {
                    $checkInMinute = 0;
                }
                $jamMasuk = sprintf('%02d:%02d:00', $checkInHour, $checkInMinute);
                
                // Generate random check-out time (16:30 - 18:00)
                $checkOutHour = rand(16, 17);
                $checkOutMinute = rand(0, 59);
                if ($checkOutHour === 18) {
                    $checkOutMinute = 0;
                }
                $jamKeluar = sprintf('%02d:%02d:00', $checkOutHour, $checkOutMinute);
                
                // Determine status based on time
                $status = 'hadir';
                $menitKeterlambatan = 0;
                
                // Check if late
                $jamMasukNormal = Carbon::createFromFormat('H:i:s', $jamKerja->jam_masuk_normal);
                $jamMasukActual = Carbon::createFromFormat('H:i:s', $jamMasuk);
                $toleransi = $jamKerja->toleransi_keterlambatan;
                
                if ($jamMasukActual->greaterThan($jamMasukNormal->copy()->addMinutes($toleransi))) {
                    $status = 'terlambat';
                    $menitKeterlambatan = $jamMasukNormal->diffInMinutes($jamMasukActual) - $toleransi;
                }
                
                // Random chance for lembur (20%)
                $isLembur = rand(1, 100) <= 20;
                if ($isLembur) {
                    $status = 'lembur';
                    // Extend checkout time for lembur
                    $checkOutHour = rand(19, 21);
                    $jamKeluar = sprintf('%02d:%02d:00', $checkOutHour, rand(0, 59));
                }

                Absensi::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal_absen' => $date->toDateString(),
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                    'lokasi_absen_masuk' => $location['lat'] . ',' . $location['lng'],
                    'lokasi_absen_keluar' => $location['lat'] . ',' . $location['lng'],
                    'status' => $status,
                    'menit_keterlambatan' => $menitKeterlambatan,
                    'is_lembur' => $isLembur,
                    'id_jamKerja' => $jamKerja->id_jamKerja,
                ]);

                $recordCount++;
                
                if ($recordCount >= 20) {
                    break;
                }
            }
        }

        $this->command->info("Successfully created {$recordCount} absensi records.");
    }
}
