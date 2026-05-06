<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RingkasanSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function title(): string
    {
        return 'Ringkasan Keuangan';
    }

    public function array(): array
    {
        $ringkasan = $this->laporan['ringkasan'];
        $periode = $this->laporan['periode'];

        return [
            ['LAPORAN KEUANGAN'],
            ['LARYN'],
            [],
            ['Periode', $periode['label']],
            ['Tanggal Cetak', now()->format('d F Y H:i')],
            [],
            ['RINGKASAN KEUANGAN'],
            [],
            ['Komponen', 'Nilai (Rp)'],
            ['Pendapatan (Total Bayar)', (float)$ringkasan['pendapatan']],
            ['Subtotal Sebelum Diskon', (float)($ringkasan['total_subtotal'] ?? 0)],
            ['Total Diskon', (float)($ringkasan['total_diskon'] ?? 0)],
            ['Total Ongkir', (float)($ringkasan['total_ongkir'] ?? 0)],
            ['Total HPP (Biaya Pokok)', (float)$ringkasan['total_biaya_hpp']],
            ['Laba / Rugi', (float)$ringkasan['laba_rugi']],
            [],
            ['PROFITABILITAS'],
            [],
            ['Margin Keuntungan (%)', number_format($ringkasan['margin_keuntungan'], 2) . '%'],
            [],
            ['KETERANGAN RUMUS:'],
            ['Pendapatan = Subtotal - Diskon + Ongkir'],
            ['Laba = Pendapatan - Total HPP'],
            ['Margin = (Laba / Pendapatan) × 100'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title styling
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A1:B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0D47A1']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Section headers
        $sheet->mergeCells('A7:B7');
        $sheet->getStyle('A7:B7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E7D32']],
        ]);

        $sheet->mergeCells('A17:B17');
        $sheet->getStyle('A17:B17')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1565C0']],
        ]);

        // Header row styling
        $sheet->getStyle('A9:B9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '37474F']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Data rows alignment
        $sheet->getStyle('B10:B15')->getAlignment()->setHorizontal('right');
        $sheet->getStyle('B19')->getAlignment()->setHorizontal('right');

        // Currency format for values
        for ($row = 10; $row <= 15; $row++) {
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        }

        // Laba row highlight
        $sheet->getStyle('A15:B15')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8F5E9']],
        ]);

        // Keterangan section
        $sheet->mergeCells('A21:B21');
        $sheet->getStyle('A21:B21')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '757575']],
        ]);
        $sheet->getStyle('A22:B24')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9E9E9E']],
        ]);

        // Borders
        $sheet->getStyle('A9:B15')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'BDBDBD'],
                ],
            ],
        ]);

        return [];
    }
}
