<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\PiutangManual;
use App\Models\ProdukSiapJual;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get total sales for a user within date range
     */
    public function getTotalSales($userId, $startDate = null, $endDate = null): float
    {
        $query = Penjualan::forUser($userId);

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        return (float) $query->sum('total_penjualan');
    }

    /**
     * Get total purchases for a user within date range
     */
    public function getTotalPurchases($userId, $startDate = null, $endDate = null): float
    {
        $query = Pembelian::forUser($userId);

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        return (float) $query->sum('total_pengeluaran');
    }

    /**
     * Get total profit for a user within date range
     */
    public function getTotalProfit($userId, $startDate = null, $endDate = null): float
    {
        $query = Penjualan::forUser($userId);

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        return (float) $query->sum('laba');
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts($userId, $limit = 10, $startDate = null, $endDate = null)
    {
        $query = Penjualan::forUser($userId)
            ->selectRaw('produk_siap_jual_id, SUM(jumlah_pcs) as total_qty, COUNT(*) as total_transactions, SUM(total_penjualan) as total_sales')
            ->groupBy('produk_siap_jual_id')
            ->orderByDesc('total_qty')
            ->with(['produk' => function($q) {
                $q->with('stockGudang');
            }]);

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get sales by date
     */
    public function getSalesByDate($userId, $startDate, $endDate)
    {
        $salesData = Penjualan::forUser($userId)
            ->selectRaw('DATE(tanggal_penjualan) as date, COUNT(*) as total_transactions, SUM(total_penjualan) as total_sales, SUM(laba) as total_profit')
            ->whereBetween('tanggal_penjualan', [$startDate, $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date,
                    'total_transactions' => $item->total_transactions,
                    'total_sales' => (float) $item->total_sales ?? 0,
                    'total_profit' => (float) $item->total_profit ?? 0,
                ];
            });

        return $salesData;
    }

    /**
     * Get outstanding receivables
     */
    public function getOutstandingReceivables($userId)
    {
        return Penjualan::forUser($userId)
            ->utang()
            ->with('customer')
            ->get();
    }

    /**
     * Get receivable amount
     */
    public function getTotalReceivables($userId): float
    {
        // Piutang dari penjualan (belum lunas)
        $piutangPenjualan = (float) Penjualan::forUser($userId)
            ->utang()
            ->sum('total_bayar');

        // Piutang manual aktif (belum lunas)
        $piutangManual = (float) PiutangManual::where('user_id', $userId)
            ->where('jenis', 'piutang')
            ->where('status', 'belum_lunas')
            ->sum('nominal');

        return $piutangPenjualan + $piutangManual;
    }

    /**
     * Get sales grouped by category
     */
    public function getSalesByCategory($userId, $startDate = null, $endDate = null): array
    {
        $query = Penjualan::where('penjualans.user_id', $userId)
            ->join('produk_siap_juals', 'penjualans.produk_siap_jual_id', '=', 'produk_siap_juals.id')
            ->leftJoin('stock_gudang', 'produk_siap_juals.stock_gudang_id', '=', 'stock_gudang.id')
            ->leftJoin('categories', 'stock_gudang.category_id', '=', 'categories.id')
            ->selectRaw('COALESCE(categories.nama_kategori, "Tanpa Kategori") as kategori, SUM(penjualans.total_penjualan) as total_sales, COUNT(penjualans.id) as total_transactions')
            ->groupBy('kategori');

        if ($startDate && $endDate) {
            $query->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate->copy()->endOfDay()]);
        }

        return $query->orderByDesc('total_sales')->get()->toArray();
    }

    /**
     * Get weekly revenue vs cost (HPP) for the current month
     */
    public function getWeeklyRevenueVsCost($userId, $startDate = null, $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfMonth() : Carbon::now()->endOfMonth();

        $weeks = [];
        $weekStart = $start->copy();
        $weekNum = 1;

        while ($weekStart->lte($end) && $weekNum <= 5) {
            $weekEnd = $weekStart->copy()->addDays(6);
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            $salesData = Penjualan::forUser($userId)
                ->whereBetween('tanggal_penjualan', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
                ->selectRaw('COALESCE(SUM(total_penjualan), 0) as revenue, COALESCE(SUM(hpp_total), 0) as cost')
                ->first();

            $weeks[] = [
                'label' => 'Minggu ' . $weekNum,
                'revenue' => (float) ($salesData->revenue ?? 0),
                'cost' => (float) ($salesData->cost ?? 0),
            ];

            $weekStart = $weekEnd->copy()->addDay();
            $weekNum++;
        }

        return $weeks;
    }

    /**
     * Get product stock status summary
     */
    public function getStockStatus($userId): array
    {
        $products = ProdukSiapJual::forUser($userId)->get();

        $stokAman = 0;
        $stokSedang = 0;
        $stokRendah = 0;

        foreach ($products as $product) {
            $stok = $product->stok_siap_jual ?? 0;
            if ($stok >= 10) {
                $stokAman++;
            } elseif ($stok >= 3) {
                $stokSedang++;
            } else {
                $stokRendah++;
            }
        }

        return [
            'stok_aman' => $stokAman,
            'stok_sedang' => $stokSedang,
            'stok_rendah' => $stokRendah,
            'total' => $products->count(),
        ];
    }
}
