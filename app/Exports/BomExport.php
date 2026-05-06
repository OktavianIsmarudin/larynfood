<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Export semua BOM ke Excel format
 */
class BomExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    private $pakets;

    public function __construct($pakets)
    {
        $this->pakets = $pakets;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->pakets as $paket) {
            $rows->push([
                'Nama BOM' => $paket->nama_paket,
                'Kode BOM' => $paket->kode_paket ?? '-',
                'Deskripsi' => $paket->deskripsi ?? '-',
                'Total Item' => $paket->details->count(),
                'HPP Total' => $paket->hpp_total ?? 0,
                'Status' => ucfirst($paket->status),
                'Dibuat' => $paket->created_at->format('d/m/Y'),
            ]);

            // Add items
            if ($paket->details->count() > 0) {
                $rows->push([
                    'Nama BOM' => '',
                    'Kode BOM' => '',
                    'Deskripsi' => 'ITEM DETAIL:',
                    'Total Item' => '',
                    'HPP Total' => '',
                    'Status' => '',
                    'Dibuat' => '',
                ]);

                foreach ($paket->details as $detail) {
                    $rows->push([
                        'Nama BOM' => '  └─ ' . ($detail->stockGudang->nama_produk ?? ''),
                        'Kode BOM' => '',
                        'Deskripsi' => $detail->stockGudang->category->nama_kategori ?? '',
                        'Total Item' => $detail->qty_per_paket . ' ' . ($detail->stockGudang->satuan ?? ''),
                        'HPP Total' => $detail->stockGudang->hpp_per_pcs ?? 0,
                        'Status' => $detail->keterangan ?? '',
                        'Dibuat' => '',
                    ]);
                }
            }

            // Add space
            $rows->push([
                'Nama BOM' => '',
                'Kode BOM' => '',
                'Deskripsi' => '',
                'Total Item' => '',
                'HPP Total' => '',
                'Status' => '',
                'Dibuat' => '',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nama BOM',
            'Kode BOM',
            'Deskripsi',
            'Total Item',
            'HPP Total',
            'Status',
            'Dibuat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}
