<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanLemburExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $bulan;
    protected $tahun;
    protected $search;

    public function __construct($bulan, $tahun, $search = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->search = $search;
    }

    public function array(): array
    {
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();
        $today = Carbon::today();
        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        $query = Absensi::whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'lembur')
            ->with(['karyawan.departemen', 'karyawan.posisi', 'jamKerja']);

        if ($this->search) {
            $query->whereHas('karyawan', function($q) {
                $q->where('nama_karyawan', 'like', '%' . $this->search . '%');
            });
        }

        $absensis = $query->get();

        $data = [];

        // Title row
        $monthName = Carbon::createFromDate($this->tahun, $this->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
        $data[] = ['', '', 'Laporan Lembur ' . $monthName];

        // Header row
        $data[] = [
            'No',
            'Nama Karyawan',
            'Departemen',
            'Posisi',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Total Jam Kerja',
            'Status'
        ];

        // Data rows
        foreach ($absensis as $index => $absen) {
            $totalJam = $this->calculateTotalJam($absen);
            $data[] = [
                $index + 1,
                $absen->karyawan->nama_karyawan ?? 'N/A',
                $absen->karyawan->departemen->nama_departemen ?? 'N/A',
                $absen->karyawan->posisi->nama_posisi ?? 'N/A',
                Carbon::parse($absen->tanggal_absen)->format('d-m-Y'),
                $absen->jam_masuk ?: '-',
                $absen->jam_keluar ?: '-',
                $totalJam,
                'Lembur'
            ];
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

        // Header row styling
        $sheet->getStyle('A2:I2')->applyFromArray([
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
        $sheet->getStyle('A3:I1000')->applyFromArray([
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
        return [
            'A' => 8,  // No
            'B' => 25, // Nama Karyawan
            'C' => 20, // Departemen
            'D' => 20, // Posisi
            'E' => 15, // Tanggal
            'F' => 15, // Jam Masuk
            'G' => 15, // Jam Keluar
            'H' => 20, // Total Jam Kerja
            'I' => 15, // Status
        ];
    }

    public function title(): string
    {
        $monthName = Carbon::createFromDate($this->tahun, $this->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
        return 'Laporan Lembur ' . $monthName;
    }
}
