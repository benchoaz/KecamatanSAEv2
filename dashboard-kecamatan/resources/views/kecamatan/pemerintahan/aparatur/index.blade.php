@extends('layouts.kecamatan')

@section('title', 'Data Aparatur Desa')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Daftar Kepala Desa & Perangkat Desa</h4>
                <p class="text-muted small">Administrasi dan monitoring aparatur desa se-Kecamatan</p>
            </div>
            <a href="{{ route('kecamatan.pemerintahan.aparatur.create') }}"
                class="btn btn-primary d-flex align-items-center gap-2">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Aparatur</span>
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-teal p-3 rounded-3 text-teal-600">
                                <i class="fas fa-user-tie fa-2x"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Kepala Desa Aktif</small>
                                <h4 class="fw-bold mb-0">{{ $stats['total_kades'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-primary p-3 rounded-3 text-primary">
                                <i class="fas fa-users-cog fa-2x"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Perangkat Desa</small>
                                <h4 class="fw-bold mb-0">{{ $stats['total_perangkat'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-warning p-3 rounded-3 text-warning-600">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Belum Diverifikasi</small>
                                <h4 class="fw-bold mb-0">{{ $stats['unverified'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-danger p-3 rounded-3 text-danger">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Perlu Perbaikan</small>
                                <h4 class="fw-bold mb-0">{{ $stats['needs_revision'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <form action="{{ route('kecamatan.pemerintahan.aparatur.index') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <select name="desa_id" class="form-select border-light bg-light" onchange="this.form.submit()">
                            <option value="">Semua Desa</option>
                            @foreach($villages as $desa)
                                <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>
                                    {{ $desa->nama_desa }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="jabatan" class="form-select border-light bg-light" onchange="this.form.submit()">
                            <option value="">Semua Jabatan</option>
                            <option value="Kades" {{ request('jabatan') == 'Kades' ? 'selected' : '' }}>Kepala Desa</option>
                            <option value="Sekdes" {{ request('jabatan') == 'Sekdes' ? 'selected' : '' }}>Sekretaris Desa
                            </option>
                            <option value="Kaur" {{ request('jabatan') == 'Kaur' ? 'selected' : '' }}>Kaur</option>
                            <option value="Kasi" {{ request('jabatan') == 'Kasi' ? 'selected' : '' }}>Kasi</option>
                            <option value="Kadus" {{ request('jabatan') == 'Kadus' ? 'selected' : '' }}>Kepala Dusun</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select border-light bg-light" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Pj" {{ request('status') == 'Pj' ? 'selected' : '' }}>Pj (Pejabat)</option>
                            <option value="Berakhir" {{ request('status') == 'Berakhir' ? 'selected' : '' }}>Masa Jabatan
                                Berakhir</option>
                        </select>
                    </div>
                    <div class="col-md-3 ms-auto">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-light bg-light"
                                placeholder="Nama / NIK..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-teal px-4">Filter</button>
                        @if(request()->anyFilled(['desa_id', 'jabatan', 'status', 'search']))
                            <a href="{{ route('kecamatan.pemerintahan.aparatur.index') }}" class="btn btn-light"
                                title="Reset"><i class="fas fa-undo"></i></a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="width: 250px;">Aparatur
                            </th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Desa</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Jabatan</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Masa Jabatan</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Status Verifikasi</th>
                            <th class="pe-4 py-3 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aparatur as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="avatar-sm bg-soft-teal text-teal-600 rounded-circle fw-bold d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                            <small class="text-muted">{{ $item->nik }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->desa->nama_desa }}</td>
                                <td>
                                    <span
                                        class="badge bg-light text-dark border-0 rounded-pill px-3">{{ $item->jabatan }}</span>
                                    @if($item->status_jabatan == 'Pj')
                                        <span class="badge bg-soft-warning text-warning-700 rounded-pill ms-1">Pj</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-medium">{{ $item->tanggal_mulai->format('d M Y') }}</div>
                                    <div class="text-muted x-small">
                                        @if($item->tanggal_akhir)
                                            s.d {{ $item->tanggal_akhir->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($item->status_verifikasi == 'Terverifikasi')
                                        <span class="badge bg-soft-teal text-teal-700 rounded-pill border-0"><i
                                                class="fas fa-check-circle me-1"></i> Terverifikasi</span>
                                    @elseif($item->status_verifikasi == 'Perlu Perbaikan')
                                        <span class="badge bg-soft-danger text-danger-700 rounded-pill border-0"><i
                                                class="fas fa-exclamation-circle me-1"></i> Perlu Perbaikan</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary-700 rounded-pill border-0">Belum
                                            Diverifikasi</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-light rounded-circle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item py-2"
                                                    href="{{ route('kecamatan.pemerintahan.aparatur.show', $item->id) }}"><i
                                                        class="fas fa-eye me-2 text-muted"></i> Lihat Detail</a></li>
                                            <li><a class="dropdown-item py-2"
                                                    href="{{ route('kecamatan.pemerintahan.aparatur.edit', $item->id) }}"><i
                                                        class="fas fa-edit me-2 text-muted"></i> Ubah Data</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('kecamatan.pemerintahan.aparatur.destroy', $item->id) }}"
                                                    method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 text-danger"><i
                                                            class="fas fa-trash-alt me-2"></i> Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="{{ asset('img/empty-data.svg') }}" alt="Empty"
                                        style="width: 120px; opacity: 0.5;">
                                    <p class="text-muted mt-3">Tidak ditemukan data aparatur desa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($aparatur->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $aparatur->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .bg-soft-teal {
            background: rgba(20, 184, 166, 0.1);
        }

        .bg-soft-primary {
            background: rgba(30, 66, 159, 0.1);
        }

        .bg-soft-warning {
            background: rgba(245, 158, 11, 0.1);
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.1);
        }

        .bg-soft-secondary {
            background: rgba(100, 116, 139, 0.1);
        }

        .text-teal-600 {
            color: #0d9488;
        }

        .text-teal-700 {
            color: #0f766e;
        }

        .text-warning-600 {
            color: #d97706;
        }

        .text-warning-700 {
            color: #b45309;
        }

        .text-danger-700 {
            color: #b91c1c;
        }

        .x-small {
            font-size: 0.75rem;
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