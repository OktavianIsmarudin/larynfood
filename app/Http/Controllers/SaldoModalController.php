<?php

namespace App\Http\Controllers;

use App\Models\SaldoModal;
use App\Models\PenggunaanModal;
use App\Models\PiutangManual;
use App\Models\Pembelian;
use App\Services\SaldoModalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaldoModalController extends Controller
{
    protected SaldoModalService $service;

    public function __construct(SaldoModalService $service)
    {
        $this->service = $service;
    }

    /**
     * Dashboard saldo modal - overview + daftar saldo + penggunaan
     */
    public function index()
    {
        $overview = $this->service->getOverview();

        $saldoModals = SaldoModal::forUser()
            ->with('penggunaanModal')
            ->orderBy('tanggal', 'desc')
            ->paginate(10, ['*'], 'saldo_page');

        $penggunaans = PenggunaanModal::forUser()
            ->with(['saldoModal', 'pembelian', 'penjualan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'penggunaan_page');

        return view('saldo-modal.index', compact('overview', 'saldoModals', 'penggunaans'));
    }

    /**
     * Form tambah saldo modal baru
     */
    public function create()
    {
        // Get active hutang records for linking
        $hutangAktif = PiutangManual::forUser()
            ->hutang()
            ->belumLunas()
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('saldo-modal.create', compact('hutangAktif'));
    }

    /**
     * Simpan saldo modal baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'saldo_awal' => 'required|numeric|min:0.01',
            'sumber_modal' => 'nullable|string|max:255',
            'piutang_manual_id' => 'nullable|exists:piutang_manual,id',
            'nama_pihak_pinjaman' => 'nullable|string|max:255',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $this->service->createSaldoModal($validated);

        return redirect()->route('saldo-modal.index')
            ->with('success', 'Saldo modal berhasil ditambahkan!');
    }

    /**
     * Form catat penggunaan modal
     */
    public function createPenggunaan()
    {
        $saldoModals = SaldoModal::forUser()
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil pembelian yang belum ditautkan ke penggunaan modal
        $pembelians = Pembelian::forUser()
            ->orderBy('tanggal_pembelian', 'desc')
            ->limit(50)
            ->get();

        return view('saldo-modal.penggunaan', compact('saldoModals', 'pembelians'));
    }

    /**
     * Simpan penggunaan modal
     */
    public function storePenggunaan(Request $request)
    {
        $validated = $request->validate([
            'saldo_modal_id' => 'required|exists:saldo_modal,id',
            'pembelian_id' => 'nullable|exists:pembelians,id',
            'nominal' => 'required|numeric|min:0.01',
            'jenis' => 'required|in:pengeluaran,pemasukan_kembali',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        // Verify ownership of saldo modal
        $saldoModal = SaldoModal::forUser()->findOrFail($validated['saldo_modal_id']);

        // Check negative warning (only for pengeluaran)
        if ($validated['jenis'] === 'pengeluaran') {
            $wouldBeNegative = $this->service->wouldCauseNegative(
                $saldoModal->id,
                (float) $validated['nominal']
            );

            // Allow but warn — don't block
            if ($wouldBeNegative && !$request->has('confirm_negative')) {
                return redirect()->back()
                    ->withInput()
                    ->with('warning_negative', true)
                    ->with('warning_saldo_akhir', $saldoModal->saldo_akhir)
                    ->with('warning_nominal', $validated['nominal']);
            }
        }

        $this->service->createPenggunaan($validated);

        return redirect()->route('saldo-modal.index')
            ->with('success', 'Penggunaan modal berhasil dicatat!');
    }

    /**
     * Hapus penggunaan modal
     */
    public function destroyPenggunaan(PenggunaanModal $penggunaanModal)
    {
        // Verify ownership
        if ($penggunaanModal->user_id !== auth()->id()) {
            abort(403);
        }

        $penggunaanModal->delete();

        return redirect()->route('saldo-modal.index')
            ->with('success', 'Penggunaan modal berhasil dihapus!');
    }

    /**
     * Hapus saldo modal (cascade delete penggunaan)
     */
    public function destroy(SaldoModal $saldoModal)
    {
        if ($saldoModal->user_id !== auth()->id()) {
            abort(403);
        }

        $saldoModal->delete();

        return redirect()->route('saldo-modal.index')
            ->with('success', 'Saldo modal berhasil dihapus!');
    }

    /**
     * API: Get remaining saldo for a saldo modal (AJAX)
     */
    public function getRemainingSaldo(SaldoModal $saldoModal)
    {
        if ($saldoModal->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'saldo_awal' => (float) $saldoModal->saldo_awal,
            'saldo_akhir' => $saldoModal->saldo_akhir,
            'total_penggunaan' => $saldoModal->total_penggunaan,
        ]);
    }
}
