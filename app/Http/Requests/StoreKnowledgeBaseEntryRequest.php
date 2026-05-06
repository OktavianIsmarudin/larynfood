<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKnowledgeBaseEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'topik' => ['required', 'in:umum,produk,checkout,tracking,pembayaran'],
            'pertanyaan' => ['required', 'string', 'max:2000'],
            'jawaban' => ['required', 'string'],
            'kata_kunci' => ['nullable', 'string', 'max:2000'],
            'instruksi_ai' => ['nullable', 'string', 'max:4000'],
            'is_active' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul wajib diisi.',
            'topik.required' => 'Topik wajib dipilih.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
        ];
    }
}
