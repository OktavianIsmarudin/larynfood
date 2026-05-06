<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\PembelianStockService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
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
        $pembelians = Pembelian::where('user_id', auth()->id())
            ->with('supplier', 'category', 'stockGudang')
            ->orderBy('tanggal_pembelian', 'desc')
            ->paginate(10);
        
        return view('pembelian.index', compact('pembelians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();
        
        return view('pembelian.create', compact('suppliers', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_bukti_pembelian' => 'nullable|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'category_id' => 'nullable|exists:categories,id',
            'nama_produk' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tipe_diskon' => 'nullable|in:persen,nominal',
            'diskon' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'total_pengeluaran' => 'nullable|numeric|min:0',
            'tanggal_pembelian' => 'required|date',
            'bukti_pembelian' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
        ]);

        // Auto-generate nomor bukti jika kosong
        if (empty($validated['nomor_bukti_pembelian'])) {
            $validated['nomor_bukti_pembelian'] = Pembelian::generateNomorBukti();
        } else {
            // Check jika nomor bukti sudah ada
            $exists = Pembelian::where('nomor_bukti_pembelian', $validated['nomor_bukti_pembelian'])
                ->where('user_id', auth()->id())
                ->exists();
            
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nomor bukti pembelian sudah pernah digunakan!');
            }
        }

        // Calculate total if not provided
        if (empty($validated['total_pengeluaran'])) {
            $validated['total_pengeluaran'] = $validated['qty'] * $validated['harga_satuan'];
        }

        // Handle file upload
        if ($request->hasFile('bukti_pembelian')) {
            $file = $request->file('bukti_pembelian');
            $path = $file->store('pembelians', 'public');
            $validated['bukti_pembelian'] = $path;
        } else {
            unset($validated['bukti_pembelian']);
        }

        try {
            DB::transaction(function () use ($validated) {
                Pembelian::create([
                    'user_id' => auth()->id(),
                    ...$validated,
                    'total_biaya_awal' => $validated['total_pengeluaran'],
                    'status_stock' => 'belum_masuk_gudang',
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage());
        }

        return redirect()->route('pembelian.index')
            ->with('success', 'Pembelian berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        $this->authorize('view', $pembelian);
        return view('pembelian.show', compact('pembelian'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(Pembelian $pembelian)
    {
        $this->authorize('update', $pembelian);
        
        $suppliers = Supplier::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();
        
        return view('pembelian.edit', compact('pembelian', 'suppliers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Jika status_stock = belum_masuk_gudang:
     * - Update freely
     * 
     * Jika status_stock = sudah_masuk_gudang (sudah ada stock):
     * - Hanya bisa update qty (akan recalculate stock)
     * - Hanya bisa update harga_satuan/total_pengeluaran (untuk HPP tracking)
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        $this->authorize('update', $pembelian);
        
        $isStockEntered = $pembelian->status_stock === 'sudah_masuk_gudang';

        if ($isStockEntered) {
            // Update terbatas: hanya qty dan harga
            $validated = $request->validate([
                'qty' => 'required|integer|min:1',
                'harga_satuan' => 'required|numeric|min:0',
                'tipe_diskon' => 'nullable|in:persen,nominal',
                'diskon' => 'nullable|numeric|min:0',
                'subtotal' => 'nullable|numeric|min:0',
                'total_pengeluaran' => 'nullable|numeric|min:0',
                'bukti_pembelian' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
            ]);
        } else {
            // Update penuh
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'category_id' => 'nullable|exists:categories,id',
                'nama_produk' => 'required|string|max:255',
                'qty' => 'required|integer|min:1',
                'harga_satuan' => 'required|numeric|min:0',
                'tipe_diskon' => 'nullable|in:persen,nominal',
                'diskon' => 'nullable|numeric|min:0',
                'subtotal' => 'nullable|numeric|min:0',
                'total_pengeluaran' => 'nullable|numeric|min:0',
                'tanggal_pembelian' => 'required|date',
                'bukti_pembelian' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
            ]);
        }

        // Calculate subtotal
        $subtotal = $validated['qty'] * $validated['harga_satuan'];
        $validated['subtotal'] = $subtotal;

        // Calculate discount
        $potongan = 0;
        if (!empty($validated['tipe_diskon']) && !empty($validated['diskon'])) {
            if ($validated['tipe_diskon'] === 'persen') {
                $potongan = $subtotal * ($validated['diskon'] / 100);
            } else {
                $potongan = $validated['diskon'];
            }
            if ($potongan > $subtotal) $potongan = $subtotal;
        } else {
            $validated['tipe_diskon'] = null;
            $validated['diskon'] = 0;
        }

        // Calculate total after discount
        $validated['total_pengeluaran'] = max(0, $subtotal - $potongan);

        // Handle file upload
        if ($request->hasFile('bukti_pembelian')) {
            // Delete old file if exists
            if ($pembelian->bukti_pembelian) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pembelian->bukti_pembelian);
            }
            $file = $request->file('bukti_pembelian');
            $path = $file->store('pembelians', 'public');
            $validated['bukti_pembelian'] = $path;
        } else {
            unset($validated['bukti_pembelian']);
        }

        try {
            DB::transaction(function () use ($validated, $pembelian, $isStockEntered) {
                if ($isStockEntered && $pembelian->stockGudang) {
                    // Update stock juga via service
                    $this->stockService->updateStockFromPembelian($pembelian, $validated);
                }
                
                // Update pembelian
                $updateData = [
                    'qty' => $validated['qty'],
                    'harga_satuan' => $validated['harga_satuan'],
                    'tipe_diskon' => $validated['tipe_diskon'] ?? null,
                    'diskon' => $validated['diskon'] ?? 0,
                    'subtotal' => $validated['subtotal'],
                    'total_pengeluaran' => $validated['total_pengeluaran'],
                    'total_biaya_awal' => $validated['total_pengeluaran'],
                ];
                
                if (!$isStockEntered) {
                    $updateData['supplier_id'] = $validated['supplier_id'];
                    $updateData['category_id'] = $validated['category_id'];
                    $updateData['nama_produk'] = $validated['nama_produk'];
                    $updateData['tanggal_pembelian'] = $validated['tanggal_pembelian'];
                }

                if (isset($validated['bukti_pembelian'])) {
                    $updateData['bukti_pembelian'] = $validated['bukti_pembelian'];
                }
                
                $pembelian->update($updateData);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pembelian: ' . $e->getMessage());
        }

        return redirect()->route('pembelian.index')
            ->with('success', 'Pembelian berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        $this->authorize('delete', $pembelian);

        try {
            DB::transaction(function () use ($pembelian) {
                // Delete stock jika ada via service (cascade)
                if ($pembelian->stockGudang) {
                    $this->stockService->deleteStockFromPembelian($pembelian);
                }
                
                $pembelian->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }

        return redirect()->route('pembelian.index')
            ->with('success', 'Pembelian berhasil dihapus');
    }

    /**
     * Redirect to stock-gudang create form dengan autofill dari pembelian
     * 
     * URL: /pembelian/{id}/masukkan-ke-gudang
     */
    public function toStockGudang(Pembelian $pembelian)
    {
        $this->authorize('update', $pembelian);
        
        // Check pembelian sudah ada stock
        if ($pembelian->stockGudang) {
            return redirect()->route('stock-gudang.show', $pembelian->stockGudang->id)
                ->with('info', 'Pembelian sudah memiliki stock di gudang');
        }
        
        // Check status
        if ($pembelian->status_stock === 'sudah_masuk_gudang') {
            return redirect()->route('pembelian.show', $pembelian->id)
                ->with('error', 'Pembelian sudah masuk gudang');
        }
        
        return redirect()->route('stock-gudang.create', ['purchase_id' => $pembelian->id]);
    }
}

