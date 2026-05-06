@php($entry = $entry ?? null)

<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="judul" class="form-control" value="{{ old('judul', $entry->judul ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Topik</label>
    <select name="topik" class="form-select" required>
        @foreach(['umum','produk','checkout','tracking','pembayaran'] as $topic)
            <option value="{{ $topic }}" @selected(old('topik', $entry->topik ?? 'umum') === $topic)>{{ ucfirst($topic) }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Pertanyaan</label>
    <textarea name="pertanyaan" class="form-control" rows="4" required>{{ old('pertanyaan', $entry->pertanyaan ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Jawaban</label>
    <textarea name="jawaban" class="form-control" rows="6" required>{{ old('jawaban', $entry->jawaban ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Kata Kunci</label>
    <textarea name="kata_kunci" class="form-control" rows="3">{{ old('kata_kunci', $entry->kata_kunci ?? '') }}</textarea>
    <small class="text-muted">Pisahkan dengan koma, misalnya: promo, ongkir, diskon</small>
</div>

<div class="mb-3">
    <label class="form-label">Instruksi AI</label>
    <textarea name="instruksi_ai" class="form-control" rows="4">{{ old('instruksi_ai', $entry->instruksi_ai ?? '') }}</textarea>
    <small class="text-muted">Opsional. Dipakai sebagai konteks tambahan untuk AI.</small>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Urutan</label>
        <input type="number" name="urutan" class="form-control" min="0" value="{{ old('urutan', $entry->urutan ?? 0) }}">
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $entry->is_active ?? true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>
