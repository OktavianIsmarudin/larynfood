@extends('layouts.app')

@section('title', 'Tambah Saldo Modal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Saldo Modal / Kas Awal</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Petunjuk:</strong> Masukkan jumlah modal/dana yang Anda siapkan untuk usaha. 
                        Saldo ini akan digunakan untuk melacak penggunaan modal terhadap pembelian dan pengeluaran lainnya.
                    </div>

                    <form action="{{ route('saldo-modal.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                   id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="saldo_awal" class="form-label">Jumlah Saldo Modal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('saldo_awal') is-invalid @enderror" 
                                       id="saldo_awal" name="saldo_awal" value="{{ old('saldo_awal') }}" 
                                       min="1" step="0.01" placeholder="Contoh: 5000000" required>
                            </div>
                            @error('saldo_awal')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jumlah dana yang disiapkan untuk modal usaha</small>
                        </div>

                        <div class="mb-3">
                            <label for="sumber_modal" class="form-label">Sumber Modal</label>
                            <select class="form-select @error('sumber_modal') is-invalid @enderror" 
                                    id="sumber_modal" name="sumber_modal">
                                <option value="">-- Pilih Sumber --</option>
                                <option value="Modal Pribadi" {{ old('sumber_modal') == 'Modal Pribadi' ? 'selected' : '' }}>Modal Pribadi</option>
                                <option value="Pinjaman" {{ old('sumber_modal') == 'Pinjaman' ? 'selected' : '' }}>💳 Pinjaman (Tercatat di Hutang)</option>
                                <option value="Investor" {{ old('sumber_modal') == 'Investor' ? 'selected' : '' }}>Investor</option>
                                <option value="Tabungan Usaha" {{ old('sumber_modal') == 'Tabungan Usaha' ? 'selected' : '' }}>Tabungan Usaha</option>
                                <option value="Lainnya" {{ old('sumber_modal') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('sumber_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Panel Pinjaman / Hutang (muncul saat sumber = Pinjaman) --}}
                        <div class="mb-3" id="panel-pinjaman" style="display:none;">
                            <div class="card border-warning">
                                <div class="card-body bg-light" style="border-radius:8px;">
                                    <h6 class="fw-bold text-warning mb-3">
                                        <i class="fas fa-handshake me-1"></i> Detail Pinjaman
                                    </h6>
                                    <small class="text-muted d-block mb-3">
                                        Modal dari pinjaman akan otomatis tercatat sebagai <strong>Hutang</strong> di halaman Piutang & Hutang.
                                    </small>

                                    {{-- Pilih hutang yang sudah ada atau buat baru --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Hubungkan ke Hutang</label>
                                        <select class="form-select form-select-sm" id="piutang_manual_id" name="piutang_manual_id">
                                            <option value="">-- Buat Hutang Baru (Otomatis) --</option>
                                            @foreach($hutangAktif as $hutang)
                                                <option value="{{ $hutang->id }}" 
                                                    data-nominal="{{ $hutang->nominal }}"
                                                    {{ old('piutang_manual_id') == $hutang->id ? 'selected' : '' }}>
                                                    {{ $hutang->nama_pihak }} — Rp {{ number_format($hutang->nominal, 0, ',', '.') }}
                                                    ({{ $hutang->tanggal->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Pilih hutang yang sudah ada, atau biarkan kosong untuk membuat catatan hutang baru.</small>
                                    </div>

                                    {{-- Form buat hutang baru --}}
                                    <div id="panel-hutang-baru">
                                        <div class="mb-3">
                                            <label for="nama_pihak_pinjaman" class="form-label fw-semibold">
                                                Nama Pemberi Pinjaman <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control form-control-sm" id="nama_pihak_pinjaman"
                                                   name="nama_pihak_pinjaman" value="{{ old('nama_pihak_pinjaman') }}"
                                                   placeholder="Contoh: Bank BRI, Bu Puji, dll">
                                        </div>
                                        <div class="mb-2">
                                            <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Jatuh Tempo</label>
                                            <input type="date" class="form-control form-control-sm" id="tanggal_jatuh_tempo"
                                                   name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo') }}">
                                            <small class="text-muted">Opsional — kapan pinjaman harus dilunasi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="3" 
                                      placeholder="Catatan tambahan (opsional)" maxlength="1000">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('saldo-modal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sumberModal = document.getElementById('sumber_modal');
    const panelPinjaman = document.getElementById('panel-pinjaman');
    const piutangSelect = document.getElementById('piutang_manual_id');
    const panelHutangBaru = document.getElementById('panel-hutang-baru');
    const namaPihak = document.getElementById('nama_pihak_pinjaman');

    function togglePinjaman() {
        if (sumberModal.value === 'Pinjaman') {
            panelPinjaman.style.display = 'block';
        } else {
            panelPinjaman.style.display = 'none';
            // Clear pinjaman fields when not Pinjaman
            if (piutangSelect) piutangSelect.value = '';
            if (namaPihak) namaPihak.value = '';
        }
    }

    function toggleHutangBaru() {
        if (piutangSelect && piutangSelect.value) {
            // Existing hutang selected — hide new hutang form
            panelHutangBaru.style.display = 'none';
        } else {
            // No hutang selected — show form for new hutang
            panelHutangBaru.style.display = 'block';
        }
    }

    sumberModal.addEventListener('change', togglePinjaman);
    if (piutangSelect) piutangSelect.addEventListener('change', toggleHutangBaru);

    // Initialize on page load (for old() repopulation)
    togglePinjaman();
    toggleHutangBaru();
});
</script>
@endsection
