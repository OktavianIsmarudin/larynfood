<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\SaldoModalService;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private ReportService $reportService;
    private SaldoModalService $saldoModalService;

    public function __construct(ReportService $reportService, SaldoModalService $saldoModalService)
    {
        $this->reportService = $reportService;
        $this->saldoModalService = $saldoModalService;
    }

    /**
     * Display dashboard with statistics
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Super Admin: dapat memilih admin atau mode agregat
        if ($user->isSuperAdmin()) {
            $selectedUserId = $request->get('user_id', 'all'); // 'all' untuk mode agregat
            $adminUsers = User::where('role', 'admin')->get();

            // Get date range (last 30 days)
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subDays(30);

            if ($selectedUserId === 'all') {
                // Mode Agregat: Gabungkan semua data dari semua admin
                $totalSales = $this->reportService->getTotalSales(null, $startDate, $endDate);
                $totalPurchases = $this->reportService->getTotalPurchases(null, $startDate, $endDate);
                $totalProfit = $this->reportService->getTotalProfit(null, $startDate, $endDate);

                // Saldo modal total semua admin
                $totalSaldoModal = $this->saldoModalService->getOverview(null)['saldo_akhir'];
                $saldoModalBersih = $totalSaldoModal + $totalSales;

                $topProducts = $this->reportService->getTopSellingProducts(null, 5, $startDate, $endDate);
                $salesByDate = $this->reportService->getSalesByDate(null, $startDate, $endDate);
                $salesByCategory = $this->reportService->getSalesByCategory(null, $startDate, $endDate);
                $weeklyRevenueVsCost = $this->reportService->getWeeklyRevenueVsCost(null, $startDate, $endDate);
                $stockStatus = $this->reportService->getStockStatus(null);
            } else {
                // Mode Per Admin: Lihat data admin tertentu
                $totalSales = $this->reportService->getTotalSales($selectedUserId, $startDate, $endDate);
                $totalPurchases = $this->reportService->getTotalPurchases($selectedUserId, $startDate, $endDate);
                $totalProfit = $this->reportService->getTotalProfit($selectedUserId, $startDate, $endDate);

                $saldoModalOverview = $this->saldoModalService->getOverview($selectedUserId);
                $totalSaldoModal = $saldoModalOverview['saldo_akhir'];
                $saldoModalBersih = $totalSaldoModal + $totalSales;

                $topProducts = $this->reportService->getTopSellingProducts($selectedUserId, 5, $startDate, $endDate);
                $salesByDate = $this->reportService->getSalesByDate($selectedUserId, $startDate, $endDate);
                $salesByCategory = $this->reportService->getSalesByCategory($selectedUserId, $startDate, $endDate);
                $weeklyRevenueVsCost = $this->reportService->getWeeklyRevenueVsCost($selectedUserId, $startDate, $endDate);
                $stockStatus = $this->reportService->getStockStatus($selectedUserId);
            }

            return view('dashboard', [
                'totalSales' => $totalSales,
                'totalPurchases' => $totalPurchases,
                'totalProfit' => $totalProfit,
                'totalSaldoModal' => $totalSaldoModal,
                'saldoModalBersih' => $saldoModalBersih,
                'topProducts' => $topProducts,
                'salesByDate' => $salesByDate,
                'salesByCategory' => $salesByCategory,
                'weeklyRevenueVsCost' => $weeklyRevenueVsCost,
                'stockStatus' => $stockStatus,
                'isSuperAdmin' => true,
                'adminUsers' => $adminUsers,
                'selectedUserId' => $selectedUserId,
            ]);
        }

        // Admin Biasa: Hanya lihat data mereka sendiri
        $userId = $user->id;

        // Get date range (last 30 days)
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);

        // Get statistics
        $totalSales = $this->reportService->getTotalSales($userId, $startDate, $endDate);
        $totalPurchases = $this->reportService->getTotalPurchases($userId, $startDate, $endDate);
        $totalProfit = $this->reportService->getTotalProfit($userId, $startDate, $endDate);

        // Get saldo modal overview
        $saldoModalOverview = $this->saldoModalService->getOverview($userId);
        $totalSaldoModal = $saldoModalOverview['saldo_akhir'];
        $saldoModalBersih = $totalSaldoModal + $totalSales;

        // Get top selling products with proper relationship
        $topProducts = $this->reportService->getTopSellingProducts($userId, 5, $startDate, $endDate);

        // Get sales by date for chart
        $salesByDate = $this->reportService->getSalesByDate($userId, $startDate, $endDate);

        // Chart data: Sales by Category, Revenue vs Cost, Stock Status
        $salesByCategory = $this->reportService->getSalesByCategory($userId, $startDate, $endDate);
        $weeklyRevenueVsCost = $this->reportService->getWeeklyRevenueVsCost($userId, $startDate, $endDate);
        $stockStatus = $this->reportService->getStockStatus($userId);

        return view('dashboard', [
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalProfit' => $totalProfit,
            'totalSaldoModal' => $totalSaldoModal,
            'saldoModalBersih' => $saldoModalBersih,
            'topProducts' => $topProducts,
            'salesByDate' => $salesByDate,
            'salesByCategory' => $salesByCategory,
            'weeklyRevenueVsCost' => $weeklyRevenueVsCost,
            'stockStatus' => $stockStatus,
            'isSuperAdmin' => false,
        ]);
    }

    /**
     * Get dashboard statistics via AJAX (for auto-refresh)
     */
    public function getStats()
    {
        $userId = auth()->id();

        // Get date range (last 30 days)
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);

        // Get statistics
        $totalSales = $this->reportService->getTotalSales($userId, $startDate, $endDate);
        $totalPurchases = $this->reportService->getTotalPurchases($userId, $startDate, $endDate);
        $totalProfit = $this->reportService->getTotalProfit($userId, $startDate, $endDate);

        // Get saldo modal overview
        $saldoModalOverview = $this->saldoModalService->getOverview($userId);
        $totalSaldoModal = $saldoModalOverview['saldo_akhir'];
        $saldoModalBersih = $totalSaldoModal + $totalSales;

        // Get top selling products
        $topProducts = $this->reportService->getTopSellingProducts($userId, 5, $startDate, $endDate);

        // Get sales by date for chart
        $salesByDate = $this->reportService->getSalesByDate($userId, $startDate, $endDate);

        // Chart data
        $salesByCategory = $this->reportService->getSalesByCategory($userId, $startDate, $endDate);
        $weeklyRevenueVsCost = $this->reportService->getWeeklyRevenueVsCost($userId, $startDate, $endDate);
        $stockStatus = $this->reportService->getStockStatus($userId);

        return response()->json([
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalProfit' => $totalProfit,
            'totalSaldoModal' => $totalSaldoModal,
            'saldoModalBersih' => $saldoModalBersih,
            'topProducts' => $topProducts,
            'salesByDate' => $salesByDate,
            'salesByCategory' => $salesByCategory,
            'weeklyRevenueVsCost' => $weeklyRevenueVsCost,
            'stockStatus' => $stockStatus,
        ]);
    }
}

