<?php

namespace Database\Seeders;

use App\Models\KnowledgeBaseEntry;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'judul' => 'Sapaan Pembuka',
                'topik' => 'umum',
                'pertanyaan' => 'Halo, hai, selamat datang, apa kabar?',
                'jawaban' => 'Balas dengan perkenalan singkat dulu, lalu panggil user dengan sebutan sobat Laryn. Setelah itu ajak lanjut bertanya tentang produk, checkout, atau tracking.',
                'kata_kunci' => 'halo, hai, selamat datang, apa kabar, hi, salam',
                'instruksi_ai' => 'Gunakan bahasa yang akrab, perkenalkan diri lebih dulu, dan jangan bilang toko ini milik kamu.',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Info Produk',
                'topik' => 'produk',
                'pertanyaan' => 'Cara melihat produk dan menambahkannya ke keranjang',
                'jawaban' => 'Customer bisa buka menu Explore Produk, pilih produk yang diinginkan, lalu klik tambah ke keranjang.',
                'kata_kunci' => 'produk, keranjang, explore, tambah produk',
                'instruksi_ai' => 'Jelaskan langkah yang paling mudah dipahami customer.',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Langkah Checkout',
                'topik' => 'checkout',
                'pertanyaan' => 'Bagaimana cara checkout?',
                'jawaban' => 'Buka keranjang, lanjut checkout, isi data customer, pilih metode pengiriman, lalu kirim pesanan.',
                'kata_kunci' => 'checkout, pesanan, keranjang, alamat, kirim',
                'instruksi_ai' => 'Buat alurnya ringkas dan jelas.',
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'judul' => 'Tracking Pesanan',
                'topik' => 'tracking',
                'pertanyaan' => 'Cara cek status pesanan',
                'jawaban' => 'Customer bisa memasukkan nomor order di fitur tracking untuk melihat status pesanan terbaru.',
                'kata_kunci' => 'tracking, status, order, resi, cek pesanan',
                'instruksi_ai' => 'Fokus ke langkah pengecekan order.',
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'judul' => 'Pembayaran Transfer',
                'topik' => 'pembayaran',
                'pertanyaan' => 'Cara pembayaran dan upload bukti transfer',
                'jawaban' => 'Customer transfer ke rekening yang tersedia lalu upload bukti pembayaran agar pesanan bisa diverifikasi.',
                'kata_kunci' => 'bayar, transfer, bukti, pembayaran, verifikasi',
                'instruksi_ai' => 'Kalau customer tanya pembayaran, beri langkah verifikasi juga.',
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($entries as $entry) {
            KnowledgeBaseEntry::updateOrCreate(
                ['judul' => $entry['judul']],
                $entry
            );
        }
    }
}
