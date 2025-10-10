<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanAbsensiHarianExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $tanggal;
    protected $search;

    public function __construct($tanggal, $search = null)
    {
        $this->tanggal = $tanggal;
        $this->search = $search;
    }

    public function array(): array
    {
        $date = Carbon::parse($this->tanggal);

        $query = Karyawan::with(['absensis' => function($q) use ($date) {
            $q->where('tanggal_absen', $date->toDateString())
              ->with('jamKerja');
        }])->where('status', 'aktif');

        if ($this->search) {
            $query->where('nama_karyawan', 'like', '%' . $this->search . '%');
        }

        $karyawans = $query->get();

        $data = [];

        // Title row
        $titleRow = ['', 'Laporan Absensi Harian ' . $date->format('d-m-Y')];
        $data[] = $titleRow;

        // Header row
        $data[] = ['Nama Karyawan', 'Jam Masuk', 'Jam Keluar', 'Total Jam Kerja', 'Status'];

        // Data rows
        foreach ($karyawans as $karyawan) {
            $absen = $karyawan->absensis->first();
            if ($absen) {
                $status = $this->determineStatus($absen);
                $totalJam = $this->calculateTotalJam($absen);
                $data[] = [
                    $karyawan->nama_karyawan,
                    $absen->jam_masuk,
                    $absen->jam_keluar,
                    $totalJam,
                    $status
                ];
            } else {
                $data[] = [
                    $karyawan->nama_karyawan,
                    '-',
                    '-',
                    '0 jam',
                    'Tidak Hadir'
                ];
            }
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge title cells
                $sheet->mergeCells('B1:F1');
            },
        ];
    }

    private function determineStatus($absen)
    {
        // If is_lembur is true, set status to lembur
        if ($absen->is_lembur) {
            return 'Lembur';
        }

        // If status is already set to manual statuses, return it
        if ($absen->status && in_array(strtolower($absen->status), ['izin', 'sakit', 'cuti', 'dinas luar'])) {
            return ucfirst($absen->status);
        }

        if (!$absen->jam_masuk) {
            return 'Tidak Hadir';
        }

        if ($absen->jamKerja) {
            $jamMasukNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_masuk_normal);
            $jamMasukActual = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);

            // Tambahkan toleransi keterlambatan
            $toleransi = $absen->jamKerja->toleransi_keterlambatan ?? 0;
            $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

            $isLateIn = $jamMasukActual->gt($jamMasukNormalWithTolerance);

            if ($isLateIn) {
                // Calculate lateness minutes beyond tolerance
                $menitKeterlambatan = $jamMasukNormal->diffInMinutes($jamMasukActual, false) - $toleransi;
                $absen->update(['menit_keterlambatan' => $menitKeterlambatan]);
            }

            if (!$absen->jam_keluar) {
                // No check-out, status based on check-in only
                return $isLateIn ? 'Terlambat' : 'Hadir';
            }

            // Has check-out
            $jamKeluarNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_keluar_normal);
            $jamKeluarActual = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);
            $toleransiPulangCepat = $absen->jamKerja->toleransi_pulang_cepat ?? 0;
            $jamKeluarNormalWithTolerance = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

            $isEarlyOut = $jamKeluarActual->lt($jamKeluarNormalWithTolerance);

            if ($isLateIn && $isEarlyOut) {
                return 'Tidak Konsisten';
            } elseif (!$isLateIn && $isEarlyOut) {
                return 'Pulang Cepat';
            } elseif ($isLateIn && !$isEarlyOut) {
                return 'Terlambat';
            } else {
                return 'Hadir';
            }
        }

        // Default jika tidak ada jam kerja
        return 'Hadir';
    }

    private function calculateTotalJam($absen)
    {
        if (!$absen->jam_masuk || !$absen->jam_keluar) {
            return '0 jam';
        }

        $jamMasuk = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);
        $jamKeluar = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);

        $minutes = $jamMasuk->diffInMinutes($jamKeluar, false);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return $hours . ' jam ' . $remainingMinutes . ' menit';
        } else {
            return $hours . ' jam';
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Title row styling
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2196F3'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Header row styling
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data rows styling
        $sheet->getStyle('A3:E1000')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Borders for all
        $sheet->getStyle('A1:E1000')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Employee name
            'B' => 15, // Jam Masuk
            'C' => 15, // Jam Keluar
            'D' => 20, // Total Jam Kerja
            'E' => 15, // Status
        ];
    }

    public function title(): string
    {
        $date = Carbon::parse($this->tanggal);
        return 'Laporan Absensi Harian ' . $date->format('d-m-Y');
    }

    private function getColumnLetter($columnNumber)
    {
        $columnLetter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $columnLetter = chr(65 + ($columnNumber % 26)) . $columnLetter;
            $columnNumber = (int)($columnNumber / 26);
        }
        return $columnLetter;
    }
}
