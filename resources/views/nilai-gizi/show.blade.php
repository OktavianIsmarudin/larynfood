@extends('layouts.app')

@section('title', 'Detail Nilai Gizi - ' . $stockGudang->nama_produk)
@section('page-title', 'Detail Nilai Gizi')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 28px; margin: 0;">
                        <i class="fas fa-seedling" style="color: #10b981; margin-right: 12px; opacity: 0.8;"></i> Detail Nilai Gizi
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Informasi nutrisi per 1 PCS (PRO Version)</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('nilai-gizi.edit', $stockGudang->id) }}" class="btn fw-bold" style="background-color: #f59e0b; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none;">
                        <i class="fas fa-edit me-2"></i> Edit Nilai Gizi
                    </a>
                    <a href="{{ route('nilai-gizi.index') }}" class="btn fw-bold" style="background-color: #6B7280; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none;">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- PRODUCT INFO --}}
            <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                <div class="card-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 12px 12px 0 0; padding: 20px; border: none;">
                    <h5 class="mb-0" style="color: white; font-weight: 700; font-size: 18px;">
                        <i class="fas fa-box me-2"></i> {{ $stockGudang->nama_produk }}
                    </h5>
                    <small style="color: rgba(255,255,255,0.8);">SKU: {{ $stockGudang->sku ?? '-' }} | Kategori: {{ $stockGudang->category->nama_kategori ?? '-' }}</small>
                </div>
                <div class="card-body" style="padding: 28px;">
                    {{-- Nutrition Cards Grid (Per 1 PCS) --}}
                    <div class="row g-3 mb-3">
                        {{-- Energi --}}
                        <div class="col-md-6 col-lg-3">
                            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d; border-radius: 14px; padding: 24px 20px; text-align: center;">
                                <div style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                    <i class="fas fa-fire" style="color: #d97706; font-size: 20px;"></i>
                                </div>
                                <div style="font-size: 28px; font-weight: 800; color: #92400e;">
                                    {{ !is_null($stockGudang->energi_kkal) ? number_format($stockGudang->energi_kkal, 2) : '—' }}
                                </div>
                                <div style="font-size: 12px; font-weight: 600; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Energi (kkal)
                                </div>
                            </div>
                        </div>

                        {{-- Protein --}}
                        <div class="col-md-6 col-lg-3">
                            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 1px solid #93c5fd; border-radius: 14px; padding: 24px 20px; text-align: center;">
                                <div style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                    <i class="fas fa-dna" style="color: #2563eb; font-size: 20px;"></i>
                                </div>
                                <div style="font-size: 28px; font-weight: 800; color: #1e40af;">
                                    {{ !is_null($stockGudang->protein_g) ? number_format($stockGudang->protein_g, 2) : '—' }}
                                </div>
                                <div style="font-size: 12px; font-weight: 600; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Protein (g)
                                </div>
                            </div>
                        </div>

                        {{-- Lemak --}}
                        <div class="col-md-6 col-lg-3">
                            <div style="background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); border: 1px solid #f9a8d4; border-radius: 14px; padding: 24px 20px; text-align: center;">
                                <div style="width: 48px; height: 48px; background: rgba(236, 72, 153, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                    <i class="fas fa-droplet" style="color: #db2777; font-size: 20px;"></i>
                                </div>
                                <div style="font-size: 28px; font-weight: 800; color: #9d174d;">
                                    {{ !is_null($stockGudang->lemak_g) ? number_format($stockGudang->lemak_g, 2) : '—' }}
                                </div>
                                <div style="font-size: 12px; font-weight: 600; color: #be185d; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Lemak (g)
                                </div>
                            </div>
                        </div>

                        {{-- Karbohidrat --}}
                        <div class="col-md-6 col-lg-3">
                            <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 1px solid #6ee7b7; border-radius: 14px; padding: 24px 20px; text-align: center;">
                                <div style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                    <i class="fas fa-wheat-awn" style="color: #059669; font-size: 20px;"></i>
                                </div>
                                <div style="font-size: 28px; font-weight: 800; color: #065f46;">
                                    {{ !is_null($stockGudang->karbohidrat_g) ? number_format($stockGudang->karbohidrat_g, 2) : '—' }}
                                </div>
                                <div style="font-size: 12px; font-weight: 600; color: #047857; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                                    Karbohidrat (g)
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Per PCS Note --}}
                    <div class="text-center" style="color: #9CA3AF; font-size: 13px;">
                        <i class="fas fa-info-circle me-1"></i> Semua nilai gizi di atas adalah per <strong>1 PCS</strong>
                    </div>
                </div>
            </div>

            @php
                $hasAnyNutrition = !is_null($stockGudang->energi_kkal) || !is_null($stockGudang->protein_g) || !is_null($stockGudang->lemak_g) || !is_null($stockGudang->karbohidrat_g);
            @endphp

            @if($hasAnyNutrition)
                {{-- FEATURE 3: SMART NUTRITION BADGES --}}
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                    <div class="card-body" style="padding: 20px;">
                        <h6 style="font-weight: 700; color: #1A1A1A; margin-bottom: 16px; font-size: 15px;">
                            <i class="fas fa-star" style="color: #f59e0b; margin-right: 8px;"></i> Smart Nutrition Badge
                        </h6>
                        <div class="d-flex flex-wrap gap-2" id="nutritionBadges">
                            <!-- Badges will be added by JavaScript -->
                        </div>
                    </div>
                </div>

                {{-- FEATURE 2: MACRONUTRIENT DISTRIBUTION --}}
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                    <div class="card-body" style="padding: 24px;">
                        <h6 style="font-weight: 700; color: #1A1A1A; margin-bottom: 20px; font-size: 15px;">
                            <i class="fas fa-chart-pie" style="color: #8b5cf6; margin-right: 8px;"></i> Distribusi Makronutrien
                        </h6>
                        <div class="row g-4">
                            {{-- Protein Distribution --}}
                            <div class="col-md-4">
                                <div style="background: #eff6ff; border-radius: 10px; padding: 16px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #1e40af; font-size: 13px;">Protein</span>
                                        <span style="font-weight: 700; color: #2563eb;" id="proteinPercentage">0%</span>
                                    </div>
                                    <div style="background: #dbeafe; border-radius: 8px; height: 12px; overflow: hidden;">
                                        <div id="proteinBar" style="background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                                    </div>
                                    <small style="color: #6b7280; display: block; margin-top: 8px;" id="proteinKcal">0 kkal</small>
                                </div>
                            </div>

                            {{-- Carbs Distribution --}}
                            <div class="col-md-4">
                                <div style="background: #ecfdf5; border-radius: 10px; padding: 16px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #065f46; font-size: 13px;">Karbohidrat</span>
                                        <span style="font-weight: 700; color: #059669;" id="carbsPercentage">0%</span>
                                    </div>
                                    <div style="background: #d1fae5; border-radius: 8px; height: 12px; overflow: hidden;">
                                        <div id="carbsBar" style="background: linear-gradient(90deg, #10b981 0%, #059669 100%); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                                    </div>
                                    <small style="color: #6b7280; display: block; margin-top: 8px;" id="carbsKcal">0 kkal</small>
                                </div>
                            </div>

                            {{-- Fat Distribution --}}
                            <div class="col-md-4">
                                <div style="background: #fdf2f8; border-radius: 10px; padding: 16px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #9d174d; font-size: 13px;">Lemak</span>
                                        <span style="font-weight: 700; color: #db2777;" id="fatPercentage">0%</span>
                                    </div>
                                    <div style="background: #fce7f3; border-radius: 8px; height: 12px; overflow: hidden;">
                                        <div id="fatBar" style="background: linear-gradient(90deg, #ec4899 0%, #db2777 100%); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                                    </div>
                                    <small style="color: #6b7280; display: block; margin-top: 8px;" id="fatKcal">0 kkal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FEATURE 1: CONSUMPTION CALCULATOR (KALKULATOR KONSUMSI) --}}
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                    <div class="card-body" style="padding: 24px;">
                        <h6 style="font-weight: 700; color: #1A1A1A; margin-bottom: 20px; font-size: 15px;">
                            <i class="fas fa-calculator" style="color: #8b5cf6; margin-right: 8px;"></i> 🧮 Simulasi Konsumsi
                        </h6>
                        
                        {{-- Input Section --}}
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block; font-size: 14px;">
                                    Jumlah PCS
                                </label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <button type="button" style="padding: 8px 16px; background: #e5e7eb; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: #374151;" onclick="decrementPCS()">−</button>
                                    <input type="number" id="quantityInput" value="1" min="1" max="999" style="width: 80px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; text-align: center; font-weight: 600; font-size: 16px;" onchange="updateCalculations()" oninput="updateCalculations()">
                                    <button type="button" style="padding: 8px 16px; background: #e5e7eb; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: #374151;" onclick="incrementPCS()">+</button>
                                    <span style="color: #6b7280; font-size: 13px; margin-left: 8px;">PCS</span>
                                </div>
                            </div>
                        </div>

                        {{-- Calculator Results Grid --}}
                        <div class="row g-3">
                            {{-- Total Energi --}}
                            <div class="col-md-6 col-lg-3">
                                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d; border-radius: 12px; padding: 20px; text-align: center;">
                                    <div style="font-size: 14px; color: #b45309; margin-bottom: 8px;">Total Energi</div>
                                    <div style="font-size: 24px; font-weight: 800; color: #92400e;" id="totalEnergy">0</div>
                                    <div style="font-size: 11px; color: #b45309; margin-top: 4px;">kkal</div>
                                </div>
                            </div>

                            {{-- Total Protein --}}
                                {{-- Energi --}}
                                <div class="col-md-6">
                                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px;">
                                        <div style="width: 40px; height: 40px; background: #fde68a; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px;">
                                            🔥
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #92400e; font-size: 13px; margin-bottom: 4px;">
                                                Energi — {{ !is_null($stockGudang->energi_kkal) ? number_format($stockGudang->energi_kkal, 2) . ' kkal' : '' }}
                                            </div>
                                            @if(!is_null($stockGudang->energi_kkal) && $stockGudang->energi_kkal > 0)
                                                <p style="margin: 0; font-size: 12.5px; color: #78716c; line-height: 1.5;">
                                                    Memberikan asupan energi untuk aktivitas harian.
                                                </p>
                                            @else
                                                <p style="margin: 0; font-size: 12.5px; color: #d1d5db; font-style: italic;">Data gizi belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Protein --}}
                                <div class="col-md-6">
                                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px;">
                                        <div style="width: 40px; height: 40px; background: #bfdbfe; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px;">
                                            💪
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1e40af; font-size: 13px; margin-bottom: 4px;">
                                                Protein — {{ !is_null($stockGudang->protein_g) ? number_format($stockGudang->protein_g, 2) . ' g' : '' }}
                                            </div>
                                            @if(!is_null($stockGudang->protein_g) && $stockGudang->protein_g > 0)
                                                <p style="margin: 0; font-size: 12.5px; color: #78716c; line-height: 1.5;">
                                                    Membantu pembentukan dan perbaikan jaringan tubuh.
                                                </p>
                                            @else
                                                <p style="margin: 0; font-size: 12.5px; color: #d1d5db; font-style: italic;">Data gizi belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Lemak --}}
                                <div class="col-md-6">
                                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; background: #fdf2f8; border: 1px solid #fce7f3; border-radius: 12px;">
                                        <div style="width: 40px; height: 40px; background: #fbcfe8; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px;">
                                            🧈
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #9d174d; font-size: 13px; margin-bottom: 4px;">
                                                Lemak — {{ !is_null($stockGudang->lemak_g) ? number_format($stockGudang->lemak_g, 2) . ' g' : '' }}
                                            </div>
                                            @if(!is_null($stockGudang->lemak_g) && $stockGudang->lemak_g > 0)
                                                <p style="margin: 0; font-size: 12.5px; color: #78716c; line-height: 1.5;">
                                                    Berperan sebagai sumber energi dan membantu penyerapan vitamin.
                                                </p>
                                            @else
                                                <p style="margin: 0; font-size: 12.5px; color: #d1d5db; font-style: italic;">Data gizi belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Karbohidrat --}}
                                <div class="col-md-6">
                                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 16px; background: #ecfdf5; border: 1px solid #d1fae5; border-radius: 12px;">
                                        <div style="width: 40px; height: 40px; background: #a7f3d0; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px;">
                                            🌾
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #065f46; font-size: 13px; margin-bottom: 4px;">
                                                Karbohidrat — {{ !is_null($stockGudang->karbohidrat_g) ? number_format($stockGudang->karbohidrat_g, 2) . ' g' : '' }}
                                            </div>
                                            @if(!is_null($stockGudang->karbohidrat_g) && $stockGudang->karbohidrat_g > 0)
                                                <p style="margin: 0; font-size: 12.5px; color: #78716c; line-height: 1.5;">
                                                    Merupakan sumber energi utama bagi tubuh.
                                                </p>
                                            @else
                                                <p style="margin: 0; font-size: 12.5px; color: #d1d5db; font-style: italic;">Data gizi belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div style="font-size: 36px; margin-bottom: 8px;">🥗</div>
                                <p style="color: #9ca3af; font-size: 14px; margin: 0;">Data gizi belum tersedia</p>
                                <small style="color: #d1d5db;">Silakan isi nilai gizi melalui tombol Edit</small>
                            </div>
                        @endif

                        {{-- Disclaimer --}}
                        <div class="mt-4" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 16px;">
                            <div class="d-flex align-items-start gap-2">
                                <span style="flex-shrink: 0;">ℹ️</span>
                                <small style="color: #6b7280; line-height: 1.6;">
                                    Informasi gizi dihitung per 1 PCS produk dan bersifat edukatif.
                                    Bukan merupakan saran medis atau klaim kesehatan.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('extra-js')
<script>
    // ===============================================
    // FEATURE 1: CONSUMPTION CALCULATOR FUNCTIONS
    // ===============================================
    
    const perPcsData = {
        energy: parseFloat({{ !is_null($stockGudang->energi_kkal) ? $stockGudang->energi_kkal : 0 }}) || 0,
        protein: parseFloat({{ !is_null($stockGudang->protein_g) ? $stockGudang->protein_g : 0 }}) || 0,
        fat: parseFloat({{ !is_null($stockGudang->lemak_g) ? $stockGudang->lemak_g : 0 }}) || 0,
        carbs: parseFloat({{ !is_null($stockGudang->karbohidrat_g) ? $stockGudang->karbohidrat_g : 0 }}) || 0
    };

    function getQuantity() {
        const input = document.getElementById('quantityInput');
        return Math.max(1, parseInt(input?.value) || 1);
    }

    function incrementPCS() {
        const input = document.getElementById('quantityInput');
        if (!input) return;
        input.value = Math.min(parseInt(input.value) + 1, 999);
        updateCalculations();
    }

    function decrementPCS() {
        const input = document.getElementById('quantityInput');
        if (!input) return;
        input.value = Math.max(parseInt(input.value) - 1, 1);
        updateCalculations();
    }

    function formatNumber(num, decimals = 2) {
        const parsed = parseFloat(num);
        return isNaN(parsed) ? '0' : parsed.toFixed(decimals);
    }

    // ===============================================
    // FEATURE 2 & 3: UPDATE ALL CALCULATIONS
    // ===============================================
    
    function updateCalculations() {
        updateConsumptionCalculator();
        updateMacroDistribution();
        updateNutritionBadges();
    }

    // ===============================================
    // CONSUMPTION CALCULATOR UPDATE
    // ===============================================
    
    function updateConsumptionCalculator() {
        const qtyInput = document.getElementById('quantityInput');
        if (!qtyInput) return;
        
        const qty = Math.max(1, parseInt(qtyInput.value) || 1);

        const totalEnergy = perPcsData.energy * qty;
        const totalProtein = perPcsData.protein * qty;
        const totalFat = perPcsData.fat * qty;
        const totalCarbs = perPcsData.carbs * qty;

        const totalEnergyEl = document.getElementById('totalEnergy');
        const totalProteinEl = document.getElementById('totalProtein');
        const totalFatEl = document.getElementById('totalFat');
        const totalCarbsEl = document.getElementById('totalCarbs');

        if (totalEnergyEl) totalEnergyEl.textContent = formatNumber(totalEnergy, 2);
        if (totalProteinEl) totalProteinEl.textContent = formatNumber(totalProtein, 2);
        if (totalFatEl) totalFatEl.textContent = formatNumber(totalFat, 2);
        if (totalCarbsEl) totalCarbsEl.textContent = formatNumber(totalCarbs, 2);
    }

    // ===============================================
    // MACRONUTRIENT DISTRIBUTION UPDATE
    // ===============================================
    
    function updateMacroDistribution() {
        // Calculate calories from each macro per PCS
        const proteinKcal = Math.max(0, perPcsData.protein * 4);
        const carbsKcal = Math.max(0, perPcsData.carbs * 4);
        const fatKcal = Math.max(0, perPcsData.fat * 9);

        const totalKcal = proteinKcal + carbsKcal + fatKcal;

        // Get elements with safety check
        const proteinPercentEl = document.getElementById('proteinPercentage');
        const carbsPercentEl = document.getElementById('carbsPercentage');
        const fatPercentEl = document.getElementById('fatPercentage');
        const proteinKcalEl = document.getElementById('proteinKcal');
        const carbsKcalEl = document.getElementById('carbsKcal');
        const fatKcalEl = document.getElementById('fatKcal');
        const proteinBarEl = document.getElementById('proteinBar');
        const carbsBarEl = document.getElementById('carbsBar');
        const fatBarEl = document.getElementById('fatBar');

        if (!proteinPercentEl || !carbsPercentEl || !fatPercentEl) {
            return; // Exit if elements not found
        }

        if (totalKcal === 0 || isNaN(totalKcal)) {
            proteinPercentEl.textContent = '0%';
            carbsPercentEl.textContent = '0%';
            fatPercentEl.textContent = '0%';
            if (proteinKcalEl) proteinKcalEl.textContent = '0 kkal';
            if (carbsKcalEl) carbsKcalEl.textContent = '0 kkal';
            if (fatKcalEl) fatKcalEl.textContent = '0 kkal';
            if (proteinBarEl) proteinBarEl.style.width = '0%';
            if (carbsBarEl) carbsBarEl.style.width = '0%';
            if (fatBarEl) fatBarEl.style.width = '0%';
        } else {
            const proteinPct = ((proteinKcal / totalKcal) * 100).toFixed(1);
            const carbsPct = ((carbsKcal / totalKcal) * 100).toFixed(1);
            const fatPct = ((fatKcal / totalKcal) * 100).toFixed(1);

            proteinPercentEl.textContent = proteinPct + '%';
            carbsPercentEl.textContent = carbsPct + '%';
            fatPercentEl.textContent = fatPct + '%';
            if (proteinKcalEl) proteinKcalEl.textContent = formatNumber(proteinKcal, 0) + ' kkal';
            if (carbsKcalEl) carbsKcalEl.textContent = formatNumber(carbsKcal, 0) + ' kkal';
            if (fatKcalEl) fatKcalEl.textContent = formatNumber(fatKcal, 0) + ' kkal';
            if (proteinBarEl) proteinBarEl.style.width = proteinPct + '%';
            if (carbsBarEl) carbsBarEl.style.width = carbsPct + '%';
            if (fatBarEl) fatBarEl.style.width = fatPct + '%';
        }
    }

    // ===============================================
    // SMART NUTRITION BADGES UPDATE
    // ===============================================
    
    function updateNutritionBadges() {
        const badgesContainer = document.getElementById('nutritionBadges');
        if (!badgesContainer) {
            return; // Exit if container not found
        }
        
        badgesContainer.innerHTML = '';

        const energy = perPcsData.energy || 0;
        const protein = perPcsData.protein || 0;

        // Energy Badge
        let energyBadge = '';
        if (energy > 0 && energy < 100) {
            energyBadge = '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 20px; font-size: 13px; font-weight: 600; color: #166534;"><span>🟢</span> Rendah Kalori</span>';
        } else if (energy >= 100 && energy <= 250) {
            energyBadge = '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 20px; font-size: 13px; font-weight: 600; color: #92400e;"><span>🟡</span> Kalori Sedang</span>';
        } else if (energy > 250) {
            energyBadge = '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 20px; font-size: 13px; font-weight: 600; color: #7f1d1d;"><span>🔴</span> Tinggi Kalori</span>';
        }

        if (energyBadge) {
            badgesContainer.innerHTML += energyBadge;
        }

        // Protein Badge
        if (protein >= 10) {
            const proteinBadge = '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #dbeafe; border: 1px solid #bfdbfe; border-radius: 20px; font-size: 13px; font-weight: 600; color: #1e40af;"><span>💪</span> Tinggi Protein</span>';
            badgesContainer.innerHTML += proteinBadge;
        }

        // If no badges, show placeholder
        if (!energyBadge && protein < 10) {
            badgesContainer.innerHTML = '<span style="color: #9ca3af; font-size: 13px; font-style: italic;">Tunggu sampai data gizi lengkap...</span>';
        }
    }

    // ===============================================
    // RESET CALCULATOR
    // ===============================================
    
    function resetCalculator() {
        const input = document.getElementById('quantityInput');
        if (input) {
            input.value = 1;
            updateCalculations();
        }
    }

    // ===============================================
    // COLLAPSE ANIMATION
    // ===============================================
    
    function initCollapse() {
        const collapseEl = document.getElementById('nutrisiSummary');
        const icon = document.getElementById('collapseIcon');
        if (collapseEl && icon) {
            collapseEl.addEventListener('hide.bs.collapse', () => { 
                icon.style.transform = 'rotate(-90deg)'; 
            });
            collapseEl.addEventListener('show.bs.collapse', () => { 
                icon.style.transform = 'rotate(0deg)'; 
            });
        }
    }

    // ===============================================
    // INITIALIZE ON PAGE LOAD
    // ===============================================
    
    function initPage() {
        // Initialize collapse functionality
        initCollapse();
        
        // Initial calculations
        updateCalculations();
        
        console.log('✅ Nutrition page initialized:', {
            energy: perPcsData.energy,
            protein: perPcsData.protein,
            fat: perPcsData.fat,
            carbs: perPcsData.carbs
        });
    }

    // Use both DOMContentLoaded and setTimeout for safety
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPage);
    } else {
        initPage();
    }

    // Fallback: Run after short delay
    setTimeout(() => {
        if (!document.getElementById('totalEnergy')) {
            initPage();
        }
    }, 100);
</script>
@endsection
@endsection
