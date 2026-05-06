<?php

namespace App\Http\Controllers;

use App\Models\ProdukPaket;
use App\Models\ProdukPaketDetail;
use App\Models\StockGudang;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Controller untuk manajemen Produk Paket/Platter
 * 
 * FUNGSI:
 * - CRUD Produk Paket
 * - Kelola detail item dalam paket
 * - Auto-hitung HPP total berdasarkan komponen
 */
class ProdukPaketController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pakets = ProdukPaket::where('user_id', auth()->id())
            ->withCount('details')
            ->withCount('produkSiapJuals')
            ->withSum('produkSiapJuals', 'stok_siap_jual')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('produk-paket.index', compact('pakets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Tampilkan SEMUA stock gudang (karena paket hanya menyimpan KOMPOSISI)
        // Tidak perlu filter berdasarkan stok yang tersedia
        $stocks = StockGudang::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('nama_produk')
            ->get();
        
        return view('produk-paket.create', compact('stocks'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * LOGIKA:
     * 1. Create Produk Paket
     * 2. Create semua detail items
     * 3. Auto-hitung HPP total
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'kode_paket' => [
                'nullable', 'string', 'max:50',
                Rule::unique('produk_pakets', 'kode_paket')
                    ->where('user_id', auth()->id()),
            ],
            'deskripsi' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,nonaktif',
            // Detail items
            'items' => 'required|array|min:1',
            'items.*.stock_gudang_id' => 'required|exists:stock_gudang,id',
            'items.*.qty_per_paket' => 'required|numeric|min:0.01',
            'items.*.keterangan' => 'nullable|string|max:255',
        ], [
            'nama_paket.required' => 'Nama paket wajib diisi',
            'kode_paket.unique' => 'Kode paket sudah digunakan, silakan gunakan kode lain.',
            'items.required' => 'Minimal harus ada 1 item dalam paket',
            'items.min' => 'Minimal harus ada 1 item dalam paket',
            'items.*.stock_gudang_id.required' => 'Pilih item dari stock gudang',
            'items.*.qty_per_paket.required' => 'Jumlah per paket wajib diisi',
            'items.*.qty_per_paket.min' => 'Jumlah per paket minimal 0.01',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // STEP 1: Create Produk Paket
                $paket = ProdukPaket::create([
                    'user_id' => auth()->id(),
                    'nama_paket' => $validated['nama_paket'],
                    'kode_paket' => $validated['kode_paket'] ?? null,
                    'deskripsi' => $validated['deskripsi'] ?? null,
                    'status' => $validated['status'],
                    'hpp_total' => 0, // Akan dihitung otomatis
                ]);

                // STEP 2: Create detail items
                foreach ($validated['items'] as $item) {
                    ProdukPaketDetail::create([
                        'produk_paket_id' => $paket->id,
                        'stock_gudang_id' => $item['stock_gudang_id'],
                        'qty_per_paket' => $item['qty_per_paket'],
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }

                // HPP auto-update via ProdukPaketDetail boot() methods
            });

            return redirect()->route('produk-paket.index')
                ->with('success', 'Produk paket berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat produk paket: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProdukPaket $produkPaket)
    {
        // Pastikan user hanya bisa lihat data miliknya
        if ($produkPaket->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $produkPaket->load([
            'details.stockGudang.category',
            'produkSiapJuals',
        ]);
        
        return view('produk-paket.show', compact('produkPaket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukPaket $produkPaket)
    {
        if ($produkPaket->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $produkPaket->load('details.stockGudang');
        
        $stocks = StockGudang::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('nama_produk')
            ->get();
        
        return view('produk-paket.edit', compact('produkPaket', 'stocks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProdukPaket $produkPaket)
    {
        if ($produkPaket->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'kode_paket' => [
                'nullable', 'string', 'max:50',
                Rule::unique('produk_pakets', 'kode_paket')
                    ->where('user_id', auth()->id())
                    ->ignore($produkPaket->id),
            ],
            'deskripsi' => 'nullable|string|max:1000',
            'status' => 'required|in:aktif,nonaktif',
            // Detail items
            'items' => 'required|array|min:1',
            'items.*.stock_gudang_id' => 'required|exists:stock_gudang,id',
            'items.*.qty_per_paket' => 'required|numeric|min:0.01',
            'items.*.keterangan' => 'nullable|string|max:255',
        ], [
            'nama_paket.required' => 'Nama paket wajib diisi',
            'kode_paket.unique' => 'Kode paket sudah digunakan, silakan gunakan kode lain.',
            'items.required' => 'Minimal harus ada 1 item dalam paket',
            'items.min' => 'Minimal harus ada 1 item dalam paket',
            'items.*.stock_gudang_id.required' => 'Pilih item dari stock gudang',
            'items.*.qty_per_paket.required' => 'Jumlah per paket wajib diisi',
            'items.*.qty_per_paket.min' => 'Jumlah per paket minimal 0.01',
        ]);

        try {
            DB::transaction(function () use ($validated, $produkPaket) {
                // STEP 1: Update Produk Paket
                $produkPaket->update([
                    'nama_paket' => $validated['nama_paket'],
                    'kode_paket' => $validated['kode_paket'] ?? null,
                    'deskripsi' => $validated['deskripsi'] ?? null,
                    'status' => $validated['status'],
                ]);

                // STEP 2: Delete existing details and recreate
                $produkPaket->details()->delete();

                // STEP 3: Create new details
                foreach ($validated['items'] as $item) {
                    ProdukPaketDetail::create([
                        'produk_paket_id' => $produkPaket->id,
                        'stock_gudang_id' => $item['stock_gudang_id'],
                        'qty_per_paket' => $item['qty_per_paket'],
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }

                // HPP auto-update via ProdukPaketDetail boot() methods
            });

            return redirect()->route('produk-paket.show', $produkPaket)
                ->with('success', 'Produk paket berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk paket: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdukPaket $produkPaket)
    {
        if ($produkPaket->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        // Cek apakah ada produk siap jual yang menggunakan paket ini
        if ($produkPaket->produkSiapJuals()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus paket. Masih digunakan oleh ' . 
                    $produkPaket->produkSiapJuals()->count() . ' produk siap jual.');
        }

        try {
            DB::transaction(function () use ($produkPaket) {
                // Delete details first (cascade seharusnya handle ini, tapi untuk safety)
                $produkPaket->details()->delete();
                $produkPaket->delete();
            });

            return redirect()->route('produk-paket.index')
                ->with('success', 'Produk paket berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk paket: ' . $e->getMessage());
        }
    }

    /**
     * API: Get paket details untuk AJAX
     */
    public function getDetails(ProdukPaket $produkPaket)
    {
        if ($produkPaket->user_id !== auth()->id()) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $produkPaket->load('details.stockGudang');
        
        $details = $produkPaket->details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'stock_gudang_id' => $detail->stock_gudang_id,
                'nama_item' => $detail->stockGudang->nama_produk ?? '-',
                'qty_per_paket' => $detail->qty_per_paket,
                'stok_tersedia' => $detail->stockGudang->pcs_sisa ?? 0,
                'hpp_item' => $detail->hpp_item,
                'keterangan' => $detail->keterangan,
            ];
        });

        return response()->json([
            'paket' => [
                'id' => $produkPaket->id,
                'nama_paket' => $produkPaket->nama_paket,
                'hpp_total' => $produkPaket->hpp_total,
            ],
            'details' => $details,
        ]);
    }

    /**
     * API: Cek ketersediaan stok untuk jumlah paket tertentu
     */
    public function cekStok(Request $request, ProdukPaket $produkPaket)
    {
        if ($produkPaket->user_id !== auth()->id()) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $jumlahPaket = (int) $request->input('jumlah', 1);
        $cekStok = $produkPaket->cekStokCukup($jumlahPaket);

        return response()->json([
            'jumlah_paket' => $jumlahPaket,
            'stok_cukup' => $cekStok['sufficient'],
            'items_tidak_cukup' => $cekStok['insufficient_items'],
        ]);
    }
}
