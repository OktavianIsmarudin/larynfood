<?php

namespace App\Http\Controllers;

use App\Models\StockGudang;
use App\Models\Pembelian;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockAdjustment;
use App\Services\PembelianStockService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class StockGudangController extends Controller
{
    use AuthorizesRequests;
    
    protected PembelianStockService $stockService;
    
    public function __construct(PembelianStockService $stockService)
    {
        $this->stockService = $stockService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = StockGudang::where('user_id', auth()->id())
            ->with('category', 'supplier', 'pembelian')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('stock-gudang.index', compact('stocks'));
    }

    /**
     * Show the form for creating a new resource
     * 
     * Jika dari pembelian (purchase_id):
     * - Autofill: nama_produk, supplier, jumlah_pack, harga_per_pack (readonly)
     * - User input: sku, satuan, konversi_satuan, lokasi_gudang
     */
    public function create(Request $request)
    {
        $categories = Category::where('user_id', auth()->id())->get();
        $suppliers = Supplier::where('user_id', auth()->id())->get();
        
        $pembelian = null;
        $autoFill = [];
        
        // Jika ada purchase_id, ambil data pembelian untuk autofill
        if ($request->has('purchase_id')) {
            $pembelian = Pembelian::where('id', $request->purchase_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            
            // Check jika sudah ada stock
            if ($pembelian->stockGudang) {
                return redirect()->route('stock-gudang.index')
                    ->with('error', 'Stock untuk pembelian ini sudah ada');
            }
            
            // Check status
            if ($pembelian->status_stock === 'sudah_masuk_gudang') {
                return redirect()->route('pembelian.show', $pembelian->id)
                    ->with('error', 'Pembelian sudah masuk gudang');
            }
            
            $autoFill = $this->stockService->getStockAutoFillFromPembelian($pembelian);
        }
        
        return view('stock-gudang.create', compact('categories', 'suppliers', 'pembelian', 'autoFill'));
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $isFromPurchase = !empty($request->input('purchase_id'));
        
        $validated = $request->validate([
            'purchase_id' => 'nullable|exists:pembelians,id',
            'sku' => 'required|string|unique:stock_gudang,sku,NULL,id,user_id,' . auth()->id(),
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'satuan' => 'required|string|max:50',
            'konversi_satuan' => 'required|integer|min:1',
            'lokasi_gudang' => 'nullable|string|max:255',
        ]);

        try {
            if ($isFromPurchase) {
                // Create stock dari pembelian
                $this->stockService->createStockFromPembelian($validated);
                $message = 'Stock gudang berhasil ditambahkan dari pembelian';
            } else {
                // Create stock manual
                $totalPcs = $validated['konversi_satuan'];
                
                StockGudang::create([
                    'user_id' => auth()->id(),
                    'sku' => $validated['sku'],
                    'nama_produk' => $validated['nama_produk'],
                    'category_id' => $validated['category_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'satuan' => $validated['satuan'],
                    'konversi_satuan' => $validated['konversi_satuan'],
                    'jumlah_pack' => 1,
                    'jumlah_stock' => 1, // backward compatibility
                    'total_pcs' => $totalPcs,
                    'pcs_terpakai' => 0,
                    'pcs_sisa' => $totalPcs,
                    'sisa_stock_pcs' => 0, // backward compatibility
                    'lokasi_gudang' => $validated['lokasi_gudang'],
                    'source' => 'manual',
                    'status_stock' => 'sudah_masuk_gudang',
                ]);
                
                $message = 'Stock gudang manual berhasil ditambahkan';
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan stock gudang: ' . $e->getMessage());
        }

        return redirect()->route('stock-gudang.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     * 
     * Tampilkan detail stock gudang dengan:
     * - Total PCS (dari jumlah_pack × konversi_satuan)
     * - PCS Terpakai (dari SUM produk siap jual)
     * - PCS Sisa
     * - Status (Pack Terbuka, Stock Tersedia, dll)
     */
    public function show(StockGudang $stockGudang)
    {
        $this->authorize('view', $stockGudang);
        
        // Selalu query fresh dari database
        $stockGudang = StockGudang::findOrFail($stockGudang->id);
        
        // Auto-sync pcs_terpakai: pastikan = pcs_awal - pcs_sisa
        $stockGudang->syncPcsTerpakai();
        
        // Load relasi
        $stockGudang->load('produkSiapJual', 'pembelian.category');
        
        return view('stock-gudang.show', compact('stockGudang'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(StockGudang $stockGudang)
    {
        $this->authorize('update', $stockGudang);
        
        $categories = Category::where('user_id', auth()->id())->get();
        $suppliers = Supplier::where('user_id', auth()->id())->get();
        
        return view('stock-gudang.edit', compact('stockGudang', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Jika dari pembelian (purchase_id):
     * - Hanya bisa update: nama_produk, supplier_id, category_id, jumlah_pack, harga_beli_pack
     * - DILARANG: sku, satuan, konversi_satuan, pcs_terpakai
     */
    public function update(Request $request, StockGudang $stockGudang)
    {
        $this->authorize('update', $stockGudang);
        
        $isFromPurchase = !is_null($stockGudang->purchase_id);

        if ($isFromPurchase) {
            // Update hanya field tertentu untuk stock dari pembelian
            $validated = $request->validate([
                'nama_produk' => 'required|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'jumlah_pack' => 'required|integer|min:1',
                'harga_beli_pack' => 'nullable|numeric|min:0',
            ]);
            
            try {
                // Update via service
                $pembelian = $stockGudang->pembelian;
                $updateData = array_merge($validated, [
                    'qty' => $validated['jumlah_pack'],
                    'total_pengeluaran' => $validated['harga_beli_pack'],
                ]);
                
                $this->stockService->updateStockFromPembelian($pembelian, $updateData);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal memperbarui stock: ' . $e->getMessage());
            }
        } else {
            // Update semua field untuk stock manual
            $validated = $request->validate([
                'sku' => 'required|string|unique:stock_gudang,sku,' . $stockGudang->id . ',id,user_id,' . auth()->id(),
                'nama_produk' => 'required|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'satuan' => 'required|string|max:50',
                'konversi_satuan' => 'required|integer|min:1',
                'jumlah_pack' => 'required|integer|min:1',
                'harga_beli_pack' => 'nullable|numeric|min:0',
                'lokasi_gudang' => 'nullable|string|max:255',
            ]);

            try {
                // ✅ LOGIKA PERBAIKAN PCS AWAL:
                // - pcs_awal TIDAK BOLEH BERUBAH untuk stock manual (tetap dari awal kali pertama)
                // - Hanya update jumlah_pack, satuan, konversi, dll
                // - pcs_sisa = pcs_awal - pcs_terpakai (AUTO CALCULATED)
                
                // Hitung pcs_awal BARU hanya jika jumlah_pack/konversi berubah
                $pcsAwalLama = $stockGudang->pcs_awal ?? ($stockGudang->jumlah_pack * $stockGudang->konversi_satuan);
                $newPcsAwal = $validated['jumlah_pack'] * $validated['konversi_satuan'];
                
                // Recalculate pcs_sisa berdasarkan pcs_awal BARU
                $newPcsSisa = $newPcsAwal - ($stockGudang->pcs_terpakai ?? 0);
                
                $stockGudang->update([
                    'sku' => $validated['sku'],
                    'nama_produk' => $validated['nama_produk'],
                    'category_id' => $validated['category_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'satuan' => $validated['satuan'],
                    'konversi_satuan' => $validated['konversi_satuan'],
                    'jumlah_pack' => $validated['jumlah_pack'],
                    'jumlah_stock' => $validated['jumlah_pack'], // backward compatibility
                    'pcs_awal' => $newPcsAwal,           // ✅ Update pcs_awal sesuai qty baru
                    'total_pcs' => $newPcsAwal,          // backward compatibility
                    'pcs_sisa' => $newPcsSisa,           // ✅ RECALCULATE = pcs_awal - pcs_terpakai
                    'harga_beli_pack' => $validated['harga_beli_pack'],
                    'lokasi_gudang' => $validated['lokasi_gudang'],
                ]);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal memperbarui stock gudang: ' . $e->getMessage());
            }
        }

        return redirect()->route('stock-gudang.index')
            ->with('success', 'Stock gudang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockGudang $stockGudang)
    {
        $this->authorize('delete', $stockGudang);
        
        $stockGudang->delete();

        return redirect()->route('stock-gudang.index')
            ->with('success', 'Stock gudang berhasil dihapus');
    }

    /**
     * Reduce stock manually (non-sale) - untuk kebutuhan internal
     * 
     * LOGIKA:
     * 1. Validasi: jumlah_pengurangan tidak boleh > pcs_sisa
     * 2. Update stock:
     *    - pcs_terpakai += jumlah_pengurangan
     *    - pcs_sisa -= jumlah_pengurangan
     *    - total_modal_sisa = pcs_sisa * harga_beli_pcs
     * 3. Simpan riwayat di stock_adjustments
     */
    public function reduceStock(Request $request, StockGudang $stockGudang)
    {
        $this->authorize('update', $stockGudang);

        // Validasi input
        $validated = $request->validate([
            'jumlah_pengurangan' => 'required|integer|min:1',
            'catatan_pengurangan' => 'required|string|min:5|max:500',
        ], [
            'jumlah_pengurangan.required' => 'Jumlah pengurangan wajib diisi',
            'jumlah_pengurangan.integer' => 'Jumlah harus berupa angka',
            'jumlah_pengurangan.min' => 'Jumlah minimal 1 PCS',
            'catatan_pengurangan.required' => 'Catatan wajib diisi',
            'catatan_pengurangan.min' => 'Catatan minimal 5 karakter',
            'catatan_pengurangan.max' => 'Catatan maksimal 500 karakter',
        ]);

        // Validasi: tidak boleh melebihi stok sisa
        $pcs_sisa = $stockGudang->pcs_sisa ?? 0;
        if ($validated['jumlah_pengurangan'] > $pcs_sisa) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Jumlah melebihi stok tersedia. Stok sisa: {$pcs_sisa} PCS");
        }

        try {
            DB::transaction(function () use ($stockGudang, $validated) {
                $jumlah_pengurangan = $validated['jumlah_pengurangan'];
                $harga_beli_pcs = (float)($stockGudang->harga_beli_pack ?? 0) / (int)($stockGudang->konversi_satuan ?? 1);

                // Update stock gudang
                $stockGudang->update([
                    'pcs_terpakai' => ($stockGudang->pcs_terpakai ?? 0) + $jumlah_pengurangan,
                    'pcs_sisa' => ($stockGudang->pcs_sisa ?? 0) - $jumlah_pengurangan,
                ]);

                // Recalculate total_modal_sisa
                $pcs_sisa_baru = $stockGudang->pcs_sisa;
                $total_modal_sisa = $pcs_sisa_baru * $harga_beli_pcs;
                
                $stockGudang->update([
                    'total_modal_sisa' => $total_modal_sisa,
                ]);

                // Simpan riwayat pengurangan
                StockAdjustment::create([
                    'stock_gudang_id' => $stockGudang->id,
                    'user_id' => auth()->id(),
                    'jumlah_pengurangan' => $jumlah_pengurangan,
                    'catatan' => $validated['catatan_pengurangan'],
                ]);
            });

            return redirect()->route('stock-gudang.show', $stockGudang->id)
                ->with('success', "Stok berhasil dikurangi sebanyak {$validated['jumlah_pengurangan']} PCS untuk kebutuhan: {$validated['catatan_pengurangan']}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengurangi stok: ' . $e->getMessage());
        }
    }

    /**
     * Delete stock adjustment dan restore stok
     * 
     * LOGIKA:
     * 1. Ambil StockAdjustment record
     * 2. Reverse operasi: restore stok yang dikurangi
     *    - pcs_terpakai -= jumlah_pengurangan
     *    - pcs_sisa += jumlah_pengurangan
     *    - total_modal_sisa = pcs_sisa * harga_beli_pcs
     * 3. Hapus StockAdjustment record
     */
    public function deleteStockAdjustment(StockAdjustment $stockAdjustment)
    {
        $stockGudang = $stockAdjustment->stockGudang;
        $this->authorize('update', $stockGudang);

        try {
            DB::transaction(function () use ($stockGudang, $stockAdjustment) {
                $jumlah_pengurangan = $stockAdjustment->jumlah_pengurangan;
                $harga_beli_pcs = (float)($stockGudang->harga_beli_pack ?? 0) / (int)($stockGudang->konversi_satuan ?? 1);

                // Restore stok gudang
                $stockGudang->update([
                    'pcs_terpakai' => max(0, ($stockGudang->pcs_terpakai ?? 0) - $jumlah_pengurangan),
                    'pcs_sisa' => ($stockGudang->pcs_sisa ?? 0) + $jumlah_pengurangan,
                ]);

                // Recalculate total_modal_sisa
                $pcs_sisa_baru = $stockGudang->pcs_sisa;
                $total_modal_sisa = $pcs_sisa_baru * $harga_beli_pcs;
                
                $stockGudang->update([
                    'total_modal_sisa' => $total_modal_sisa,
                ]);

                // Hapus adjustment record
                $stockAdjustment->delete();
            });

            return redirect()->route('stock-gudang.show', $stockGudang->id)
                ->with('success', "Pengurangan stok berhasil dibatalkan. Stok dikembalikan sebanyak {$jumlah_pengurangan} PCS");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membatalkan pengurangan stok: ' . $e->getMessage());
        }
    }
}
