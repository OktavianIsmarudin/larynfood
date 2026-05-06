<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Customer;
use App\Models\ProdukSiapJual;
use App\Models\PaymentMethod;
use App\Models\StockGudang;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualan = Penjualan::where('user_id', auth()->id())
            ->with(['customer', 'produk.stockGudang', 'produk.produkPaket', 'metodePembayaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('penjualan.index', compact('penjualan'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * LOGIKA: Ambil produk dari produk_siap_jual dengan stok_siap_jual > 0
     * Data stok diambil dari field stok_siap_jual (PAKET)
     */
    public function create()
    {
        // Get existing customers for autocomplete
        $customers = Customer::where('user_id', auth()->id())
            ->select('id', 'nama_customer', 'telepon', 'kota')
            ->orderBy('nama_customer')
            ->get();
        
        // Ambil produk siap jual yang memiliki stok_siap_jual > 0
        // PENTING: Gunakan stok_siap_jual (dalam PAKET) × pcs_per_paket = total PCS
        $produk = ProdukSiapJual::where('user_id', auth()->id())
            ->where('stok_siap_jual', '>', 0) // Filter dari stok_siap_jual (dalam paket)
            ->orderBy('nama_produk')
            ->get();
        
        $metode = PaymentMethod::where('user_id', auth()->id())->get();
        
        return view('penjualan.create', compact('customers', 'produk', 'metode'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * LOGIKA (DENGAN SUPPORT PAKET):
     * 1. Find or create customer
     * 2. Ambil produk dari produk_siap_jual
     * 3. Validasi & kurangi stok via kurangiStokPenjualan():
     *    - Single: kurangi stok_siap_jual saja (gudang sudah dikurangi saat Tambah Stock)
     *    - Paket: kurangi stok_siap_jual DAN kurangi stok gudang SETIAP komponen
     * 4. Simpan penjualan dengan harga_satuan dari form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_customer' => 'required|string|max:255',
            'tanggal_penjualan' => 'required|date',
            'produk_siap_jual_id' => 'required|exists:produk_siap_juals,id',
            'metode_pembayaran_id' => 'nullable|exists:payment_methods,id',
            'jumlah_pcs' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0.01',
            'diskon' => 'nullable|numeric|min:0',
            'tipe_diskon' => 'nullable|in:persentase,nominal',
            'ongkir' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'status_pembayaran' => 'required|in:lunas,dp,utang',
            'modal_terpakai' => 'nullable|numeric|min:0',
            'keterangan_modal' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // STEP 1: Find or create customer
                $customer = Customer::where('user_id', auth()->id())
                    ->where('nama_customer', $validated['nama_customer'])
                    ->first();

                if (!$customer) {
                    $customer = Customer::create([
                        'user_id' => auth()->id(),
                        'nama_customer' => $validated['nama_customer'],
                    ]);
                }

                // STEP 2: Ambil produk siap jual dengan eager load
                $produk = ProdukSiapJual::where('user_id', auth()->id())
                    ->with(['produkPaket.details.stockGudang'])
                    ->lockForUpdate()
                    ->findOrFail($validated['produk_siap_jual_id']);
                
                // Get harga_satuan dari form (user sudah pilih harga dari produk)
                $hargaSatuan = (float) $validated['harga_satuan'];
                if ($hargaSatuan <= 0) {
                    throw new \Exception('Harga satuan tidak valid.');
                }

                $jumlahPaketInput = (int) $validated['jumlah_pcs']; // Input sekarang dalam satuan PAKET
                $pcsPerPaket = (int) ($produk->pcs_per_paket ?? 1);
                $jumlahPcs = $jumlahPaketInput * $pcsPerPaket; // Konversi ke PCS
                
                // VALIDASI STOK: Cek apakah stok paket mencukupi
                $stokPaketAvailable = (int) ($produk->stok_siap_jual ?? 0);
                if ($jumlahPaketInput > $stokPaketAvailable) {
                    throw new \Exception(
                        'Stok tidak mencukupi! Jumlah yang diminta: ' . $jumlahPaketInput . 
                        ' paket, stok tersedia: ' . $stokPaketAvailable . ' paket. Silakan kurangi jumlah pesanan.'
                    );
                }
                
                // Jumlah paket yang akan dikurangi = input langsung (sudah dalam paket)
                $paketDikurangi = $jumlahPaketInput;

                // STEP 3: Validasi dan kurangi stok menggunakan method baru
                // Method ini akan handle validasi stok_siap_jual DAN komponen paket
                $produk->kurangiStokPenjualan($paketDikurangi);

                // STEP 4: Hitung semua nilai penjualan dengan helper method
                $diskon = (float) ($validated['diskon'] ?? 0);
                $tipeDiskon = $validated['tipe_diskon'] ?? 'nominal';
                $ongkir = (float) ($validated['ongkir'] ?? 0);
                
                // ✅ Gunakan helper method dari Model untuk kalkulasi konsisten
                // Harga satuan adalah per PAKET, jadi gunakan jumlah paket (bukan PCS)
                $kalkulasi = Penjualan::hitungPenjualan(
                    $jumlahPaketInput,
                    $hargaSatuan,
                    $diskon,
                    $tipeDiskon,
                    $ongkir
                );

                // STEP 5: Hitung HPP Total dan Laba
                // Untuk paket: gunakan HPP dari produkPaket
                // Untuk single: gunakan hpp_per_pcs
                if ($produk->isPaket() && $produk->produkPaket) {
                    $hppPerPaket = (float) ($produk->produkPaket->hpp_total ?? 0);
                    $hppTotal = $paketDikurangi * $hppPerPaket;
                } else {
                    $hppPerPcs = (float) ($produk->hpp_per_pcs ?? 0);
                    $hppTotal = $jumlahPcs * $hppPerPcs;
                }
                $laba = $kalkulasi['subtotal'] - $hppTotal; // Laba dihitung dari subtotal

                // STEP 6: Simpan penjualan
                Penjualan::create([
                    'user_id' => auth()->id(),
                    'tanggal_penjualan' => $validated['tanggal_penjualan'],
                    'customer_id' => $customer->id,
                    'nama_customer_snapshot' => $customer->nama_customer,
                    'produk_siap_jual_id' => $validated['produk_siap_jual_id'],
                    'metode_pembayaran_id' => $validated['metode_pembayaran_id'],
                    'jumlah_pcs' => $jumlahPcs,
                    'qty_pcs' => $jumlahPcs,
                    'harga_satuan' => $hargaSatuan,
                    'total_penjualan' => $kalkulasi['subtotal'],
                    'hpp_total' => $hppTotal,
                    'ongkir' => $ongkir,
                    'diskon' => $diskon,
                    'tipe_diskon' => $tipeDiskon,
                    'total_bayar' => $kalkulasi['total_bayar'],
                    'laba' => $laba,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status_pembayaran' => $validated['status_pembayaran'],
                    'modal_terpakai' => $validated['modal_terpakai'] ?? null,
                    'keterangan_modal' => $validated['keterangan_modal'] ?? null,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penjualan: ' . $e->getMessage());
        }

        return redirect()->route('penjualan.index')
            ->with('success', 'Penjualan berhasil dicatat dan stok telah dikurangi');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['produk.stockGudang', 'produk.produkPaket', 'customer', 'metodePembayaran']);
        
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        $this->authorize('update', $penjualan);
        
        $customers = Customer::where('user_id', auth()->id())->get();
        
        // Ambil produk siap jual dengan stok_siap_jual > 0 atau produk yang sedang diedit
        // SUMBER STOK: stok_siap_jual (dalam PAKET) × pcs_per_paket = total PCS
        $currentProdukId = $penjualan->produk_siap_jual_id;
        $produk = ProdukSiapJual::where('user_id', auth()->id())
            ->where(function ($query) use ($currentProdukId) {
                $query->where('stok_siap_jual', '>', 0)
                      ->orWhere('id', $currentProdukId);
            })
            ->orderBy('nama_produk')
            ->get();
        
        $metode = PaymentMethod::where('user_id', auth()->id())->get();
        return view('penjualan.edit', compact('penjualan', 'customers', 'produk', 'metode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $this->authorize('update', $penjualan);

        $validated = $request->validate([
            'nama_customer' => 'required|string|max:255',
            'tanggal_penjualan' => 'required|date',
            'produk_siap_jual_id' => 'required|exists:produk_siap_juals,id',
            'metode_pembayaran_id' => 'nullable|exists:payment_methods,id',
            'jumlah_pcs' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0.01',
            'diskon' => 'nullable|numeric|min:0',
            'tipe_diskon' => 'nullable|in:persentase,nominal',
            'ongkir' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'status_pembayaran' => 'required|in:lunas,dp,utang',
            'modal_terpakai' => 'nullable|numeric|min:0',
            'keterangan_modal' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $penjualan) {
                // Find or create customer
                $customer = Customer::where('user_id', auth()->id())
                    ->where('nama_customer', $validated['nama_customer'])
                    ->first();

                if (!$customer) {
                    $customer = Customer::create([
                        'user_id' => auth()->id(),
                        'nama_customer' => $validated['nama_customer'],
                    ]);
                }

                // Get product and price from form input
                $produk = ProdukSiapJual::where('user_id', auth()->id())
                    ->findOrFail($validated['produk_siap_jual_id']);

                $hargaSatuan = (float) $validated['harga_satuan'];
                if ($hargaSatuan <= 0) {
                    throw new \Exception('Harga satuan tidak valid.');
                }

                $diskon = (float) ($validated['diskon'] ?? 0);
                $tipeDiskon = $validated['tipe_diskon'] ?? 'nominal';
                $ongkir = (float) ($validated['ongkir'] ?? 0);

                // ✅ Gunakan helper method dari Model untuk kalkulasi konsisten
                // Harga satuan adalah per PAKET, jadi gunakan jumlah paket (bukan PCS)
                $jumlahPaketInput = (int) $validated['jumlah_pcs']; // Input dalam satuan PAKET
                $pcsPerPaket = (int) ($produk->pcs_per_paket ?? 1);
                $jumlahPcsUpdate = $jumlahPaketInput * $pcsPerPaket;
                
                $kalkulasi = Penjualan::hitungPenjualan(
                    $jumlahPaketInput,
                    $hargaSatuan,
                    $diskon,
                    $tipeDiskon,
                    $ongkir
                );

                // Calculate hpp_total and laba (dari subtotal, BUKAN total_bayar)
                if ($produk->isPaket() && $produk->produkPaket) {
                    $hppPerPaket = (float) ($produk->produkPaket->hpp_total ?? 0);
                    $hppTotal = $jumlahPaketInput * $hppPerPaket;
                } else {
                    $hppPerPcs = (float) ($produk->hpp_per_pcs ?? 0);
                    $hppTotal = $jumlahPcsUpdate * $hppPerPcs;
                }
                $laba = $kalkulasi['subtotal'] - $hppTotal; // Laba dihitung dari subtotal (belum dipotong diskon)

                // Update penjualan
                $penjualan->update([
                    'tanggal_penjualan' => $validated['tanggal_penjualan'],
                    'customer_id' => $customer->id,
                    'nama_customer_snapshot' => $customer->nama_customer,
                    'produk_siap_jual_id' => $validated['produk_siap_jual_id'],
                    'metode_pembayaran_id' => $validated['metode_pembayaran_id'],
                    'jumlah_pcs' => $jumlahPcsUpdate,
                    'qty_pcs' => $jumlahPcsUpdate,
                    'harga_satuan' => $hargaSatuan,
                    'total_penjualan' => $kalkulasi['subtotal'],
                    'hpp_total' => $hppTotal,
                    'diskon' => $diskon,
                    'tipe_diskon' => $tipeDiskon,
                    'ongkir' => $ongkir,
                    'total_bayar' => $kalkulasi['total_bayar'],
                    'laba' => $laba,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status_pembayaran' => $validated['status_pembayaran'],
                    'modal_terpakai' => $validated['modal_terpakai'] ?? null,
                    'keterangan_modal' => $validated['keterangan_modal'] ?? null,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penjualan: ' . $e->getMessage());
        }

        return redirect()->route('penjualan.index')
            ->with('success', 'Penjualan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Ketika penjualan dihapus, stok akan di-restore via ProdukSiapJual::restoreStokPenjualan()
     * yang akan mengembalikan stok_siap_jual DAN stok gudang (untuk Single dan Paket)
     */
    public function destroy(Penjualan $penjualan)
    {
        $this->authorize('delete', $penjualan);

        try {
            DB::transaction(function () use ($penjualan) {
                // Ambil produk siap jual terkait
                $produk = ProdukSiapJual::with(['produkPaket.details.stockGudang', 'stockGudang'])
                    ->find($penjualan->produk_siap_jual_id);
                
                if ($produk) {
                    // Hitung jumlah paket yang perlu di-restore
                    $pcsPerPaket = (int) ($produk->pcs_per_paket ?? 1);
                    $jumlahPcs = (int) $penjualan->jumlah_pcs;
                    $paketDirestore = (int) ceil($jumlahPcs / $pcsPerPaket);

                    // Restore stok: stok_siap_jual + stok gudang (untuk kedua tipe)
                    $produk->restoreStokPenjualan($paketDirestore);
                }
                // Jika produk tidak ada, tetap lanjut hapus penjualan tanpa restore stok
                
                // Hapus penjualan
                $penjualan->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus penjualan: ' . $e->getMessage());
        }

        return redirect()->route('penjualan.index')
            ->with('success', 'Penjualan berhasil dihapus dan stok telah di-restore');
    }

    /**
     * Print resi penjualan sebagai PDF
     */
    public function printResi(Penjualan $penjualan)
    {
        $this->authorize('view', $penjualan);
        
        $penjualan->load(['produk.stockGudang', 'produk.produkPaket', 'customer', 'metodePembayaran']);
        
        return view('penjualan.print-resi', compact('penjualan'));
    }

    /**
     * Download resi penjualan sebagai PDF
     */
    public function downloadResi(Penjualan $penjualan)
    {
        $this->authorize('view', $penjualan);
        
        $pdf = Pdf::loadView('penjualan.print-resi', compact('penjualan'));
        
        $filename = 'Resi-Penjualan-' . $penjualan->id . '-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}


