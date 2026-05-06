<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'nama_kategori')->ignore($this->category->id),
            ],
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
        ];
    }
}
