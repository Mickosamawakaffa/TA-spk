<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KontrakanExport implements FromCollection, WithHeadings, WithStyles
{
    protected $kontrakan;

    public function __construct($kontrakan)
    {
        $this->kontrakan = $kontrakan;
    }

    public function collection()
    {
        return $this->kontrakan->map(function ($item) {
            return [
                $item->nama,
                $item->alamat,
                'Rp ' . number_format($item->harga, 0, ',', '.'),
                $item->jumlah_kamar . ' kamar',
                round($item->jarak / 1000, 2) . ' km',
                $item->fasilitas ?? '-',
                $item->no_whatsapp ?? '-',
                $item->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Kontrakan',
            'Alamat',
            'Harga/Bulan',
            'Jumlah Kamar',
            'Jarak dari Kampus',
            'Fasilitas',
            'No. WhatsApp',
            'Tanggal Input',
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
