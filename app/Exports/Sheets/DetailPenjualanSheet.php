<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailPenjualanSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function title(): string
    {
        return 'Detail Penjualan';
    }

    public function array(): array
    {
        $data = [];

        // Header
        $data[] = ['DETAIL PENJUALAN'];
        $data[] = ['Periode: ' . $this->laporan['periode']['label']];
        $data[] = [];

        // Column headers
        $data[] = [
            'No',
            'Tanggal',
            'Customer',
            'Produk',
            'Qty',
            'Harga Satuan',
            'Subtotal',
            'Diskon',
            'Ongkir',
            'Total Bayar',
            'HPP Total',
            'Laba'
        ];

        // Data rows
        $no = 1;
        $totals = [
            'qty' => 0,
            'subtotal' => 0,
            'diskon' => 0,
            'ongkir' => 0,
            'total_bayar' => 0,
            'hpp' => 0,
            'laba' => 0,
        ];

        foreach ($this->laporan['detail_penjualan'] as $p) {
            $qty = (int)($p['jumlah_pcs'] ?? 0);
            $subtotal = (float)($p['subtotal'] ?? 0);
            $diskon = (float)($p['nilai_diskon'] ?? 0);
            $ongkir = (float)($p['ongkir'] ?? 0);
            $total_bayar = (float)($p['total_bayar'] ?? 0);
            $hpp = (float)($p['hpp_total'] ?? 0);
            $laba = (float)($p['laba_per_transaksi'] ?? 0);

            $data[] = [
                $no++,
                isset($p['tanggal']) ? date('d-m-Y', strtotime($p['tanggal'])) : '-',
                $p['customer'] ?? '-',
                $p['produk'] ?? '-',
                $qty,
                (float)($p['harga_satuan'] ?? 0),
                $subtotal,
                $diskon,
                $ongkir,
                $total_bayar,
                $hpp,
                $laba,
            ];

            // Accumulate totals
            $totals['qty'] += $qty;
            $totals['subtotal'] += $subtotal;
            $totals['diskon'] += $diskon;
            $totals['ongkir'] += $ongkir;
            $totals['total_bayar'] += $total_bayar;
            $totals['hpp'] += $hpp;
            $totals['laba'] += $laba;
        }

        if (empty($this->laporan['detail_penjualan'])) {
            $data[] = ['', 'Tidak ada data penjualan untuk periode ini', '', '', '', '', '', '', '', '', '', ''];
        } else {
            // Footer totals
            $data[] = [
                '',
                '',
                '',
                'TOTAL',
                $totals['qty'],
                '',
                $totals['subtotal'],
                $totals['diskon'],
                $totals['ongkir'],
                $totals['total_bayar'],
                $totals['hpp'],
                $totals['laba'],
            ];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // Tanggal
            'C' => 20,  // Customer
            'D' => 22,  // Produk
            'E' => 8,   // Qty
            'F' => 15,  // Harga Satuan
            'G' => 15,  // Subtotal
            'H' => 12,  // Diskon
            'I' => 12,  // Ongkir
            'J' => 15,  // Total Bayar
            'K' => 14,  // HPP Total
            'L' => 14,  // Laba
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Title
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0D47A1']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Subtitle
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 11, 'italic' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Column headers (row 4)
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Data rows currency format (rows 5 to highestRow-1)
        for ($row = 5; $row < $highestRow; $row++) {
            // Harga Satuan, Subtotal, Diskon, Ongkir, Total Bayar, HPP, Laba
            $sheet->getStyle('F' . $row . ':L' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('F' . $row . ':L' . $row)->getAlignment()->setHorizontal('right');
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('center');
        }

        // Total row styling (last row)
        $sheet->getStyle('A' . $highestRow . ':L' . $highestRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E3F2FD']],
        ]);
        $sheet->getStyle('F' . $highestRow . ':L' . $highestRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');

        // Borders
        $sheet->getStyle('A4:L' . $highestRow)->applyFromArray([
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
                $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F5F5F5']],
                ]);
            }
        }

        return [];
    }
}
