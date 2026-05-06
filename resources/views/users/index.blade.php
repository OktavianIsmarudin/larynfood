@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-users" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> Manajemen User
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola akun admin dan super admin</small>
                </div>
                <a href="{{ route('users.create') }}" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#D97706'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)';" onmouseout="this.style.backgroundColor='#F59E0B'; this.style.boxShadow='0 2px 8px rgba(245, 158, 11, 0.2)';">
                    <i class="fas fa-user-plus me-2"></i> Tambah User Admin
                </a>
            </div>

            {{-- ALERTS --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #D1FAE5; background-color: #F0FDF4;">
                    <i class="fas fa-check-circle" style="color: #22C55E; margin-right: 8px;"></i> <strong>Sukses!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #FECACA; background-color: #FEF2F2;">
                    <i class="fas fa-exclamation-circle" style="color: #EF4444; margin-right: 8px;"></i> <strong>Error!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- CARD TABEL --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; width: 60px;">No</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Email</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Role</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Terdaftar</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $index => $user)
                                    <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                        <td style="padding: 16px 20px; color: #6B7280; font-weight: 500;">{{ $loop->iteration }}</td>
                                        <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-info ms-2" style="font-size: 10px;">Anda</span>
                                            @endif
                                        </td>
                                        <td style="padding: 16px 20px; color: #6B7280;">{{ $user->email }}</td>
                                        <td style="padding: 16px 20px;">
                                            @if($user->role === 'super_admin')
                                                <span class="badge" style="background-color: #8B5CF6; font-size: 11px; padding: 5px 12px; border-radius: 6px;">
                                                    <i class="fas fa-crown me-1"></i> Super Admin
                                                </span>
                                            @elseif($user->role === 'admin')
                                                <span class="badge" style="background-color: #3B82F6; font-size: 11px; padding: 5px 12px; border-radius: 6px;">
                                                    <i class="fas fa-user-tie me-1"></i> Admin
                                                </span>
                                            @else
                                                <span class="badge" style="background-color: #10B981; font-size: 11px; padding: 5px 12px; border-radius: 6px;">
                                                    <i class="fas fa-user me-1"></i> Customer
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 16px 20px; color: #6B7280;">{{ $user->created_at->format('d M Y') }}</td>
                                        <td style="padding: 16px 20px; text-align: center;">
                                            @if($user->role !== 'super_admin')
                                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning" style="padding: 6px 12px; font-size: 12px; border-radius: 6px; margin-right: 4px;">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @else
                                                <span class="badge bg-secondary" style="padding: 6px 12px; font-size: 12px;">
                                                    <i class="fas fa-lock"></i> Protected
                                                </span>
                                            @endif

                                            @if($user->role !== 'super_admin' && $user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" style="padding: 6px 12px; font-size: 12px; border-radius: 6px;">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; color: #9CA3AF;">
                            <i class="fas fa-users" style="font-size: 64px; opacity: 0.3; margin-bottom: 16px;"></i>
                            <p style="font-size: 16px; font-weight: 500; color: #6B7280; margin: 0;">Belum ada user terdaftar</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
