<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:categories,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'jenis_kategori' => ['required', 'in:produk,peralatan'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah terdaftar.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'jenis_kategori.required' => 'Jenis kategori wajib dipilih.',
            'jenis_kategori.in' => 'Jenis kategori yang dipilih tidak valid.',
        ];
    }
}
