<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'nama_customer_snapshot',
        'produk_siap_jual_id',
        'metode_pembayaran_id',
        'jumlah_pcs',
        'qty_pcs',
        'harga_satuan',
        'total_penjualan',
        'hpp_total',
        'ongkir',
        'diskon',
        'tipe_diskon',
        'promo',
        'pajak',
        'alamat_pengiriman',
        'metode_pengiriman',
        'keterangan',
        'bukti_pembayaran',
        'total_bayar',
        'laba',
        'modal_terpakai',
        'keterangan_modal',
        'status_pembayaran',
        'tanggal_penjualan',
    ];

    protected $casts = [
        'jumlah_pcs' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_penjualan' => 'decimal:2',
        'hpp_total' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'diskon' => 'decimal:2',
        'promo' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'laba' => 'decimal:2',
        'modal_terpakai' => 'decimal:2',
        'tanggal_penjualan' => 'date',
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(ProdukSiapJual::class, 'produk_siap_jual_id');
    }

    public function metodePembayaran(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'metode_pembayaran_id');
    }

    // ========================================
    // ACCESSORS - DISPLAY
    // ========================================

    /**
     * Nama produk yang dijual (resolve dari produk_siap_jual)
     * - Paket: produkPaket.nama_paket
     * - Single: stockGudang.nama_produk atau nama_produk
     * - Fallback: '-'
     */
    protected function namaProdukDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                $produk = $this->produk;
                if (!$produk) {
                    return '-';
                }

                // Produk Paket
                if ($produk->isPaket() && $produk->produkPaket) {
                    return $produk->produkPaket->nama_paket;
                }

                // Produk Single
                return $produk->stockGudang->nama_produk
                    ?? $produk->nama_produk
                    ?? '-';
            }
        );
    }

    // ========================================
    // ACCESSORS - KALKULASI PENJUALAN
    // ========================================

    /**
     * SUBTOTAL = qty × harga_satuan
     * Sebelum diskon dan ongkir
     */
    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->jumlah_pcs * (float) $this->harga_satuan
        );
    }

    /**
     * NILAI DISKON (dalam Rupiah)
     *
     * Jika tipe_diskon = 'persentase': subtotal × (diskon / 100)
     * Jika tipe_diskon = 'nominal': diskon langsung
     * Nilai diskon tidak boleh melebihi subtotal
     */
    protected function nilaiDiskon(): Attribute
    {
        return Attribute::make(
            get: function () {
                $subtotal = $this->subtotal;
                $diskon = (float) ($this->diskon ?? 0);
                $tipeDiskon = $this->tipe_diskon ?? 'nominal';

                if ($diskon <= 0) {
                    return 0;
                }

                if ($tipeDiskon === 'persentase') {
                    $nilaiDiskon = $subtotal * ($diskon / 100);
                } else {
                    $nilaiDiskon = $diskon;
                }

                // Diskon tidak boleh melebihi subtotal
                return min($nilaiDiskon, $subtotal);
            }
        );
    }

    /**
     * Label diskon untuk display
     * Contoh: "10%" atau "Rp 50.000"
     */
    protected function labelDiskon(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskon = (float) ($this->diskon ?? 0);
                $tipeDiskon = $this->tipe_diskon ?? 'nominal';

                if ($diskon <= 0) {
                    return '-';
                }

                if ($tipeDiskon === 'persentase') {
                    return number_format($diskon, 0) . '%';
                } else {
                    return 'Rp ' . number_format($diskon, 0, ',', '.');
                }
            }
        );
    }

    /**
     * SUBTOTAL SETELAH DISKON
     * subtotal - nilai_diskon
     */
    protected function subtotalSetelahDiskon(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->subtotal - $this->nilai_diskon)
        );
    }

    /**
     * ONGKIR (Shipping Cost)
     * Getter dengan default 0
     */
    protected function ongkirDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) ($this->ongkir ?? 0)
        );
    }

    /**
     * TOTAL BAYAR CALCULATED
     * = subtotal - nilai_diskon + ongkir
     * Tidak boleh negatif
     */
    protected function totalBayarCalculated(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->subtotal_setelah_diskon + $this->ongkir_display)
        );
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForUser($query, $userId = null)
    {
        // If userId is null, return all records (untuk super admin mode agregat)
        if ($userId === null) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_penjualan', [$startDate, $endDate->endOfDay()]);
    }

    public function scopeUtang($query)
    {
        return $query->where('status_pembayaran', 'utang');
    }

    public function scopeLunas($query)
    {
        return $query->where('status_pembayaran', 'lunas');
    }

    // ========================================
    // STATIC HELPER - KALKULASI PENJUALAN
    // ========================================

    /**
     * Calculate all sales values from input
     * Use this for consistent calculation in controller
     *
     * @param int $qty Jumlah PCS
     * @param float $hargaSatuan Harga per PCS
     * @param float $diskon Nilai diskon (nominal atau persentase)
     * @param string $tipeDiskon 'nominal' atau 'persentase'
     * @param float $ongkir Biaya pengiriman
     * @return array
     */
    public static function hitungPenjualan(
        int $qty,
        float $hargaSatuan,
        float $diskon = 0,
        string $tipeDiskon = 'nominal',
        float $ongkir = 0
    ): array {
        // STEP 1: Hitung subtotal
        $subtotal = $qty * $hargaSatuan;

        // STEP 2: Hitung nilai diskon
        $nilaiDiskon = 0;
        if ($diskon > 0) {
            if ($tipeDiskon === 'persentase') {
                $nilaiDiskon = $subtotal * ($diskon / 100);
            } else {
                $nilaiDiskon = $diskon;
            }
            // Pastikan diskon tidak melebihi subtotal
            $nilaiDiskon = min($nilaiDiskon, $subtotal);
        }

        // STEP 3: Hitung subtotal setelah diskon
        $subtotalSetelahDiskon = max(0, $subtotal - $nilaiDiskon);

        // STEP 4: Hitung total bayar (tambahkan ongkir)
        $totalBayar = max(0, $subtotalSetelahDiskon + $ongkir);

        return [
            'subtotal' => round($subtotal, 2),
            'nilai_diskon' => round($nilaiDiskon, 2),
            'subtotal_setelah_diskon' => round($subtotalSetelahDiskon, 2),
            'ongkir' => round($ongkir, 2),
            'total_bayar' => round($totalBayar, 2),
        ];
    }
}
