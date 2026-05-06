<?php

namespace App\Http\Controllers;

use App\Models\ProdukSiapJual;
use App\Models\StockGudang;
use App\Models\ProdukPaket;
use App\Services\ProdukSiapJualService;
use App\Services\StockMovementService;
use App\Http\Requests\StoreProdukSiapJualRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProdukSiapJualController extends Controller
{
    use AuthorizesRequests;

    protected ProdukSiapJualService $service;
    protected StockMovementService $movementService;

    public function __construct(ProdukSiapJualService $service, StockMovementService $movementService)
    {
        $this->service = $service;
        $this->movementService = $movementService;
    }

    /**
     * Display a listing of the resource.
     * Super admin bisa lihat semua produk, admin hanya produknya sendiri
     */
    public function index(Request $request)
    {
        $query = ProdukSiapJual::with(['stockGudang', 'produkPaket.details', 'user']);
        
        // Filter by published status
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }
        
        // Super admin bisa lihat semua produk
        if (auth()->user()->role !== 'super_admin') {
            $query->where('user_id', auth()->id());
        }
        
        $produk = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('produk-siap-jual.index', compact('produk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stocks = StockGudang::where('user_id', auth()->id())
            ->with('category', 'supplier')
            ->orderBy('nama_produk')
            ->get();
        
        // Juga load produk paket untuk membuat produk berbasis paket
        $pakets = ProdukPaket::where('user_id', auth()->id())
            ->where('status', 'aktif')
            ->withCount('details')
            ->orderBy('nama_paket')
            ->get();

        $prefilledPaketId = null;
        $prefilledPaketName = null;
        $defaultTipeProduk = 'single';

        $requestedPaketId = request()->query('produk_paket_id');
        if ($requestedPaketId) {
            $selectedPaket = $pakets->firstWhere('id', (int) $requestedPaketId);

            if ($selectedPaket) {
                $prefilledPaketId = $selectedPaket->id;
                $prefilledPaketName = $selectedPaket->nama_paket;
                $defaultTipeProduk = 'paket';
            }
        }
        
        return view('produk-siap-jual.create', compact(
            'stocks',
            'pakets',
            'prefilledPaketId',
            'prefilledPaketName',
            'defaultTipeProduk'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * LOGIKA BARU:
     * - Input HPP, Margin, Biaya lain-lain
     * - HITUNG harga jual otomatis
     * - JANGAN kurangi stock gudang (hanya saat user klik "Tambah Stock")
     * - Set stok_siap_jual = 0 (default)
     */
    public function store(StoreProdukSiapJualRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();

            // Set default stok_siap_jual = 0 (belum ada paket yang ready)
            $validated['stok_siap_jual'] = 0;

            // Gunakan service untuk create dengan transaction
            $produk = $this->service->create($validated);
            
            return redirect()->route('produk-siap-jual.show', $produk)
                ->with('success', 'Produk siap jual berhasil dibuat. Stock gudang TIDAK dikurangi. Gunakan tombol "Tambah Stock" untuk menambahkan stok siap jual.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat produk siap jual: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * 
     * Load dengan eager loading untuk menghindari N+1 queries
     * dan ensure data fresh dari database (terutama setelah equipment usage update)
     */
    public function show(ProdukSiapJual $produkSiapJual)
    {
        $this->authorize('view', $produkSiapJual);
        
        // Refresh produk dari DB
        $produkSiapJual = $produkSiapJual->fresh();
        
        // EXTENSION: Load produkPaket untuk tipe paket
        if ($produkSiapJual->isPaket() && $produkSiapJual->produk_paket_id) {
            $produkSiapJual->load(['produkPaket.details.stockGudang']);
        }
        
        // Refresh stockGudang dengan direct findOrFail() untuk memastikan pcs_terpakai terbaru
        if ($produkSiapJual->stock_gudang_id) {
            $produkSiapJual->stockGudang = StockGudang::findOrFail($produkSiapJual->stock_gudang_id);
            $produkSiapJual->stockGudang->load('category');
        }
        
        // Load pemakaian peralatan
        $produkSiapJual->load([
            'pemakaianPeralatan.user',
            'pemakaianPeralatan.stockGudang.category',
        ]);
        
        return view('produk-siap-jual.show', compact('produkSiapJual'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukSiapJual $produkSiapJual)
    {
        $this->authorize('update', $produkSiapJual);
        
        $stocks = StockGudang::where('user_id', auth()->id())
            ->orderBy('nama_produk')
            ->get();
        
        return view('produk-siap-jual.edit', compact('produkSiapJual', 'stocks'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * LOGIKA BARU:
     * - Update HPP, Margin, Biaya
     * - HITUNG ulang harga jual otomatis
     * - JANGAN ubah stok (stok hanya berubah via "Tambah Stock")
     * - ❌ HPP TIDAK BOLEH BERUBAH (TERKUNCI)
     */
    public function update(StoreProdukSiapJualRequest $request, ProdukSiapJual $produkSiapJual)
    {
        $this->authorize('update', $produkSiapJual);
        
        try {
            $validated = $request->validated();
            
            // ❌ SAFEGUARD: Validasi HPP tidak boleh berubah
            if ((float)$validated['hpp_per_pcs'] != (float)$produkSiapJual->hpp_per_pcs) {
                throw new \Exception(
                    'HPP tidak boleh diubah! HPP sudah terkunci sejak awal pembuatan produk. ' .
                    'Jika ingin mengubah HPP, silahkan hapus produk ini dan buat yang baru.'
                );
            }
            
            // Gunakan service untuk update dengan transaction
            $produk = $this->service->update($produkSiapJual, $validated);
            
            return redirect()->route('produk-siap-jual.show', $produk)
                ->with('success', 'Produk siap jual berhasil diperbarui. Harga jual telah dihitung ulang.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk siap jual: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * LOGIKA BARU:
     * - Jika ada stok_siap_jual, kembalikan ke stock gudang
     * - Catat di stock_movements (type: IN)
     */
    public function destroy(ProdukSiapJual $produkSiapJual)
    {
        $this->authorize('delete', $produkSiapJual);
        
        try {
            $result = $this->service->delete($produkSiapJual);
            
            return redirect()
                ->route('produk-siap-jual.index')
                ->with('success', $result['message'] ?? 'Produk siap jual berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('produk-siap-jual.index')
                ->with('error', 'Gagal menghapus produk siap jual: ' . $e->getMessage());
        }
    }

    /**
     * Tambah stock siap jual dari stock gudang
     * 
     * LOGIKA BARU (WAJIB):
     * - Input: jumlah_paket (contoh: 2 paket)
     * - Hitung total PCS: paket × pcs_per_paket
     * - Validasi: pcs_sisa gudang >= total PCS
     * - KURANGI gudang, TAMBAH stok_siap_jual
     * - Catat di stock_movements (type: OUT)
     * 
     * POST /produk-siap-jual/{id}/tambah-stock
     */
    public function tambahStock(Request $request, ProdukSiapJual $produkSiapJual)
    {
        $this->authorize('update', $produkSiapJual);

        // Validasi: input dalam satuan PAKET (bukan PCS)
        $validated = $request->validate([
            'jumlah_paket' => 'required|integer|min:1',
        ], [
            'jumlah_paket.required' => 'Jumlah paket harus diisi',
            'jumlah_paket.integer' => 'Jumlah paket harus angka bulat',
            'jumlah_paket.min' => 'Jumlah paket minimal 1',
        ]);

        try {
            // Gunakan StockMovementService untuk handle transaction & pencatatan
            $result = $this->movementService->tambahStockSiapJual(
                $produkSiapJual,
                $validated['jumlah_paket']
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'stok_siap_jual' => $result['stok_siap_jual'],
                    'pcs_sisa_gudang' => $result['pcs_sisa_gudang'],
                ]);
            }

            return redirect()->back()
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Gagal tambah stock: ' . $e->getMessage());
        }
    }

    /**
     * Proses pemakaian peralatan/kemasan
     */
    public function processEquipment(Request $request, ProdukSiapJual $produkSiapJual)
    {
        $this->authorize('update', $produkSiapJual);

        try {
            // DEBUG: Log request data
            \Log::debug("🔍 processEquipment - Request received", [
                'produk_id' => $produkSiapJual->id,
                'peralatan' => $request->input('peralatan'),
                'peralatan_qty' => $request->input('peralatan_qty'),
            ]);
            
            $validated = $request->validate([
                'peralatan' => 'array|nullable',
                'peralatan.*' => 'integer|min:1|exists:stock_gudang,id',
                'peralatan_qty' => 'array|nullable',
                'peralatan_qty.*' => 'integer|min:1',
            ], [
                'peralatan.*.integer' => 'ID peralatan harus valid',
                'peralatan.*.exists' => 'Peralatan tidak ditemukan',
                'peralatan_qty.*.integer' => 'Jumlah harus angka',
                'peralatan_qty.*.min' => 'Jumlah minimal 1',
            ]);

            $peralatanList = $validated['peralatan'] ?? [];
            $peralatanQty = $validated['peralatan_qty'] ?? [];

            if (empty($peralatanList)) {
                return redirect()->back()->with('info', 'Tidak ada peralatan yang dipilih');
            }

            // Build equipment data array: [stock_gudang_id => quantity, ...]
            $peralatanData = [];
            foreach ($peralatanList as $index => $stockGudangId) {
                if ($stockGudangId && isset($peralatanQty[$index])) {
                    $peralatanData[$stockGudangId] = intval($peralatanQty[$index]);
                }
            }

            if (empty($peralatanData)) {
                return redirect()->back()->with('info', 'Mohon isi jumlah untuk setiap peralatan');
            }

            // DEBUG: Log equipment data before processing
            \Log::debug("🔍 Processing equipment data", [
                'peralatan_data' => $peralatanData,
                'items_count' => count($peralatanData),
            ]);

            // Proses pemakaian dengan transaction
            $result = $this->service->processEquipmentUsage($produkSiapJual, $peralatanData);
            
            // DEBUG: Log service result
            \Log::debug("🔍 Service result", [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? '',
                'items_processed' => $result['items_processed'] ?? 0,
            ]);

            // Check if processing was successful
            if (!$result['success']) {
                \Log::warning("❌ Equipment processing failed", [
                    'error_message' => $result['message'] ?? 'Unknown error',
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal memproses pemakaian peralatan');
            }

            // Build detail message
            $detailMessages = [];
            foreach ($result['details'] as $detail) {
                $detailMessages[] = sprintf(
                    "✓ %s: %d PCS (Terpakai: %d → %d, Sisa: %d → %d)",
                    $detail['nama_peralatan'],
                    $detail['jumlah_pakai'],
                    $detail['pcs_terpakai_sebelum'],
                    $detail['pcs_terpakai_sesudah'],
                    $detail['pcs_sisa_sebelum'],
                    $detail['pcs_sisa_sesudah']
                );
            }

            $successMessage = sprintf(
                "✓ Pemakaian %d item peralatan berhasil dicatat.\n\nDetail:\n%s",
                $result['items_processed'],
                implode("\n", $detailMessages)
            );

            return redirect()->route('produk-siap-jual.show', $produkSiapJual)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error("❌ Controller exception", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', "❌ Error: " . $e->getMessage());
        }
    }

    /**
     * Get available equipment untuk modal/ajax
     */
    public function getAvailableEquipment(Request $request)
    {
        try {
            $userId = auth()->id();

            $equipment = StockGudang::where('user_id', $userId)
                ->whereHas('category', function ($q) {
                    $q->where('jenis_kategori', 'peralatan');
                })
                ->where(function ($q) {
                    $q->where('pcs_sisa', '>', 0)
                      ->orWhereNull('pcs_sisa');
                })
                ->with('category')
                ->orderBy('nama_produk')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama_produk' => $item->nama_produk,
                        'kategori' => $item->category?->nama_kategori,
                        'pcs_sisa' => $item->pcs_sisa ?? 0,
                        'sku' => $item->sku,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $equipment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Toggle publish status produk
     */
    public function togglePublish(ProdukSiapJual $produkSiapJual)
    {
        try {
            // Super admin bisa toggle semua produk, admin hanya produknya sendiri
            if (auth()->user()->role !== 'super_admin' && $produkSiapJual->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah produk ini.'
                ], 403);
            }
            
            $produkSiapJual->is_published = !$produkSiapJual->is_published;
            $produkSiapJual->save();
            
            return response()->json([
                'success' => true,
                'is_published' => $produkSiapJual->is_published,
                'message' => $produkSiapJual->is_published 
                    ? 'Produk berhasil dipublikasikan di landing page' 
                    : 'Produk berhasil disembunyikan dari landing page'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}

