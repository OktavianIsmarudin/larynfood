<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupplierController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier,NULL,id,user_id,' . auth()->id(),
            'kontak' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Supplier::create([
            'user_id' => auth()->id(),
            ...$validated
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier,' . $supplier->id . ',id,user_id,' . auth()->id(),
            'kontak' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus');
    }
}

