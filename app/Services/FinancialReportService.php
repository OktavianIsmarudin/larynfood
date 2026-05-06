<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\Pembelian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportService
{
    /**
     * Hitung laporan keuangan untuk periode tertentu
     * 
     * RUMUS SESUAI SINGLE SOURCE OF TRUTH (nilai tersimpan di DB):
     * - Pendapatan = SUM(total_bayar) ← sudah termasuk diskon & ongkir
     * - Total Biaya HPP = SUM(hpp_total) ← sudah tersimpan saat transaksi
     * - Laba/Rugi = Pendapatan - Total HPP
     * - Margin = (Laba / Pendapatan) × 100
     * 
     * ❗ Ongkir MASUK pendapatan
     * ❗ Diskon SUDAH mengurangi pendapatan
     * ❗ HPP tidak terpengaruh diskon & ongkir
     */
    public function getFinancialReport($userId, $startDate, $endDate)
    {
        // Convert to Carbon for comparison
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Base query untuk periode ini
        $baseQuery = Penjualan::where('user_id', $userId)
            ->whereBetween('tanggal_penjualan', [$startDate, $endDate]);

        // 1️⃣ PENDAPATAN = SUM(total_bayar)
        // total_bayar sudah dihitung: subtotal - diskon + ongkir
        $pendapatan = (clone $baseQuery)->sum('total_bayar');

        // 2️⃣ TOTAL BIAYA HPP = SUM(hpp_total)
        // hpp_total sudah dihitung saat transaksi: qty × hpp_per_pcs
        $totalBiayaHPP = (clone $baseQuery)->sum('hpp_total');

        // 3️⃣ TOTAL SUBTOTAL (sebelum diskon)
        $totalSubtotal = (clone $baseQuery)->sum('total_penjualan');

        // 4️⃣ TOTAL DISKON (nilai diskon yang diberikan)
        // Hitung ulang berdasarkan tipe diskon
        $penjualanData = (clone $baseQuery)->get();
        $totalDiskon = $penjualanData->sum(function($p) {
            if ($p->tipe_diskon === 'persentase') {
                return ($p->total_penjualan * $p->diskon) / 100;
            }
            return (float)$p->diskon;
        });

        // 5️⃣ TOTAL ONGKIR
        $totalOngkir = (clone $baseQuery)->sum('ongkir');

        // 6️⃣ LABA / RUGI = Pendapatan - Total HPP
        $labaRugi = (float)$pendapatan - (float)$totalBiayaHPP;

        // 7️⃣ DETAIL PENJUALAN (untuk breakdown)
        $detailPenjualan = Penjualan::where('user_id', $userId)
            ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->with(['produk' => function($q) {
                $q->with('stockGudang');
            }])
            ->orderBy('tanggal_penjualan', 'desc')
            ->get()
            ->map(function($penjualan) {
                // Hitung nilai diskon berdasarkan tipe
                $subtotal = (float)$penjualan->total_penjualan;
                $nilaiDiskon = $penjualan->tipe_diskon === 'persentase'
                    ? ($subtotal * $penjualan->diskon) / 100
                    : (float)$penjualan->diskon;
                
                // Label diskon untuk display
                $labelDiskon = $penjualan->diskon > 0
                    ? ($penjualan->tipe_diskon === 'persentase' 
                        ? $penjualan->diskon . '%' 
                        : 'Rp ' . number_format($penjualan->diskon, 0, ',', '.'))
                    : '-';
                
                return [
                    'id' => $penjualan->id,
                    'tanggal' => $penjualan->tanggal_penjualan,
                    'customer' => $penjualan->nama_customer_snapshot ?? $penjualan->customer->nama_customer ?? '-',
                    'produk' => $penjualan->produk->stockGudang->nama_produk ?? $penjualan->produk->nama_produk ?? 'N/A',
                    'jumlah_pcs' => $penjualan->jumlah_pcs,
                    'harga_satuan' => (float)$penjualan->harga_satuan,
                    'subtotal' => $subtotal,
                    'diskon_label' => $labelDiskon,
                    'nilai_diskon' => $nilaiDiskon,
                    'ongkir' => (float)$penjualan->ongkir,
                    'total_bayar' => (float)$penjualan->total_bayar,
                    'hpp_total' => (float)$penjualan->hpp_total,
                    'laba_per_transaksi' => (float)$penjualan->total_bayar - (float)$penjualan->hpp_total,
                    'status_pembayaran' => $penjualan->status_pembayaran,
                ];
            });

        // 8️⃣ BREAKDOWN PER PRODUK
        $breakdownProduk = collect($detailPenjualan)
            ->groupBy('produk')
            ->map(function($items, $produk) {
                $totalSubtotal = $items->sum('subtotal'); // Total sebelum diskon
                $totalPendapatan = $items->sum('total_bayar'); // Menggunakan total_bayar
                $totalHPP = $items->sum('hpp_total');
                $totalDiskonProduk = $items->sum('nilai_diskon');
                $totalOngkirProduk = $items->sum('ongkir');
                $laba = $totalPendapatan - $totalHPP;
                $margin = $totalPendapatan > 0 ? ($laba / $totalPendapatan) * 100 : 0;
                
                return [
                    'produk' => $produk,
                    'nama_produk' => $produk,
                    'jumlah_pcs' => $items->sum('jumlah_pcs'),
                    'total_subtotal' => $totalSubtotal, // total sebelum diskon
                    'total_penjualan' => $totalPendapatan, // total_bayar (pendapatan aktual)
                    'total_diskon' => $totalDiskonProduk,
                    'total_ongkir' => $totalOngkirProduk,
                    'total_hpp' => $totalHPP,
                    'laba' => $laba,
                    'transaksi' => $items->count(),
                    'margin' => round($margin, 2),
                ];
            });

        return [
            'periode' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'label' => $this->getPeriodLabel($startDate, $endDate),
            ],
            'ringkasan' => [
                'pendapatan' => (float)$pendapatan,        // SUM(total_bayar)
                'total_subtotal' => (float)$totalSubtotal, // SUM(total_penjualan) sebelum diskon
                'total_diskon' => (float)$totalDiskon,     // Total nilai diskon
                'total_ongkir' => (float)$totalOngkir,     // Total ongkir
                'total_biaya_hpp' => (float)$totalBiayaHPP,// SUM(hpp_total)
                'laba_rugi' => (float)$labaRugi,           // Pendapatan - HPP
                'margin_keuntungan' => $pendapatan > 0 ? ($labaRugi / $pendapatan) * 100 : 0,
            ],
            'detail_penjualan' => $detailPenjualan->toArray(),
            'breakdown_produk' => $breakdownProduk->values()->toArray(),
        ];
    }

    /**
     * Get periode label untuk display
     */
    private function getPeriodLabel($startDate, $endDate)
    {
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Jika tanggal sama (same day)
        if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
            return $startDate->format('d') . ' ' . $monthNames[$startDate->month] . ' ' . $startDate->format('Y');
        }

        // Jika bulan dan tahun sama (same month)
        if ($startDate->month === $endDate->month && $startDate->year === $endDate->year) {
            return $startDate->format('d') . ' - ' . $endDate->format('d') . ' ' . $monthNames[$startDate->month] . ' ' . $startDate->format('Y');
        }

        // Jika tahun sama tapi bulan beda (same year)
        if ($startDate->year === $endDate->year) {
            return $startDate->format('d') . ' ' . $monthNames[$startDate->month] . ' - ' . 
                   $endDate->format('d') . ' ' . $monthNames[$endDate->month] . ' ' . $startDate->format('Y');
        }

        // Jika tahun beda (different year)
        return $startDate->format('d') . ' ' . $monthNames[$startDate->month] . ' ' . $startDate->format('Y') . ' - ' . 
               $endDate->format('d') . ' ' . $monthNames[$endDate->month] . ' ' . $endDate->format('Y');
    }

    /**
     * Get ringkasan keuangan dengan default periode bulan ini
     */
    public function getSummary($userId)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $report = $this->getFinancialReport($userId, $startDate, $endDate);
        
        return $report['ringkasan'];
    }

    /**
     * Get data untuk top produk berdasarkan laba
     */
    public function getTopProdukByProfit($userId, $startDate, $endDate, $limit = 5)
    {
        $report = $this->getFinancialReport($userId, $startDate, $endDate);
        
        return collect($report['breakdown_produk'])
            ->sortByDesc('laba')
            ->take($limit)
            ->values();
    }
}
