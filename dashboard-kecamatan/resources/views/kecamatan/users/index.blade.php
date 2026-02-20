@extends('layouts.kecamatan')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Manajemen Pengguna</h4>
                <p class="text-muted small">Kelola hak akses operator kecamatan dan desa</p>
            </div>
            <a href="{{ route('kecamatan.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
                style="background-color: #4f46e5; border-color: #4f46e5; color: white;">
                <i class="fas fa-user-plus"></i>
                <span>Tambah User Baru</span>
            </a>
        </div>

        <!-- Main Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <form action="{{ route('kecamatan.users.index') }}" method="GET" class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-light bg-light"
                                placeholder="Cari Nama atau Username..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role_id" class="form-select border-light bg-light" onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama_role }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <button type="submit" class="btn btn-teal px-4">Cari User</button>
                        @if(request()->anyFilled(['search', 'role_id']))
                            <a href="{{ route('kecamatan.users.index') }}" class="btn btn-light"><i class="fas fa-undo"></i></a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Informasi User</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Role</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Unit Kerja (Desa)</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">Status</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">Login Terakhir</th>
                            <th class="pe-4 py-3 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="avatar-sm bg-soft-primary text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->nama_lengkap }}</div>
                                            <small class="text-muted">@ {{ $user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-info rounded-pill px-3">
                                        {{ $user->role->nama_role }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->desa)
                                        <div class="fw-medium text-dark">{{ $user->desa->nama_desa }}</div>
                                        <small class="text-muted">Kec. {{ $user->desa->kecamatan }}</small>
                                    @else
                                        <span class="text-muted italic">Internal Kecamatan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($user->status == 'aktif')
                                        <span class="badge bg-soft-success text-success rounded-pill px-3">Aktif</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $user->last_login ? $user->last_login->diffForHumans() : 'Belum pernah' }}
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('kecamatan.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" title="Ubah Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('kecamatan.users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan akses user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-3 shadow-sm"
                                                title="Nonaktifkan">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">Tidak ditemukan data pengguna.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .bg-soft-primary {
            background: rgba(30, 66, 159, 0.1);
        }

        .bg-soft-success {
            background: rgba(16, 185, 129, 0.1);
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.1);
        }

        .bg-soft-info {
            background: rgba(14, 165, 233, 0.1);
        }

        .text-info {
            color: #0ea5e9;
        }

        .btn-teal {
            background: #14b8a6;
            color: white;
            border: none;
        }

        .btn-teal:hover {
            background: #0d9488;
            color: white;
        }

        .avatar-sm {
            width: 38px;
            height: 38px;
        }
    </style>
@endsection