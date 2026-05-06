@extends('layouts.app')

@section('title', 'Catat Penggunaan Modal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            {{-- Warning jika saldo akan negatif --}}
            @if(session('warning_negative'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian!</strong> Penggunaan sebesar 
                    <strong>Rp {{ number_format(session('warning_nominal'), 0, ',', '.') }}</strong> 
                    akan menyebabkan saldo menjadi <strong class="text-danger">NEGATIF</strong>. 
                    Saldo akhir saat ini: <strong>Rp {{ number_format(session('warning_saldo_akhir'), 0, ',', '.') }}</strong>.
                    <hr>
                    <p class="mb-2">Apakah Anda yakin ingin melanjutkan?</p>
                    <form action="{{ route('saldo-modal.penggunaan.store') }}" method="POST" class="d-inline">
                        @csrf
                        {{-- Resend all the form data --}}
                        <input type="hidden" name="saldo_modal_id" value="{{ old('saldo_modal_id') }}">
                        <input type="hidden" name="pembelian_id" value="{{ old('pembelian_id') }}">
                        <input type="hidden" name="nominal" value="{{ old('nominal') }}">
                        <input type="hidden" name="jenis" value="{{ old('jenis') }}">
                        <input type="hidden" name="keterangan" value="{{ old('keterangan') }}">
                        <input type="hidden" name="confirm_negative" value="1">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-check"></i> Ya, Lanjutkan
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="alert">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-minus-circle"></i> Catat Penggunaan Modal</h5>
                </div>
                <div class="card-body">
                    @if($saldoModals->isEmpty())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                            <p>Belum ada saldo modal yang tercatat. Silakan tambah saldo modal terlebih dahulu.</p>
                            <a href="{{ route('saldo-modal.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Saldo Modal
                            </a>
                        </div>
                    @else
                        <form action="{{ route('saldo-modal.penggunaan.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="saldo_modal_id" class="form-label">Pilih Saldo Modal <span class="text-danger">*</span></label>
                                <select class="form-select @error('saldo_modal_id') is-invalid @enderror" 
                                        id="saldo_modal_id" name="saldo_modal_id" required>
                                    <option value="">-- Pilih Saldo Modal --</option>
                                    @foreach($saldoModals as $sm)
                                        <option value="{{ $sm->id }}" 
                                                data-saldo-akhir="{{ $sm->saldo_akhir }}"
                                                {{ old('saldo_modal_id') == $sm->id ? 'selected' : '' }}>
                                            {{ $sm->tanggal->format('d/m/Y') }} - {{ $sm->sumber_modal ?? 'Modal' }} 
                                            (Sisa: Rp {{ number_format($sm->saldo_akhir, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('saldo_modal_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="saldo-info" class="mt-2" style="display:none;">
                                    <small class="text-muted">
                                        Saldo tersisa: <strong id="saldo-tersisa" class="text-info">Rp 0</strong>
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis') is-invalid @enderror" id="jenis" name="jenis" required>
                                    <option value="pengeluaran" {{ old('jenis', 'pengeluaran') == 'pengeluaran' ? 'selected' : '' }}>
                                        Pengeluaran (Menggunakan Modal)
                                    </option>
                                    <option value="pemasukan_kembali" {{ old('jenis') == 'pemasukan_kembali' ? 'selected' : '' }}>
                                        Pemasukan Kembali (Mengembalikan ke Modal)
                                    </option>
                                </select>
                                @error('jenis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="pembelian_id" class="form-label">Tautkan ke Pembelian (Opsional)</label>
                                <select class="form-select @error('pembelian_id') is-invalid @enderror" 
                                        id="pembelian_id" name="pembelian_id">
                                    <option value="">-- Tidak ditautkan --</option>
                                    @foreach($pembelians as $pb)
                                        <option value="{{ $pb->id }}" 
                                                data-nominal="{{ $pb->total_pengeluaran }}"
                                                {{ old('pembelian_id') == $pb->id ? 'selected' : '' }}>
                                            {{ $pb->tanggal_pembelian->format('d/m/Y') }} - {{ $pb->nama_produk }} 
                                            (Rp {{ number_format($pb->total_pengeluaran, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pembelian_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">💡 Pilih pembelian untuk mencatat bahwa dana modal digunakan untuk pembelian tersebut</small>
                            </div>

                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                           id="nominal" name="nominal" value="{{ old('nominal') }}" 
                                           min="1" step="0.01" placeholder="Masukkan nominal" required>
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="warning-negative" class="alert alert-warning mt-2 py-2 px-3" style="display:none;font-size:13px;">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <span id="warning-text">Nominal melebihi saldo tersisa. Saldo akan menjadi negatif.</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" name="keterangan" rows="3" 
                                          placeholder="Catatan penggunaan modal (opsional)" maxlength="1000">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning text-dark" id="btn-simpan">
                                    <i class="fas fa-save"></i> Simpan Penggunaan
                                </button>
                                <a href="{{ route('saldo-modal.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$saldoModals->isEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saldoSelect = document.getElementById('saldo_modal_id');
    const pembelianSelect = document.getElementById('pembelian_id');
    const nominalInput = document.getElementById('nominal');
    const saldoInfo = document.getElementById('saldo-info');
    const saldoTersisa = document.getElementById('saldo-tersisa');
    const warningNeg = document.getElementById('warning-negative');
    const jenisSelect = document.getElementById('jenis');

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR',
            minimumFractionDigits: 0, maximumFractionDigits: 0
        }).format(value);
    }

    function updateInfo() {
        const opt = saldoSelect.options[saldoSelect.selectedIndex];
        if (saldoSelect.value && opt) {
            const sisa = parseFloat(opt.dataset.saldoAkhir) || 0;
            saldoInfo.style.display = 'block';
            saldoTersisa.textContent = formatCurrency(sisa);

            const nominal = parseFloat(nominalInput.value) || 0;
            if (jenisSelect.value === 'pengeluaran' && nominal > sisa && nominal > 0) {
                warningNeg.style.display = 'block';
            } else {
                warningNeg.style.display = 'none';
            }
        } else {
            saldoInfo.style.display = 'none';
            warningNeg.style.display = 'none';
        }
    }

    // Auto-fill nominal when pembelian selected
    pembelianSelect.addEventListener('change', function() {
        if (this.value) {
            const opt = this.options[this.selectedIndex];
            const nom = parseFloat(opt.dataset.nominal) || 0;
            nominalInput.value = nom;
            updateInfo();
        }
    });

    saldoSelect.addEventListener('change', updateInfo);
    nominalInput.addEventListener('input', updateInfo);
    jenisSelect.addEventListener('change', updateInfo);

    // Initial check
    updateInfo();
});
</script>
@endif
@endsection
