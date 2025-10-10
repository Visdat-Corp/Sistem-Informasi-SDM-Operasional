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

class LaporanAbsensiMingguanExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $minggu;
    protected $search;

    public function __construct($minggu, $search = null)
    {
        $this->minggu = $minggu;
        $this->search = $search;
    }

    public function array(): array
    {
        list($year, $week) = explode('-W', $this->minggu);
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = Carbon::now()->setISODate($year, $week)->endOfWeek();

        $query = Karyawan::with(['absensis' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
              ->with('jamKerja');
        }])->where('status', 'aktif');

        if ($this->search) {
            $query->where('nama_karyawan', 'like', '%' . $this->search . '%');
        }

        $karyawans = $query->get();

        $data = [];

        // Title row
        $titleRow = ['', '', 'Laporan Absensi Mingguan ' . $startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y')];
        for ($i = 3; $i <= 9; $i++) {
            $titleRow[] = '';
        }
        $data[] = $titleRow;

        // Day abbreviation row
        $dayRow = ['Nama Karyawan', 'ID Karyawan'];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayAbbr = $currentDate->locale('id')->isoFormat('ddd');
            $dayRow[] = $dayAbbr;
            $currentDate->addDay();
        }
        $data[] = $dayRow;

        // Date number row
        $dateRow = ['', ''];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateRow[] = $currentDate->day;
            $currentDate->addDay();
        }
        $data[] = $dateRow;

        // Data rows
        foreach ($karyawans as $karyawan) {
            $row = [$karyawan->nama_karyawan, $karyawan->id_karyawan];

            // Create a map of date to absensi
            $absensiMap = [];
            foreach ($karyawan->absensis as $absen) {
                $day = Carbon::parse($absen->tanggal_absen)->day;
                $absensiMap[$day] = $absen;
            }

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $day = $currentDate->day;
                if (isset($absensiMap[$day])) {
                    $status = $this->determineStatus($absensiMap[$day]);
                } else {
                    $status = 'Tidak Hadir';
                }
                $row[] = $status;
                $currentDate->addDay();
            }

            $data[] = $row;
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge title cells
                $sheet->mergeCells('C1:I1');
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

    public function styles(Worksheet $sheet)
    {
        // Title row styling
        $sheet->getStyle('A1:I1')->applyFromArray([
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

        // Header rows styling (rows 2 and 3)
        $sheet->getStyle('A2:I3')->applyFromArray([
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

        // Employee columns styling (merged)
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('B2:B3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);

        // Data rows styling
        $sheet->getStyle('A4:I1000')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Borders for all
        $sheet->getStyle('A1:I1000')->applyFromArray([
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
        $widths = [
            'A' => 25, // Employee name
            'B' => 15, // Employee ID
        ];

        for ($i = 1; $i <= 7; $i++) {
            $columnLetter = $this->getColumnLetter($i + 2); // +2 because A and B are taken
            $widths[$columnLetter] = 12; // Status columns
        }

        return $widths;
    }

    public function title(): string
    {
        list($year, $week) = explode('-W', $this->minggu);
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = Carbon::now()->setISODate($year, $week)->endOfWeek();
        return 'Laporan Absensi Mingguan ' . $startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y');
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
