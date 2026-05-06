@extends('layouts.app')

@section('title', 'Produk Siap Jual')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-box" style="color: #EF4444; margin-right: 12px; opacity: 0.8;"></i> Produk Siap Jual
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola produk dan harga jual dengan HPP</small>
                </div>
                <a href="{{ route('produk-siap-jual.create') }}" class="btn fw-bold" style="background-color: #EF4444; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#DC2626'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)';" onmouseout="this.style.backgroundColor='#EF4444'; this.style.boxShadow='0 2px 8px rgba(239, 68, 68, 0.2)';">
                    <i class="fas fa-plus me-2"></i> Tambah Produk HPP
                </a>
            </div>

            {{-- ALERTS --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #D1FAE5; background-color: #F0FDF4;">
                    <i class="fas fa-check-circle" style="color: #22C55E; margin-right: 8px;"></i> <strong>Sukses!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #FEE2E2; background-color: #FEF2F2;">
                    <i class="fas fa-exclamation-circle" style="color: #DC3545; margin-right: 8px;"></i> <strong>Error!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- FILTER --}}
            <div class="card border-0 mb-3" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body">
                    <form method="GET" action="{{ route('produk-siap-jual.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                        <label class="form-label mb-0 me-2" style="font-weight: 600; color: #6B7280;">Filter Status:</label>
                        <select name="status" class="form-select form-select-sm" style="width: 200px; border-radius: 6px;" onchange="this.form.submit()">
                            <option value="">Semua Produk</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>✓ Dipublikasikan</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>✕ Draft (Tersembunyi)</option>
                        </select>
                        @if(request('status'))
                        <a href="{{ route('produk-siap-jual.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- CARD TABEL --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body p-0">
                    @if ($produk->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama Produk</th>
                                        @if(auth()->user()->role === 'super_admin')
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Pemilik</th>
                                        @endif
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Stok Paket</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">HPP/PCS</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Harga Jual</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Margin Laba</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Status Landing</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produk as $p)
                                        <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">
                                                @if($p->isPaket())
                                                    <span style="background-color: #E8E8FF; color: #5B21B6; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; margin-right: 4px;">PAKET</span>
                                                    {{ $p->produkPaket->nama_paket ?? $p->nama_produk ?? '-' }}
                                                @else
                                                    {{ $p->stockGudang->nama_produk ?? $p->nama_produk ?? '-' }}
                                                @endif
                                                <br><small style="color: #9CA3AF;">{{ $p->pcs_per_paket ?? '?' }} PCS/paket</small>
                                            </td>
                                            @if(auth()->user()->role === 'super_admin')
                                            <td style="padding: 16px 20px;">
                                                <span style="background-color: #F3E8FF; color: #7C3AED; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                    {{ $p->user->name ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            @endif
                                            <td style="padding: 16px 20px;">
                                                <span style="background-color: #E0F2FE; color: #0369A1; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">{{ $p->stok_siap_jual ?? 0 }} paket</span>
                                                <br>
                                                @if($p->isPaket())
                                                    <small style="color: #9CA3AF;">{{ $p->produkPaket->details->count() ?? 0 }} item komponen</small>
                                                @else
                                                    <small style="color: #9CA3AF;">Gudang: {{ $p->stockGudang->pcs_sisa ?? 0 }} PCS</small>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 500;">
                                                @if($p->isPaket() && $p->produkPaket)
                                                    Rp {{ number_format($p->produkPaket->hpp_total ?? 0, 0, ',', '.') }}
                                                    <br><small style="color: #9CA3AF;">/paket</small>
                                                @else
                                                    Rp {{ number_format($p->hpp_per_pcs ?? 0, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #EF4444; font-weight: 600;">Rp {{ number_format($p->harga_jual ?? 0, 0, ',', '.') }}</td>
                                            <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 500;">{{ $p->margin_laba ?? '0' }}%</td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <button class="btn btn-sm toggle-publish-btn" 
                                                        data-id="{{ $p->id }}" 
                                                        data-published="{{ $p->is_published ? '1' : '0' }}"
                                                        style="background-color: {{ $p->is_published ? '#DCFCE7' : '#FEE2E2' }}; 
                                                               color: {{ $p->is_published ? '#166534' : '#B91C1C' }}; 
                                                               border: none; border-radius: 6px; padding: 6px 12px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;"
                                                        title="{{ $p->is_published ? 'Klik untuk sembunyikan' : 'Klik untuk publikasikan' }}">
                                                    <i class="fas {{ $p->is_published ? 'fa-eye' : 'fa-eye-slash' }} me-1"></i>
                                                    <span class="status-text">{{ $p->is_published ? 'Published' : 'Draft' }}</span>
                                                </button>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                    @if(!$p->isPaket())
                                                    <button class="btn btn-sm" style="background-color: #DCFCE7; color: #166534; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalTambahStock{{ $p->id }}" title="Tambah Stock Siap Jual" onmouseover="this.style.backgroundColor='#BBFBEE'; this.style.boxShadow='0 2px 6px rgba(22, 101, 52, 0.2)';" onmouseout="this.style.backgroundColor='#DCFCE7'; this.style.boxShadow='none';">
                                                        <i class="fas fa-plus-circle"></i>
                                                    </button>
                                                    @endif
                                                    <a href="{{ route('produk-siap-jual.show', $p) }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0284C7; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Lihat Detail HPP" onmouseover="this.style.backgroundColor='#BFDBFE'; this.style.boxShadow='0 2px 6px rgba(2, 132, 199, 0.2)';" onmouseout="this.style.backgroundColor='#E0F2FE'; this.style.boxShadow='none';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('produk-siap-jual.edit', $p) }}" class="btn btn-sm" style="background-color: #FEF3C7; color: #92400E; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Edit" onmouseover="this.style.backgroundColor='#FDE68A'; this.style.boxShadow='0 2px 6px rgba(146, 64, 14, 0.2)';" onmouseout="this.style.backgroundColor='#FEF3C7'; this.style.boxShadow='none';">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <form action="{{ route('produk-siap-jual.destroy', $p) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;" title="Hapus" onclick="return confirm('Yakin ingin menghapus?');" onmouseover="this.style.backgroundColor='#FCA5A5'; this.style.boxShadow='0 2px 6px rgba(185, 28, 28, 0.2)';" onmouseout="this.style.backgroundColor='#FEE2E2'; this.style.boxShadow='none';">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="d-flex justify-content-center p-4">
                            {{ $produk->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; background-color: #F9FAFB; border-radius: 12px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #D1D5DB; margin-bottom: 20px; display: block;"></i>
                            <h5 style="color: #6B7280; font-weight: 600; margin-bottom: 10px;">Belum ada data produk siap jual</h5>
                            <p style="color: #9CA3AF; margin-bottom: 24px;">Buat produk HPP untuk mengelola harga jual</p>
                            <a href="{{ route('produk-siap-jual.create') }}" class="btn fw-bold" style="background-color: #EF4444; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#DC2626'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)';" onmouseout="this.style.backgroundColor='#EF4444'; this.style.boxShadow='none';">
                                <i class="fas fa-plus me-2"></i> Buat Produk Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Stock untuk setiap produk -->
@foreach ($produk as $p)
<div class="modal fade" id="modalTambahStock{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #EF4444; color: white; border: none;">
                <h5 class="modal-title" style="font-weight: 600;">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Stock Siap Jual
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            @if (!$p->pcs_per_paket || $p->pcs_per_paket <= 0)
                <div class="modal-body">
                    <div class="alert alert-danger" style="border-radius: 8px; border: 1px solid #FEE2E2; background-color: #FEF2F2;">
                        <i class="fas fa-exclamation-circle" style="color: #DC3545; margin-right: 8px;"></i> 
                        <strong>Isi PCS per Paket belum dikonfigurasi!</strong><br>
                        <small>Silakan edit produk terlebih dahulu untuk mengatur "Isi PCS per Paket"</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #E5E7EB;">
                    <a href="{{ route('produk-siap-jual.edit', $p) }}" class="btn" style="background-color: #3B82F6; color: white; border: none;">
                        <i class="fas fa-edit me-2"></i> Edit Produk
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            @else
                <form action="{{ route('produk-siap-jual.tambah-stock', $p) }}" method="POST" id="formTambahStock{{ $p->id }}">
                    @csrf
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600; color: #1A1A1A;">{{ $p->stockGudang->nama_produk ?? '-' }}</label>
                            <div class="alert alert-info" style="border-radius: 8px; border: 1px solid #DBEAFE; background-color: #E0F2FE;">
                                <small>
                                    <i class="fas fa-info-circle" style="color: #0369A1; margin-right: 4px;"></i> Stok Siap Jual Saat Ini: <strong>{{ $p->stok_siap_jual ?? 0 }} paket</strong><br>
                                    Stock Gudang Tersedia: <strong>{{ $p->stockGudang->pcs_sisa ?? 0 }} PCS</strong><br>
                                    <strong>Konversi:</strong> 1 paket = <strong style="color: #EF4444; font-size: 1.05em;">{{ $p->pcs_per_paket ?? 1 }} PCS</strong>
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_paket{{ $p->id }}" class="form-label" style="font-weight: 600; color: #1A1A1A;">Jumlah Paket <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="jumlah_paket{{ $p->id }}" 
                                   name="jumlah_paket" 
                                   min="1" 
                                   required
                                   placeholder="Contoh: 2"
                                   data-pcs-per-paket="{{ $p->pcs_per_paket ?? 1 }}"
                                   onchange="updatePcsCalculation({{ $p->id }}, parseFloat(document.getElementById('jumlah_paket{{ $p->id }}').dataset.pcsPerPaket), {{ $p->stockGudang->pcs_sisa ?? 0 }})"
                                   oninput="updatePcsCalculation({{ $p->id }}, parseFloat(document.getElementById('jumlah_paket{{ $p->id }}').dataset.pcsPerPaket), {{ $p->stockGudang->pcs_sisa ?? 0 }})"
                                   style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px;">
                            <small style="color: #6B7280; display: block; margin-top: 8px;">
                                Masukkan jumlah PAKET (bukan PCS). 1 paket = {{ $p->pcs_per_paket ?? 1 }} PCS
                            </small>
                        </div>

                        <div class="mb-3">
                            <label style="font-weight: 600; color: #1A1A1A;">Total PCS yang dibutuhkan:</label>
                            <div class="input-group" style="border: 1px solid #D1D5DB; border-radius: 8px; background-color: #F9FAFB;">
                                <input type="text" 
                                       class="form-control" 
                                       id="totalPcs{{ $p->id }}" 
                                       disabled
                                       placeholder="Akan dihitung otomatis"
                                       style="border: none; background-color: transparent; font-weight: 500;">
                                <span class="input-group-text" style="font-size: 0.85em; background-color: transparent; border: none; color: #6B7280;">
                                    <span id="formulaDisplay{{ $p->id }}">0 × {{ $p->pcs_per_paket ?? 1 }}</span>
                                </span>
                            </div>
                            <small id="validation{{ $p->id }}" class="d-block mt-1" style="font-size: 0.9em;"></small>
                        </div>

                        <div class="alert alert-warning" style="border-radius: 8px; border: 1px solid #FEF3C7; background-color: #FFFBEB;">
                            <small style="color: #92400E;">
                                <i class="fas fa-exclamation-triangle" style="margin-right: 4px;"></i> 
                                <strong>Perhatian:</strong> Stock Gudang akan berkurang sesuai PCS yang ditambahkan.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid #E5E7EB;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn" style="background-color: #22C55E; color: white; border: none;">
                            <i class="fas fa-check me-2"></i> Tambah Stock
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endforeach

<script>
/**
 * Update PCS calculation real-time
 * LOGIKA:
 * - jumlah_paket (input user) = satuan PAKET
 * - pcsPerPaket (dari HPP) = 1 paket = berapa PCS
 * - totalPcs = jumlah_paket × pcsPerPaket
 * - Validate: totalPcs <= stockGudangPcs
 */
function updatePcsCalculation(produkId, pcsPerPaket, stockGudangPcs) {
    const inputElement = document.getElementById('jumlah_paket' + produkId);
    const totalPcsElement = document.getElementById('totalPcs' + produkId);
    const validationElement = document.getElementById('validation' + produkId);
    const formulaDisplay = document.getElementById('formulaDisplay' + produkId);
    const submitBtn = document.querySelector('#formTambahStock' + produkId + ' button[type="submit"]');
    
    // Get pcsPerPaket - HARUS dari data attribute untuk avoid inline JS issue
    const actualPcsPerPaket = parseFloat(inputElement.dataset.pcsPerPaket) || 1;
    
    const jumlahPaket = parseInt(inputElement.value) || 0;
    const totalPcs = jumlahPaket * actualPcsPerPaket;
    
    console.log(`[DEBUG] Produk ID: ${produkId}, pcsPerPaket: ${actualPcsPerPaket}, jumlahPaket: ${jumlahPaket}, totalPcs: ${totalPcs}, stockGudang: ${stockGudangPcs}`);
    
    // Update formula display
    if (formulaDisplay) {
        formulaDisplay.textContent = `${jumlahPaket} × ${actualPcsPerPaket}`;
    }
    
    // Tampilkan total PCS
    if (jumlahPaket > 0) {
        totalPcsElement.value = totalPcs + ' PCS';
    } else {
        totalPcsElement.value = '';
    }
    
    // Validasi client-side
    if (jumlahPaket > 0) {
        if (totalPcs > stockGudangPcs) {
            // Tidak cukup stock
            const selisih = totalPcs - stockGudangPcs;
            validationElement.innerHTML = `
                <span style="color: #DC2626;">
                    <i class="fas fa-exclamation-circle"></i> 
                    Stock gudang tidak cukup. Dibutuhkan ${totalPcs} PCS, tersedia ${stockGudangPcs} PCS (kurang ${selisih} PCS)
                </span>
            `;
            validationElement.style.display = 'block';
            submitBtn.disabled = true;
            inputElement.classList.add('is-invalid');
        } else {
            // Stock cukup
            validationElement.innerHTML = `
                <span style="color: #166534;">
                    <i class="fas fa-check-circle"></i> 
                    Stock gudang cukup. Akan digunakan ${totalPcs} PCS dari ${stockGudangPcs} PCS yang tersedia
                </span>
            `;
            validationElement.style.display = 'block';
            submitBtn.disabled = false;
            inputElement.classList.remove('is-invalid');
        }
    } else {
        validationElement.innerHTML = '';
        validationElement.style.display = 'none';
        submitBtn.disabled = true;
        inputElement.classList.remove('is-invalid');
    }
}
</script>

<script>
// Toggle Publish Status
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-publish-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const isPublished = this.dataset.published === '1';
            const btn = this;
            const icon = btn.querySelector('i');
            const statusText = btn.querySelector('.status-text');
            
            // Disable button
            btn.disabled = true;
            btn.style.opacity = '0.6';
            
            // Send AJAX request
            fetch(`/produk-siap-jual/${productId}/toggle-publish`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button UI
                    btn.dataset.published = data.is_published ? '1' : '0';
                    
                    if (data.is_published) {
                        btn.style.backgroundColor = '#DCFCE7';
                        btn.style.color = '#166534';
                        icon.className = 'fas fa-eye me-1';
                        statusText.textContent = 'Published';
                        btn.title = 'Klik untuk sembunyikan';
                    } else {
                        btn.style.backgroundColor = '#FEE2E2';
                        btn.style.color = '#B91C1C';
                        icon.className = 'fas fa-eye-slash me-1';
                        statusText.textContent = 'Draft';
                        btn.title = 'Klik untuk publikasikan';
                    }
                    
                    // Show success message
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message || 'Gagal mengubah status produk');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat mengubah status produk');
            })
            .finally(() => {
                // Re-enable button
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        });
    });
});

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    const iconColor = type === 'success' ? '#22C55E' : '#DC3545';
    
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 8px;';
    alertDiv.innerHTML = `
        <i class="fas ${icon}" style="color: ${iconColor}; margin-right: 8px;"></i> 
        <strong>${type === 'success' ? 'Berhasil!' : 'Gagal!'}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

@endsection
