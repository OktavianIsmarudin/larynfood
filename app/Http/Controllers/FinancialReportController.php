<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportService;
use App\Exports\FinancialReportExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class FinancialReportController extends Controller
{
    protected FinancialReportService $reportService;

    public function __construct(FinancialReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display laporan keuangan
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $filterType = $request->get('filter_type', 'custom'); // harian, bulanan, custom

        // Validasi tanggal
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($startDate > $endDate) {
            return redirect()->back()
                ->with('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir');
        }

        // Get laporan
        $laporan = $this->reportService->getFinancialReport(
            auth()->id(),
            $startDate,
            $endDate
        );

        // Hitung statistik tambahan
        $stats = [
            'total_transaksi' => count($laporan['detail_penjualan']),
            'rata_rata_transaksi' => count($laporan['detail_penjualan']) > 0 
                ? $laporan['ringkasan']['pendapatan'] / count($laporan['detail_penjualan']) 
                : 0,
            'margin_keuntungan' => $laporan['ringkasan']['margin_keuntungan'],
        ];

        return view('financial-report.index', compact('laporan', 'stats', 'startDate', 'endDate', 'filterType'));
    }

    /**
     * Get data untuk API / AJAX (untuk chart)
     */
    public function getChartData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $laporan = $this->reportService->getFinancialReport(
            auth()->id(),
            $startDate,
            $endDate
        );

        return response()->json($laporan['breakdown_produk']);
    }

    /**
     * Quick filter harian
     */
    public function filterHarian()
    {
        $date = Carbon::now()->format('Y-m-d');
        return redirect()->route('financial-report.index', [
            'start_date' => $date,
            'end_date' => $date,
            'filter_type' => 'harian'
        ]);
    }

    /**
     * Quick filter bulanan
     */
    public function filterBulanan()
    {
        return redirect()->route('financial-report.index', [
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->format('Y-m-d'),
            'filter_type' => 'bulanan'
        ]);
    }

    /**
     * Export laporan ke PDF (opsional)
     */
    public function exportPDF(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $laporan = $this->reportService->getFinancialReport(
            auth()->id(),
            $startDate,
            $endDate
        );

        $pdf = \PDF::loadView('financial-report.pdf', compact('laporan'));
        
        $filename = 'Laporan-Keuangan-' . Carbon::parse($startDate)->format('Y-m-d') . 
                    '-to-' . Carbon::parse($endDate)->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $laporan = $this->reportService->getFinancialReport(
            auth()->id(),
            Carbon::parse($startDate),
            Carbon::parse($endDate)
        );

        $filename = 'Laporan-Keuangan-' . Carbon::parse($startDate)->format('d-M-Y') . 
                    '-to-' . Carbon::parse($endDate)->format('d-M-Y') . '.xlsx';

        return Excel::download(new FinancialReportExport($laporan), $filename);
    }
}
