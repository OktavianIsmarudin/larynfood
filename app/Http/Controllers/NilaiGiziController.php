<?php

namespace App\Http\Controllers;

use App\Models\StockGudang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NilaiGiziController extends Controller
{
    /**
     * Display list of stock gudang items with nutrition info.
     */
    public function index(): View
    {
        $stocks = StockGudang::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('nama_produk')
            ->get();

        return view('nilai-gizi.index', compact('stocks'));
    }

    /**
     * Show detail nutrition for a single item.
     */
    public function show(StockGudang $stockGudang): View
    {
        // Ensure user can only view their own data
        if ($stockGudang->user_id !== auth()->id()) {
            abort(403);
        }

        return view('nilai-gizi.show', compact('stockGudang'));
    }

    /**
     * Show edit form for nutrition values.
     */
    public function edit(StockGudang $stockGudang): View
    {
        // Ensure user can only edit their own data
        if ($stockGudang->user_id !== auth()->id()) {
            abort(403);
        }

        return view('nilai-gizi.edit', compact('stockGudang'));
    }

    /**
     * Update nutrition values ONLY — no stock/HPP changes.
     */
    public function update(Request $request, StockGudang $stockGudang): RedirectResponse
    {
        // Ensure user can only update their own data
        if ($stockGudang->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'energi_kkal' => 'nullable|numeric|min:0|max:99999999',
            'protein_g' => 'nullable|numeric|min:0|max:99999999',
            'lemak_g' => 'nullable|numeric|min:0|max:99999999',
            'karbohidrat_g' => 'nullable|numeric|min:0|max:99999999',
        ]);

        // ONLY update nutrition columns — nothing else
        $stockGudang->update([
            'energi_kkal' => $validated['energi_kkal'],
            'protein_g' => $validated['protein_g'],
            'lemak_g' => $validated['lemak_g'],
            'karbohidrat_g' => $validated['karbohidrat_g'],
        ]);

        return redirect()->route('nilai-gizi.index')
            ->with('success', 'Nilai gizi "' . $stockGudang->nama_produk . '" berhasil diperbarui.');
    }
}
