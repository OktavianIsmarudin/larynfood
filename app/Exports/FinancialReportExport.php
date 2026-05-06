<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\RingkasanSheet;
use App\Exports\Sheets\DetailPenjualanSheet;
use App\Exports\Sheets\BreakdownProdukSheet;

/**
 * Export Laporan Keuangan ke Excel dengan Multi-Sheet
 * 
 * STRUKTUR:
 * Sheet 1: Ringkasan Keuangan
 * Sheet 2: Detail Penjualan  
 * Sheet 3: Breakdown per Produk
 * 
 * RUMUS PERHITUNGAN:
 * - Subtotal = Harga Satuan × Qty
 * - Total Bayar = Subtotal - Diskon + Ongkir
 * - Laba = Total Bayar - HPP Total
 * - Margin = (Laba / Total Bayar) × 100
 */
class FinancialReportExport implements WithMultipleSheets
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    /**
     * Define sheets untuk export
     */
    public function sheets(): array
    {
        return [
            new RingkasanSheet($this->laporan),
            new DetailPenjualanSheet($this->laporan),
            new BreakdownProdukSheet($this->laporan),
        ];
    }
}
