@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid py-4" style="background-color: #F5F7FA; min-height: 100vh;">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%); color: white; border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Detail Penjualan</h5>
                    @if ($penjualan->status_pembayaran == 'lunas')
                        <span class="badge" style="background-color: #DCFCE7; color: #166534; padding: 8px 16px; font-size: 13px;">Lunas</span>
                    @elseif ($penjualan->status_pembayaran == 'dp')
                        <span class="badge" style="background-color: #FEF3C7; color: #92400E; padding: 8px 16px; font-size: 13px;">DP</span>
                    @else
                        <span class="badge" style="background-color: #FEE2E2; color: #B91C1C; padding: 8px 16px; font-size: 13px;">Utang</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    @php
                        $autoOrderNumber = null;
                        $cleanKeterangan = $penjualan->keterangan;
                        if (!empty($penjualan->keterangan) && preg_match('/\[AUTO-ORDER:([^\]]+)\]/', $penjualan->keterangan, $matches)) {
                            $autoOrderNumber = $matches[1];

                            // Remove internal sync markers from UI output.
                            $cleanKeterangan = preg_replace('/\[AUTO-ORDER:[^\]]+\]\[ITEM:[^\]]+\]\s*/', '', $cleanKeterangan);
                            $cleanKeterangan = preg_replace('/^Auto\s+sinkron\s+dari\s+tracking\s+order\s+[^\.]+\.\s*/i', '', $cleanKeterangan);
                            $cleanKeterangan = trim((string) $cleanKeterangan);
                        }
                    @endphp

                    @if($autoOrderNumber)
                        <div class="alert mb-4" style="background-color: #F0F9FF; border: 1px solid #BAE6FD; color: #0C4A6E; border-radius: 10px;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge" style="background-color: #E0F2FE; color: #075985; font-size: 12px; padding: 6px 10px; border-radius: 999px; font-weight: 700;">Auto Order</span>
                                <span style="font-weight: 600;">Sumber dari tracking pesanan selesai:</span>
                                <span style="font-weight: 700;">{{ $autoOrderNumber }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Informasi Dasar --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-3">
                                <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Tanggal Penjualan</strong><br>
                                <span style="color: #1F2937; font-size: 15px; font-weight: 500;">{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d F Y') }}</span>
                            </p>
                            <p class="mb-3">
                                <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Customer</strong><br>
                                <span style="color: #1F2937; font-size: 15px; font-weight: 500;">{{ $penjualan->nama_customer_snapshot ?? $penjualan->customer->nama_customer ?? '-' }}</span>
                            </p>
                            @if ($penjualan->customer && $penjualan->customer->telepon)
                                <p class="mb-3">
                                    <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Kontak</strong><br>
                                    <span style="color: #1F2937; font-size: 15px;">{{ $penjualan->customer->telepon }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-3">
                                <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Produk</strong><br>
                                <span style="color: #1F2937; font-size: 15px; font-weight: 500;">
                                    @if($penjualan->produk && $penjualan->produk->isPaket())
                                        <span style="background-color: #E8E8FF; color: #5B21B6; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; margin-right: 4px;">PAKET</span>
                                    @endif
                                    {{ $penjualan->nama_produk_display }}
                                </span>
                            </p>
                            <p class="mb-3">
                                <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Metode Pembayaran</strong><br>
                                <span style="color: #1F2937; font-size: 15px;">{{ $penjualan->metodePembayaran->nama_metode ?? 'Tunai' }}</span>
                            </p>
                        </div>
                    </div>

                    <hr style="border-color: #E5E7EB;">

                    {{-- Detail Penjualan --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0" style="background-color: #F9FAFB; border-radius: 8px;">
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Jumlah Penjualan</strong><br>
                                        <span style="color: #1F2937; font-size: 24px; font-weight: 700;">{{ $penjualan->jumlah_pcs }} PCS</span>
                                    </p>
                                    <p class="mb-0">
                                        <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Harga Satuan</strong><br>
                                        <span style="color: #06B6D4; font-size: 18px; font-weight: 600;">Rp {{ number_format($penjualan->harga_satuan, 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-0" style="background-color: #F9FAFB; border-radius: 8px;">
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Diskon</strong><br>
                                        @if ($penjualan->diskon > 0)
                                            <span style="color: #10B981; font-size: 18px; font-weight: 600;">{{ $penjualan->label_diskon }}</span>
                                            <small style="color: #6B7280; display: block; margin-top: 4px;">
                                                ({{ $penjualan->tipe_diskon == 'persentase' ? 'Persentase' : 'Nominal' }})
                                            </small>
                                        @else
                                            <span style="color: #9CA3AF; font-size: 16px;">Tidak ada diskon</span>
                                        @endif
                                    </p>
                                    <p class="mb-0">
                                        <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Nilai Potongan</strong><br>
                                        <span style="color: #10B981; font-size: 18px; font-weight: 600;">
                                            @if ($penjualan->nilai_diskon > 0)
                                                - Rp {{ number_format($penjualan->nilai_diskon, 0, ',', '.') }}
                                            @else
                                                Rp 0
                                            @endif
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Rincian Pembayaran --}}
                    <div class="card border-0 mb-4" style="background-color: #FFFFFF; border: 2px solid #06B6D4; border-radius: 12px;">
                        <div class="card-header" style="background-color: #F0FDFA; border-bottom: 1px solid #E5E7EB; border-radius: 10px 10px 0 0;">
                            <h6 class="mb-0" style="color: #0891B2; font-weight: 600;">
                                <i class="fas fa-calculator"></i> Rincian Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">
                            <table style="width: 100%; font-size: 14px;">
                                <tr>
                                    <td style="padding: 8px 0; color: #6B7280;">Subtotal ({{ $penjualan->jumlah_pcs }} × Rp {{ number_format($penjualan->harga_satuan, 0, ',', '.') }})</td>
                                    <td style="padding: 8px 0; text-align: right; color: #1F2937; font-weight: 500;">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6B7280;">
                                        Diskon
                                        @if ($penjualan->diskon > 0)
                                            ({{ $penjualan->label_diskon }})
                                        @endif
                                    </td>
                                    <td style="padding: 8px 0; text-align: right; color: #10B981; font-weight: 500;">
                                        @if ($penjualan->nilai_diskon > 0)
                                            - Rp {{ number_format($penjualan->nilai_diskon, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6B7280;">Ongkir (Biaya Pengiriman)</td>
                                    <td style="padding: 8px 0; text-align: right; color: #F59E0B; font-weight: 500;">
                                        @if ($penjualan->ongkir > 0)
                                            + Rp {{ number_format($penjualan->ongkir, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>
                                </tr>
                                <tr style="border-top: 2px solid #06B6D4;">
                                    <td style="padding: 12px 0; color: #1F2937; font-weight: 700; font-size: 16px;">TOTAL BAYAR</td>
                                    <td style="padding: 12px 0; text-align: right; color: #06B6D4; font-weight: 700; font-size: 20px;">
                                        Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Informasi Laba (jika ada) --}}
                    @if ($penjualan->laba > 0)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0" style="background-color: #F0FDF4; border-radius: 8px;">
                                <div class="card-body">
                                    <p class="mb-0">
                                        <strong style="color: #166534; font-size: 12px; text-transform: uppercase;">Estimasi Laba</strong><br>
                                        <span style="color: #16A34A; font-size: 20px; font-weight: 700;">Rp {{ number_format($penjualan->laba, 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0" style="background-color: #FEF2F2; border-radius: 8px;">
                                <div class="card-body">
                                    <p class="mb-0">
                                        <strong style="color: #991B1B; font-size: 12px; text-transform: uppercase;">HPP Total</strong><br>
                                        <span style="color: #DC2626; font-size: 20px; font-weight: 700;">Rp {{ number_format($penjualan->hpp_total, 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Keterangan --}}
                    @if (!empty($cleanKeterangan))
                    <div class="mb-4">
                        <strong style="color: #6B7280; font-size: 12px; text-transform: uppercase;">Keterangan / Catatan</strong>
                        <div class="alert mb-0 mt-2" style="background-color: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; border-radius: 8px;">
                            {{ $cleanKeterangan }}
                        </div>
                    </div>
                    @endif

                    {{-- Tombol Aksi --}}
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('penjualan.download-resi', $penjualan) }}" class="btn" style="background-color: #06B6D4; color: white; border-radius: 8px; padding: 10px 16px;" target="_blank">
                            <i class="fas fa-file-pdf"></i> Download Resi PDF
                        </a>
                        <a href="{{ route('penjualan.print-resi', $penjualan) }}" class="btn" style="background-color: #6B7280; color: white; border-radius: 8px; padding: 10px 16px;" target="_blank">
                            <i class="fas fa-print"></i> Print Resi
                        </a>
                        <a href="{{ route('penjualan.edit', $penjualan) }}" class="btn" style="background-color: #F59E0B; color: white; border-radius: 8px; padding: 10px 16px;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('penjualan.destroy', $penjualan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background-color: #DC2626; color: white; border-radius: 8px; padding: 10px 16px;" onclick="return confirm('Yakin hapus penjualan ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                        <a href="{{ route('penjualan.index') }}" class="btn" style="background-color: #E5E7EB; color: #374151; border-radius: 8px; padding: 10px 16px;">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
