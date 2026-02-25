<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SAWResultsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $hasil;
    protected $tipe;

    public function __construct($hasil, $tipe)
    {
        $this->hasil = $hasil;
        $this->tipe = $tipe;
    }

    public function collection()
    {
        return collect($this->hasil)->map(function ($item) {
            return [
                $item['ranking'] ?? '-',
                $item['nama'] ?? '-',
                $item['alamat'] ?? '-',
                number_format($item['nilai'] ?? 0, 4),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Ranking',
            'Nama ' . ucfirst($this->tipe),
            'Alamat',
            'Nilai SAW',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '667eea']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
