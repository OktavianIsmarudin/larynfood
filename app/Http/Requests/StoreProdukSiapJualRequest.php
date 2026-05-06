<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProdukSiapJualRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Stock gudang / produk paket must belong to authenticated user
        // Validated in rules() with custom validation
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * EXTENSION: Support untuk tipe_produk (single/paket)
     * - Single: stock_gudang_id required
     * - Paket: produk_paket_id required (stock_gudang_id nullable)
     */
    public function rules(): array
    {
        $tipeProduk = $this->input('tipe_produk', 'single');

        $rules = [
            // Tipe produk (single atau paket)
            'tipe_produk' => 'nullable|in:single,paket',

            'hpp_per_pcs' => 'required|numeric|min:0.01',
            'pcs_per_paket' => 'required|integer|min:1',
            'margin_laba' => 'nullable|numeric|min:0|max:1000',
            'biaya_packing' => 'nullable|numeric|min:0',
            'biaya_saos' => 'nullable|numeric|min:0',
            'biaya_sumpit' => 'nullable|numeric|min:0',
            'biaya_tenaga' => 'nullable|numeric|min:0',
            'nama_produk' => 'nullable|string|max:255',
            'gambar_produk' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Conditional validation berdasarkan tipe_produk
        if ($tipeProduk === 'paket') {
            // Untuk tipe paket: produk_paket_id required, stock_gudang_id nullable
            $rules['produk_paket_id'] = [
                'required',
                'exists:produk_pakets,id',
                Rule::exists('produk_pakets', 'id')->where('user_id', auth()->id()),
            ];
            $rules['stock_gudang_id'] = 'nullable';
        } else {
            // Untuk tipe single (default): stock_gudang_id required
            $rules['stock_gudang_id'] = [
                'required',
                'exists:stock_gudang,id',
                Rule::exists('stock_gudang', 'id')->where('user_id', auth()->id()),
            ];
            $rules['produk_paket_id'] = 'nullable';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'tipe_produk.in' => 'Tipe produk harus single atau paket',
            'stock_gudang_id.required' => 'Stock produk wajib dipilih untuk produk tunggal',
            'produk_paket_id.required' => 'Produk paket wajib dipilih untuk tipe paket',
            'produk_paket_id.exists' => 'Produk paket tidak ditemukan',
            'hpp_per_pcs.required' => 'HPP per PCS wajib diisi',
            'hpp_per_pcs.numeric' => 'HPP per PCS harus berupa angka',
            'hpp_per_pcs.min' => 'HPP per PCS tidak boleh negatif',
            'pcs_per_paket.required' => 'Isi PCS per Paket wajib diisi',
            'pcs_per_paket.integer' => 'Isi PCS per Paket harus berupa angka bulat',
            'pcs_per_paket.min' => 'Isi PCS per Paket minimal 1',
            'margin_laba.numeric' => 'Margin laba harus berupa angka',
            'margin_laba.min' => 'Margin laba tidak boleh negatif',
            'biaya_packing.numeric' => 'Biaya packing harus berupa angka',
            'biaya_saos.numeric' => 'Biaya saos harus berupa angka',
            'biaya_sumpit.numeric' => 'Biaya sumpit harus berupa angka',
            'biaya_tenaga.numeric' => 'Biaya tenaga harus berupa angka',
        ];
    }
}
