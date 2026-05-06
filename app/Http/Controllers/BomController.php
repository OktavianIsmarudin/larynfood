<?php

namespace App\Http\Controllers;

use App\Models\ProdukPaket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BomExport;
use App\Exports\SingleBomExport;

/**
 * Controller untuk Bill of Material (BOM) Management
 * 
 * BOM adalah komposisi/resep dari sebuah paket produk yang
 * terdiri dari beberapa item dari stock gudang dengan kuantitas tertentu.
 * 
 * Fitur:
 * - View daftar BOM dengan stats
 * - View detail BOM lengkap dengan komponen
 * - Export semua BOM ke Excel
 * - Export single BOM ke Excel
 */
class BomController extends Controller
{
    /**
     * Display a listing of all BOM
     * GET /bom
     */
    public function index()
    {
        try {
            $userId = auth()->id();
            
            // Get all BOM with stats
            $pakets = ProdukPaket::where('user_id', $userId)
                ->withCount('details')
                ->withCount('produkSiapJuals')
                ->withSum('produkSiapJuals', 'stok_siap_jual')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            // Calculate stats
            $totalBom = ProdukPaket::where('user_id', $userId)->count();
            $bomAktif = ProdukPaket::where('user_id', $userId)
                ->where('status', 'aktif')
                ->count();
            
            $totalItem = DB::table('produk_paket_details as ppd')
                ->join('produk_pakets as pp', 'ppd.produk_paket_id', '=', 'pp.id')
                ->where('pp.user_id', $userId)
                ->count();
            
            $totalHppRaw = DB::table('produk_pakets')
                ->where('user_id', $userId)
                ->sum('hpp_total');
            
            $totalHpp = 'Rp ' . number_format($totalHppRaw ?? 0, 0, ',', '.');
            
            return view('bom.index', compact('pakets', 'totalBom', 'bomAktif', 'totalItem', 'totalHpp'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display detail of a specific BOM
     * GET /bom/{bom}
     */
    public function details($id)
    {
        try {
            $bom = ProdukPaket::with(['details.stockGudang.category', 'produkSiapJuals'])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            
            return view('bom.details', compact('bom'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('bom.index')
                ->with('error', 'BOM tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export all BOM to Excel
     * GET /bom/export/excel
     */
    public function exportExcel()
    {
        try {
            $userId = auth()->id();
            
            // Get all BOM with details
            $pakets = ProdukPaket::with('details.stockGudang.category')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            if ($pakets->count() === 0) {
                return redirect()->route('bom.index')
                    ->with('error', 'Tidak ada BOM untuk diexport');
            }
            
            $fileName = 'BOM-Laryn-' . date('Y-m-d-His') . '.xlsx';
            
            return Excel::download(new BomExport($pakets), $fileName);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Export single BOM to Excel
     * GET /bom/{bom}/export
     */
    public function exportBom($id)
    {
        try {
            $bom = ProdukPaket::with('details.stockGudang.category')
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            
            $fileName = 'BOM-' . str_slug($bom->nama_paket) . '-' . date('Y-m-d-His') . '.xlsx';
            
            return Excel::download(new SingleBomExport($bom), $fileName);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('bom.index')
                ->with('error', 'BOM tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Get BOM stats via AJAX
     * GET /api/bom/stats
     */
    public function getStats()
    {
        try {
            $userId = auth()->id();
            
            $stats = [
                'totalBom' => ProdukPaket::where('user_id', $userId)->count(),
                'bomAktif' => ProdukPaket::where('user_id', $userId)->where('status', 'aktif')->count(),
                'totalItem' => DB::table('produk_paket_details as ppd')
                    ->join('produk_pakets as pp', 'ppd.produk_paket_id', '=', 'pp.id')
                    ->where('pp.user_id', $userId)
                    ->count(),
                'totalHpp' => DB::table('produk_pakets')
                    ->where('user_id', $userId)
                    ->sum('hpp_total') ?? 0,
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
