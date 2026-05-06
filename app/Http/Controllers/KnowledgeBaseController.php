<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKnowledgeBaseEntryRequest;
use App\Http\Requests\UpdateKnowledgeBaseEntryRequest;
use App\Models\KnowledgeBaseEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    public function index(): View
    {
        $entries = KnowledgeBaseEntry::query()
            ->orderBy('topik')
            ->orderByDesc('is_active')
            ->orderBy('urutan')
            ->latest()
            ->paginate(10);

        return view('knowledge-base.index', compact('entries'));
    }

    public function create(): View
    {
        return view('knowledge-base.create');
    }

    public function store(StoreKnowledgeBaseEntryRequest $request): RedirectResponse
    {
        KnowledgeBaseEntry::create($this->payload($request->validated()));

        return redirect()->route('knowledge-base.index')->with('success', 'Knowledge base berhasil ditambahkan.');
    }

    public function edit(KnowledgeBaseEntry $knowledge_base): View
    {
        return view('knowledge-base.edit', [
            'entry' => $knowledge_base,
        ]);
    }

    public function update(UpdateKnowledgeBaseEntryRequest $request, KnowledgeBaseEntry $knowledge_base): RedirectResponse
    {
        $knowledge_base->update($this->payload($request->validated()));

        return redirect()->route('knowledge-base.index')->with('success', 'Knowledge base berhasil diperbarui.');
    }

    public function destroy(KnowledgeBaseEntry $knowledge_base): RedirectResponse
    {
        $knowledge_base->delete();

        return redirect()->route('knowledge-base.index')->with('success', 'Knowledge base berhasil dihapus.');
    }

    private function payload(array $validated): array
    {
        return [
            'judul' => $validated['judul'],
            'topik' => $validated['topik'],
            'pertanyaan' => $validated['pertanyaan'],
            'jawaban' => $validated['jawaban'],
            'kata_kunci' => $validated['kata_kunci'] ?? null,
            'instruksi_ai' => $validated['instruksi_ai'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'urutan' => (int) ($validated['urutan'] ?? 0),
        ];
    }
}
