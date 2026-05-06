@extends('layouts.app')

@section('title', 'Knowledge Base')
@section('page-title', 'Knowledge Base')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                <i class="fas fa-robot" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> Knowledge Base
            </h2>
            <small style="color: #6B7280; font-size: 14px;">Kelola pertanyaan, jawaban, keyword, dan instruksi AI chatbot</small>
        </div>
        <a href="{{ route('knowledge-base.create') }}" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none;">
            <i class="fas fa-plus me-2"></i> Tambah Entri
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background-color: #FFFFFF;">
        <div class="card-body p-0">
            @if($entries->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 14px;">
                        <thead>
                            <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                <th style="padding: 16px 20px;">Judul</th>
                                <th style="padding: 16px 20px;">Topik</th>
                                <th style="padding: 16px 20px;">Pertanyaan</th>
                                <th style="padding: 16px 20px;">Status</th>
                                <th style="padding: 16px 20px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td style="padding: 16px 20px; font-weight: 600;">{{ $entry->judul }}</td>
                                    <td style="padding: 16px 20px;">{{ $entry->topik }}</td>
                                    <td style="padding: 16px 20px; color: #6B7280;">{{ \Illuminate\Support\Str::limit($entry->pertanyaan, 90) }}</td>
                                    <td style="padding: 16px 20px;">
                                        @if($entry->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 20px; text-align: center;">
                                        <a href="{{ route('knowledge-base.edit', $entry) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('knowledge-base.destroy', $entry) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Hapus entry ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $entries->links() }}</div>
            @else
                <div style="text-align: center; padding: 60px 20px; color: #9CA3AF;">
                    <i class="fas fa-robot" style="font-size: 64px; opacity: 0.3; margin-bottom: 16px;"></i>
                    <p style="font-size: 16px; font-weight: 500; color: #6B7280; margin: 0;">Belum ada knowledge base</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
