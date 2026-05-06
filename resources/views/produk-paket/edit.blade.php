@extends('layouts.app')

@section('title', 'Edit Produk Paket')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('produk-paket.show', $produkPaket) }}" style="color: #6B7280; text-decoration: none; display: flex; align-items: center; font-size: 14px; margin-bottom: 8px;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali ke Detail Paket
                    </a>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-edit" style="color: #8B5CF6; margin-right: 12px; opacity: 0.8;"></i> Edit Paket
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">{{ $produkPaket->nama_paket }}</small>
                </div>
            </div>

            {{-- ALERTS --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #FEE2E2; background-color: #FEF2F2;">
                    <i class="fas fa-exclamation-circle" style="color: #DC3545; margin-right: 8px;"></i> <strong>Error!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('produk-paket.update', $produkPaket) }}" method="POST" id="formPaket">
                @csrf
                @method('PUT')
                
                {{-- INFO PAKET --}}
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                    <div class="card-header" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                        <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                            <i class="fas fa-info-circle" style="color: #8B5CF6; margin-right: 8px;"></i> Informasi Paket
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 24px;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="color: #374151; font-weight: 500;">Nama Paket <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="nama_paket" class="form-control" value="{{ old('nama_paket', $produkPaket->nama_paket) }}" required placeholder="Contoh: Nasi Box Spesial" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #E5E7EB;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" style="color: #374151; font-weight: 500;">Kode Paket</label>
                                <input type="text" name="kode_paket" class="form-control" value="{{ old('kode_paket', $produkPaket->kode_paket) }}" placeholder="Contoh: PKT001" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #E5E7EB;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" style="color: #374151; font-weight: 500;">Status <span style="color: #EF4444;">*</span></label>
                                <select name="status" class="form-select" required style="border-radius: 8px; padding: 12px 16px; border: 1px solid #E5E7EB;">
                                    <option value="aktif" {{ old('status', $produkPaket->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $produkPaket->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" style="color: #374151; font-weight: 500;">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi singkat tentang paket ini" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #E5E7EB;">{{ old('deskripsi', $produkPaket->deskripsi) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ITEM PAKET --}}
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                        <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                            <i class="fas fa-list" style="color: #8B5CF6; margin-right: 8px;"></i> Komponen Paket
                        </h5>
                        <button type="button" class="btn btn-sm" id="btnTambahItem" style="background-color: #8B5CF6; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-size: 12px;">
                            <i class="fas fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body" style="padding: 24px;">
                        <div class="table-responsive">
                            <table class="table" id="tableItems" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 12px;">Item dari Stock Gudang</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 12px; width: 120px;">Qty/Paket</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 12px; text-align: right; width: 140px;">HPP/PCS</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 12px; text-align: right; width: 160px;">Subtotal HPP</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 12px; width: 60px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer">
                                    {{-- Existing items will be populated here --}}
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #F0F9FF; border-top: 2px solid #8B5CF6;">
                                        <td colspan="3" style="padding: 16px; text-align: right; font-weight: 600; color: #1A1A1A;">Total HPP Paket:</td>
                                        <td style="padding: 16px; text-align: right; font-weight: 700; color: #8B5CF6; font-size: 16px;" id="totalHpp">Rp {{ number_format($produkPaket->hpp_total ?? 0, 0, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        {{-- Empty state --}}
                        <div id="emptyState" class="text-center py-4" style="display: none;">
                            <i class="fas fa-boxes" style="font-size: 48px; color: #D1D5DB; margin-bottom: 16px;"></i>
                            <p style="color: #6B7280; margin: 0;">Belum ada komponen. Klik <strong>"+ Tambah Item"</strong> untuk menambahkan.</p>
                        </div>
                        
                        <p class="text-muted mb-0 mt-3" style="font-size: 12px;">
                            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                            HPP Total = SUM(Qty × HPP/PCS) dari semua komponen. Stok gudang <strong>TIDAK</strong> akan dikurangi.
                        </p>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('produk-paket.show', $produkPaket) }}" class="btn" style="background-color: #F3F4F6; color: #374151; border: 1px solid #E5E7EB; padding: 12px 24px; border-radius: 8px;">
                        <i class="fas fa-times me-2"></i> Batal
                    </a>
                    <button type="submit" class="btn" style="background-color: #8B5CF6; color: white; padding: 12px 24px; border-radius: 8px; border: none;">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Template untuk item row --}}
<template id="itemRowTemplate">
    <tr class="item-row" style="border-bottom: 1px solid #E5E7EB;">
        <td style="padding: 12px;">
            <select name="items[INDEX][stock_gudang_id]" class="form-select stock-select" required style="border-radius: 8px; padding: 10px 12px; border: 1px solid #E5E7EB; font-size: 13px;">
                <option value="">-- Pilih Item Stock Gudang --</option>
                @foreach($stocks as $stock)
                    @php
                        $hppPerPcs = ($stock->harga_beli_pack ?? 0) / max(1, $stock->konversi_satuan);
                    @endphp
                    <option value="{{ $stock->id }}" 
                            data-hpp="{{ $hppPerPcs }}"
                            data-nama="{{ $stock->nama_produk }}"
                            data-stok="{{ $stock->pcs_sisa ?? 0 }}">
                        {{ $stock->nama_produk }} ({{ $stock->pcs_sisa ?? 0 }} PCS) - {{ $stock->category->nama_kategori ?? 'Tanpa Kategori' }}
                    </option>
                @endforeach
            </select>
        </td>
        <td style="padding: 12px;">
            <input type="number" name="items[INDEX][qty_per_paket]" class="form-control qty-input" step="0.01" min="0.01" required placeholder="0" style="border-radius: 8px; padding: 10px 12px; border: 1px solid #E5E7EB; text-align: right;">
        </td>
        <td style="padding: 12px; text-align: right;">
            <span class="hpp-pcs-display" style="font-weight: 500; color: #6B7280; font-size: 13px;">Rp 0</span>
            <input type="hidden" class="hpp-pcs-value" value="0">
        </td>
        <td style="padding: 12px; text-align: right;">
            <span class="subtotal-hpp-display" style="font-weight: 600; color: #1A1A1A;">Rp 0</span>
        </td>
        <td style="padding: 12px; text-align: center;">
            <button type="button" class="btn btn-sm btn-remove-item" style="background-color: #FEE2E2; color: #B91C1C; border: none; border-radius: 6px; padding: 6px 10px;" title="Hapus item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@section('extra-js')
@php
    $existingItemsData = $produkPaket->details->map(function($detail) {
        $stock = $detail->stockGudang;
        $hppPerPcs = $stock ? ($stock->harga_beli_pack ?? 0) / max(1, $stock->konversi_satuan) : 0;
        return [
            'stock_gudang_id' => $detail->stock_gudang_id,
            'qty_per_paket' => $detail->qty_per_paket,
            'hpp_per_pcs' => $hppPerPcs,
        ];
    })->toArray();
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ====== CONFIGURATION ======
    let itemIndex = 0;
    const container = document.getElementById('itemsContainer');
    const template = document.getElementById('itemRowTemplate');
    const btnTambah = document.getElementById('btnTambahItem');
    const emptyState = document.getElementById('emptyState');
    const totalHppDisplay = document.getElementById('totalHpp');
    
    // Existing items from server
    const existingItems = @json($existingItemsData);
    
    // ====== UTILITY FUNCTIONS ======
    function formatRupiah(angka) {
        return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { 
            minimumFractionDigits: 0, 
            maximumFractionDigits: 0 
        });
    }
    
    function updateEmptyState() {
        const hasItems = container.querySelectorAll('.item-row').length > 0;
        emptyState.style.display = hasItems ? 'none' : 'block';
        document.getElementById('tableItems').querySelector('thead').style.display = hasItems ? '' : 'none';
        document.getElementById('tableItems').querySelector('tfoot').style.display = hasItems ? '' : 'none';
    }
    
    // ====== ADD NEW ITEM ROW ======
    function addItemRow(data = null) {
        // Clone template
        const content = template.content.cloneNode(true);
        const row = content.querySelector('tr');
        
        // Replace INDEX placeholder with array notation
        row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);
        
        // Append to container
        container.appendChild(row);
        
        // Get row elements
        const stockSelect = row.querySelector('.stock-select');
        const qtyInput = row.querySelector('.qty-input');
        const hppPcsDisplay = row.querySelector('.hpp-pcs-display');
        const hppPcsValue = row.querySelector('.hpp-pcs-value');
        const subtotalDisplay = row.querySelector('.subtotal-hpp-display');
        const removeBtn = row.querySelector('.btn-remove-item');
        
        // If data provided (existing item), populate the fields
        if (data) {
            stockSelect.value = data.stock_gudang_id;
            qtyInput.value = data.qty_per_paket;
            
            // Use provided HPP or get from option
            let hppPerPcs = data.hpp_per_pcs || 0;
            if (!hppPerPcs && stockSelect.value) {
                const option = stockSelect.options[stockSelect.selectedIndex];
                hppPerPcs = parseFloat(option.dataset.hpp) || 0;
            }
            
            hppPcsValue.value = hppPerPcs;
            hppPcsDisplay.textContent = formatRupiah(hppPerPcs);
            
            // Calculate subtotal
            const qty = parseFloat(data.qty_per_paket) || 0;
            subtotalDisplay.textContent = formatRupiah(hppPerPcs * qty);
        }
        
        // Event: Stock selection changes
        stockSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const hppPerPcs = parseFloat(option.dataset.hpp) || 0;
            
            // Update HPP/PCS display
            hppPcsDisplay.textContent = formatRupiah(hppPerPcs);
            hppPcsValue.value = hppPerPcs;
            
            // Recalculate subtotal
            calculateRowSubtotal(row);
        });
        
        // Event: Qty changes
        qtyInput.addEventListener('input', function() {
            calculateRowSubtotal(row);
        });
        
        // Event: Remove row
        removeBtn.addEventListener('click', function() {
            row.remove();
            calculateTotalHpp();
            updateEmptyState();
        });
        
        itemIndex++;
        updateEmptyState();
        
        // Focus on the new dropdown if no data (new row)
        if (!data) {
            stockSelect.focus();
        }
    }
    
    // ====== CALCULATE ROW SUBTOTAL ======
    function calculateRowSubtotal(row) {
        const hppPcsValue = row.querySelector('.hpp-pcs-value');
        const qtyInput = row.querySelector('.qty-input');
        const subtotalDisplay = row.querySelector('.subtotal-hpp-display');
        
        const hppPerPcs = parseFloat(hppPcsValue.value) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        const subtotal = hppPerPcs * qty;
        
        subtotalDisplay.textContent = formatRupiah(subtotal);
        
        // Recalculate total
        calculateTotalHpp();
    }
    
    // ====== CALCULATE TOTAL HPP ======
    function calculateTotalHpp() {
        let total = 0;
        
        container.querySelectorAll('.item-row').forEach(row => {
            const hppPcsValue = row.querySelector('.hpp-pcs-value');
            const qtyInput = row.querySelector('.qty-input');
            
            const hppPerPcs = parseFloat(hppPcsValue.value) || 0;
            const qty = parseFloat(qtyInput.value) || 0;
            
            total += hppPerPcs * qty;
        });
        
        totalHppDisplay.textContent = formatRupiah(total);
    }
    
    // ====== EVENT LISTENERS ======
    btnTambah.addEventListener('click', function() {
        addItemRow();
    });
    
    // ====== FORM VALIDATION ======
    document.querySelector('form').addEventListener('submit', function(e) {
        const itemCount = container.querySelectorAll('.item-row').length;
        
        if (itemCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 item dalam paket!');
            return false;
        }
        
        // Validate each row has selected item
        let valid = true;
        container.querySelectorAll('.item-row').forEach((row, index) => {
            const select = row.querySelector('.stock-select');
            const qty = row.querySelector('.qty-input');
            
            if (!select.value) {
                valid = false;
                select.style.borderColor = '#EF4444';
            } else {
                select.style.borderColor = '#E5E7EB';
            }
            
            if (!qty.value || parseFloat(qty.value) <= 0) {
                valid = false;
                qty.style.borderColor = '#EF4444';
            } else {
                qty.style.borderColor = '#E5E7EB';
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Pastikan semua item sudah dipilih dan qty sudah diisi!');
            return false;
        }
    });
    
    // ====== INITIALIZE ======
    // Load existing items
    if (existingItems.length > 0) {
        existingItems.forEach(item => addItemRow(item));
    }
    
    updateEmptyState();
    calculateTotalHpp();
});
</script>
@endsection
