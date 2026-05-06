@extends('layouts.app')

@section('title', 'Edit Knowledge Base')
@section('page-title', 'Edit Knowledge Base')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background-color: #FFFFFF;">
        <div class="card-body p-4">
            <form action="{{ route('knowledge-base.update', $entry) }}" method="POST">
                @csrf
                @method('PUT')
                @include('knowledge-base._form', ['entry' => $entry])
                <button type="submit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                <a href="{{ route('knowledge-base.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
