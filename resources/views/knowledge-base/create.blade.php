@extends('layouts.app')

@section('title', 'Tambah Knowledge Base')
@section('page-title', 'Tambah Knowledge Base')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background-color: #FFFFFF;">
        <div class="card-body p-4">
            <form action="{{ route('knowledge-base.store') }}" method="POST">
                @csrf
                @include('knowledge-base._form')
                <button type="submit" class="btn btn-warning fw-bold">Simpan</button>
                <a href="{{ route('knowledge-base.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
