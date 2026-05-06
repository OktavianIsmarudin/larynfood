<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\StockGudang;
use App\Models\ProdukSiapJual;
use Illuminate\Support\Facades\DB;

class SampleProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create category
        $category = Category::firstOrCreate(
            ['nama_kategori' => 'Makanan Siap Saji'],
            [
                'user_id' => 1,
                'deskripsi' => 'Makanan siap saji dalam kemasan'
            ]
        );

        // Get first user (admin) for user_id
        $userId = DB::table('users')->where('role', 'admin')->first()->id ?? 1;

        // Sample Product 1: Ayam Saus Spesial (Isi Banyak)
        $stock1 = StockGudang::firstOrCreate(
            [
                'sku' => 'AYAM-JUMBO-001',
            ],
            [
                'user_id' => $userId,
                'nama_produk' => 'Ayam Saus Spesial',
                'category_id' => $category->id,
                'satuan' => 'box',
                'konversi_satuan' => 1,
                'jumlah_pack' => 50,
                'jumlah_stock' => 50,
                'pcs_awal' => 50,
                'pcs_terpakai' => 0,
                'pcs_sisa' => 50,
                'harga_beli_pack' => 45000,
                'source' => 'manual',
                'status_stock' => 'sudah_masuk_gudang',
            ]
        );

        ProdukSiapJual::firstOrCreate(
            [
                'nama_produk' => 'Ayam Saus Spesial - Porsi Jumbo',
            ],
            [
                'user_id' => $userId,
                'tipe_produk' => 'single',
                'stock_gudang_id' => $stock1->id,
                'stok_pcs' => 50,
                'stok_siap_jual' => 50,
                'pcs_per_paket' => 1,
                'jumlah_pcs' => 1,
                'hpp_per_pcs' => 45000,
                'hpp_total_per_pcs' => 45000,
                'harga_jual' => 60000,
                'harga_jual_per_pcs' => 60000,
                'margin_laba' => 15000,
            ]
        );

        // Sample Product 2: Udang Saus Premium (Isi Sedang)
        $stock2 = StockGudang::firstOrCreate(
            [
                'sku' => 'UDANG-REG-001',
            ],
            [
                'user_id' => $userId,
                'nama_produk' => 'Udang Saus Premium',
                'category_id' => $category->id,
                'satuan' => 'box',
                'konversi_satuan' => 1,
                'jumlah_pack' => 100,
                'jumlah_stock' => 100,
                'pcs_awal' => 100,
                'pcs_terpakai' => 0,
                'pcs_sisa' => 100,
                'harga_beli_pack' => 18000,
                'source' => 'manual',
                'status_stock' => 'sudah_masuk_gudang',
            ]
        );

        ProdukSiapJual::firstOrCreate(
            [
                'nama_produk' => 'Udang Saus Premium - Porsi Regular',
            ],
            [
                'user_id' => $userId,
                'tipe_produk' => 'single',
                'stock_gudang_id' => $stock2->id,
                'stok_pcs' => 100,
                'stok_siap_jual' => 100,
                'pcs_per_paket' => 1,
                'jumlah_pcs' => 1,
                'hpp_per_pcs' => 18000,
                'hpp_total_per_pcs' => 18000,
                'harga_jual' => 25000,
                'harga_jual_per_pcs' => 25000,
                'margin_laba' => 7000,
            ]
        );

        ProdukSiapJual::firstOrCreate(
            [
                'nama_produk' => 'Udang Saus Premium - Porsi Regular',
                'user_id' => $userId
            ],
            [
                'tipe_produk' => 'single',
                'stock_gudang_id' => $stock2->id,
                'jumlah_per_box' => 1,
                'harga_jual' => 25000,
                'harga_jual_per_pcs' => 25000,
                'stok' => 100,
            ]
        );

        $this->command->info('Sample products created successfully!');
    }
}
