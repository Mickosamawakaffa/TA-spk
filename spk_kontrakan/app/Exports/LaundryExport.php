<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaundryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $laundry;

    public function __construct($laundry)
    {
        $this->laundry = $laundry;
    }

    public function collection()
    {
        return $this->laundry->map(function ($item) {
            // Ambil layanan dengan harga
            $layananInfo = $item->layanan->map(function($svc) {
                return ucfirst($svc->jenis_layanan) . ' (Rp ' . number_format($svc->harga, 0, ',', '.') . ')';
            })->implode(', ');
            
            return [
                $item->nama,
                $item->alamat ?? '-',
                $item->fasilitas ?? '-',
                $layananInfo ?: '-',
                $item->no_whatsapp ?? '-',
                $item->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Laundry',
            'Alamat',
            'Fasilitas',
            'Layanan & Harga',
            'No. WhatsApp',
            'Tanggal Input',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f5576c']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
