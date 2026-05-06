<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Export single BOM ke Excel format
 */
class SingleBomExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    private $bom;

    public function __construct($bom)
    {
        $this->bom = $bom;
    }

    public function collection()
    {
        $rows = collect();

        // Header Info
        $rows->push(['Bill of Material (BOM) Detail']);
        $rows->push(['']);
        $rows->push(['Nama BOM', $this->bom->nama_paket]);
        $rows->push(['Kode BOM', $this->bom->kode_paket ?? '-']);
        $rows->push(['Deskripsi', $this->bom->deskripsi ?? '-']);
        $rows->push(['Status', ucfirst($this->bom->status)]);
        $rows->push(['Total HPP', $this->bom->hpp_total]);
        $rows->push(['Dibuat', $this->bom->created_at->format('d/m/Y H:i')]);
        $rows->push(['']);

        // Items header
        $rows->push([
            '#',
            'Nama Item',
            'Kategori',
            'Satuan',
            'Qty per Paket',
            'HPP per Unit',
            'Subtotal',
            'Keterangan',
        ]);

        // Items detail
        $nomor = 1;
        $totalSubtotal = 0;
        foreach ($this->bom->details as $detail) {
            $subtotal = $detail->qty_per_paket * ($detail->stockGudang->hpp_per_pcs ?? 0);
            $totalSubtotal += $subtotal;

            $rows->push([
                $nomor++,
                $detail->stockGudang->nama_produk ?? '',
                $detail->stockGudang->category->nama_kategori ?? '',
                $detail->stockGudang->satuan ?? '',
                $detail->qty_per_paket,
                $detail->stockGudang->hpp_per_pcs ?? 0,
                $subtotal,
                $detail->keterangan ?? '',
            ]);
        }

        // Total
        $rows->push(['', '', '', '', '', 'TOTAL HPP', $totalSubtotal, '']);

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']],
            ],
            11 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            $highestRow => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
        ];
    }
}
