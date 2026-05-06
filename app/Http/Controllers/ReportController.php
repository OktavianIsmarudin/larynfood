<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\ProdukSiapJual;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Top Selling Products Report
     * 
     * Data diambil dari tabel penjualan dengan filter:
     * - Status pembayaran: 'lunas' (completed payments)
     * - User ID: authenticated user
     * 
     * Perhitungan:
     * - Total Terjual = SUM(jumlah_pcs)
     * - Total Transaksi = COUNT(*)
     * - Rata-rata = Total Terjual / Total Transaksi
     */
    public function topProducts()
    {
        // ✅ Query dengan filter status pembayaran 'lunas' (selesai/lunas)
        // Hanya menghitung transaksi yang sudah selesai/lunas
        $topProducts = Penjualan::where('user_id', auth()->id())
            ->where('status_pembayaran', 'lunas')  // FILTER: Hanya transaksi LUNAS
            ->groupBy('produk_siap_jual_id')
            ->select(
                'produk_siap_jual_id',
                \DB::raw('SUM(jumlah_pcs) as total_qty'),
                \DB::raw('COUNT(*) as total_transactions')
            )
            ->orderByDesc('total_qty')
            ->with(['produk' => function($q) {
                $q->with('stockGudang');
            }])
            ->limit(20)
            ->get();

        // ✅ Hitung summary statistics dari data yang sudah difilter
        $totalProducts = $topProducts->count();
        $totalPcs = $topProducts->sum('total_qty');
        $totalTransactions = $topProducts->sum('total_transactions');

        return view('reports.top-products', compact('topProducts', 'totalProducts', 'totalPcs', 'totalTransactions'));
    }

    /**
     * Sales Summary Report
     */
    public function salesSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $salesData = Penjualan::where('user_id', auth()->id())
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transactions, SUM(qty_pcs) as total_qty, SUM(total_penjualan) as total_sales')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        $summaryStats = [
            'total_transactions' => $salesData->sum('transactions'),
            'total_qty' => $salesData->sum('total_qty'),
            'total_sales' => $salesData->sum('total_sales'),
            'average_sale' => $salesData->count() > 0 ? $salesData->sum('total_sales') / $salesData->count() : 0,
        ];

        return view('reports.sales-summary', compact('salesData', 'summaryStats', 'startDate', 'endDate'));
    }

    /**
     * Purchase Summary Report
     */
    public function purchaseSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $purchaseData = Pembelian::where('user_id', auth()->id())
            ->whereDate('tanggal_pembelian', '>=', $startDate)
            ->whereDate('tanggal_pembelian', '<=', $endDate)
            ->selectRaw('DATE(tanggal_pembelian) as date, COUNT(*) as transactions, SUM(qty) as total_qty, SUM(total_pengeluaran) as total_purchases')
            ->groupByRaw('DATE(tanggal_pembelian)')
            ->orderBy('date')
            ->get();

        $summaryStats = [
            'total_transactions' => $purchaseData->sum('transactions'),
            'total_qty' => $purchaseData->sum('total_qty'),
            'total_purchases' => $purchaseData->sum('total_purchases'),
            'average_purchase' => $purchaseData->count() > 0 ? $purchaseData->sum('total_purchases') / $purchaseData->count() : 0,
        ];

        return view('reports.purchase-summary', compact('purchaseData', 'summaryStats', 'startDate', 'endDate'));
    }
}

