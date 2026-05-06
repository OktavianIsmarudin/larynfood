<?php

namespace App\Http\Controllers;

use App\Models\ProdukSiapJual;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display landing page for guests
     */
    public function index()
    {
        // Ambil produk yang dipublish untuk ditampilkan (limit 9 produk terbaru)
        $products = ProdukSiapJual::with(['stockGudang', 'produkPaket', 'user'])
            ->where('is_published', true)
            ->where('harga_jual_per_pcs', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get();

        // Data untuk carousel promo
        $promos = [
            [
                'title' => 'Paket Hemat Spesial',
                'subtitle' => 'Dapatkan diskon hingga 20% untuk paket pilihan',
                'image' => 'promo1.jpg',
                'cta' => 'Pesan Sekarang',
            ],
            [
                'title' => 'Menu Baru Laryn',
                'subtitle' => 'Coba menu terbaru kami dengan harga terjangkau',
                'image' => 'promo2.jpg',
                'cta' => 'Lihat Menu',
            ],
            [
                'title' => 'Sistem Inventory Terbaik',
                'subtitle' => 'Kelola bisnis makanan Anda dengan mudah',
                'image' => 'promo3.jpg',
                'cta' => 'Daftar Gratis',
            ],
        ];

        return view('welcome', compact('products', 'promos'));
    }
}
