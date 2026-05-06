<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BreakdownProdukSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function title(): string
    {
        return 'Breakdown per Produk';
    }

    public function array(): array
    {
        $data = [];

        // Header
        $data[] = ['BREAKDOWN LABA PER PRODUK'];
        $data[] = ['Periode: ' . $this->laporan['periode']['label']];
        $data[] = [];

        // Column headers
        $data[] = [
            'No',
            'Produk',
            'Transaksi',
            'Total Qty',
            'Total Subtotal',
            'Total Diskon',
            'Total Ongkir',
            'Pendapatan',
            'Total HPP',
            'Laba',
            'Margin'
        ];

        // Data rows
        $no = 1;
        $totals = [
            'transaksi' => 0,
            'qty' => 0,
            'subtotal' => 0,
            'diskon' => 0,
            'ongkir' => 0,
            'pendapatan' => 0,
            'hpp' => 0,
            'laba' => 0,
        ];

        foreach ($this->laporan['breakdown_produk'] as $p) {
            $transaksi = (int)($p['transaksi'] ?? 0);
            $qty = (int)($p['jumlah_pcs'] ?? 0);
            $subtotal = (float)($p['total_subtotal'] ?? 0);
            $pendapatan = (float)($p['total_penjualan'] ?? 0);
            $diskon = (float)($p['total_diskon'] ?? 0);
            $ongkir = (float)($p['total_ongkir'] ?? 0);
            $hpp = (float)($p['total_hpp'] ?? 0);
            $laba = (float)($p['laba'] ?? 0);
            $margin = (float)($p['margin'] ?? 0);

            $data[] = [
                $no++,
                $p['nama_produk'] ?? $p['produk'] ?? '-',
                $transaksi,
                $qty,
                $subtotal,
                $diskon,
                $ongkir,
                $pendapatan,
                $hpp,
                $laba,
                $margin,
            ];

            // Accumulate totals
            $totals['transaksi'] += $transaksi;
            $totals['qty'] += $qty;
            $totals['subtotal'] += $subtotal;
            $totals['diskon'] += $diskon;
            $totals['ongkir'] += $ongkir;
            $totals['pendapatan'] += $pendapatan;
            $totals['hpp'] += $hpp;
            $totals['laba'] += $laba;
        }

        if (empty($this->laporan['breakdown_produk'])) {
            $data[] = ['', 'Tidak ada data produk untuk periode ini', '', '', '', '', '', '', '', '', ''];
        } else {
            // Footer totals
            $marginTotal = $totals['pendapatan'] > 0 
                ? ($totals['laba'] / $totals['pendapatan']) * 100 
                : 0;

            $data[] = [
                '',
                'TOTAL',
                $totals['transaksi'],
                $totals['qty'],
                $totals['subtotal'],
                $totals['diskon'],
                $totals['ongkir'],
                $totals['pendapatan'],
                $totals['hpp'],
                $totals['laba'],
                $marginTotal,
            ];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Produk
            'C' => 10,  // Transaksi
            'D' => 10,  // Total Qty
            'E' => 15,  // Total Subtotal
            'F' => 13,  // Total Diskon
            'G' => 13,  // Total Ongkir
            'H' => 15,  // Pendapatan
            'I' => 14,  // Total HPP
            'J' => 14,  // Laba
            'K' => 10,  // Margin
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Title
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Subtitle
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 11, 'italic' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Column headers (row 4)
        $sheet->getStyle('A4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(30);

        // Data rows formatting
        for ($row = 5; $row < $highestRow; $row++) {
            // Currency format for Subtotal, Diskon, Ongkir, Pendapatan, HPP, Laba
            $sheet->getStyle('E' . $row . ':J' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('E' . $row . ':J' . $row)->getAlignment()->setHorizontal('right');
            
            // Center alignment for Transaksi, Qty
            $sheet->getStyle('C' . $row . ':D' . $row)->getAlignment()->setHorizontal('center');
            
            // Margin percentage format
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('0.00"%"');
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal('center');
        }

        // Total row styling
        $sheet->getStyle('A' . $highestRow . ':K' . $highestRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'C8E6C9']],
        ]);
        $sheet->getStyle('E' . $highestRow . ':J' . $highestRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('K' . $highestRow)->getNumberFormat()->setFormatCode('0.00"%"');

        // Borders
        $sheet->getStyle('A4:K' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'BDBDBD'],
                ],
            ],
        ]);

        // Alternate row colors
        for ($row = 5; $row < $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F5F5F5']],
                ]);
            }
        }

        // Laba column highlighting
        for ($row = 5; $row <= $highestRow; $row++) {
            $labaValue = $sheet->getCell('J' . $row)->getValue();
            if (is_numeric($labaValue)) {
                $color = $labaValue >= 0 ? '2E7D32' : 'C62828';
                $sheet->getStyle('J' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($color));
            }
        }

        return [];
    }
}
