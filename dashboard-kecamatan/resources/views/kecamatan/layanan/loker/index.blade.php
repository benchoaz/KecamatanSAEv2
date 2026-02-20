@extends('layouts.kecamatan')

@section('title', 'Manajemen Loker Warga')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                        <div class="header-title">
                            <h4 class="card-title fw-bold">Daftar Lowongan Kerja</h4>
                            <p class="text-muted small mb-0">Kelola dan verifikasi informasi pekerjaan dari warga.</p>
                        </div>
                        <div>
                            <a href="{{ route('kecamatan.loker.create') }}"
                                class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-plus me-1"></i> Tambah Loker
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Filter Area -->
                        <div class="px-4 py-3 bg-light border-bottom">
                            <form action="{{ route('kecamatan.loker.index') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fas fa-search text-muted"></i></span>
                                        <input type="text" name="q" class="form-control border-start-0"
                                            placeholder="Cari judul, kategori, atau kontak..." value="{{ request('q') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">Semua Status</option>
                                        <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>
                                            Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success w-100">Filter</button>
                                </div>
                                @if(request()->hasAny(['q', 'status']))
                                    <div class="col-md-2">
                                        <a href="{{ route('kecamatan.loker.index') }}"
                                            class="btn btn-outline-secondary w-100">Reset</a>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-soft-primary">
                                    <tr>
                                        <th class="ps-4">Info Pekerjaan</th>
                                        <th>Kontak & Lokasi</th>
                                        <th>Sumber</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loker as $l)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-40 rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-3 fw-bold">
                                                        {{ substr($l->title, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $l->title }}</div>
                                                        <div class="text-muted small">{{ $l->job_category }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small fw-medium">{{ $l->contact_wa }}</div>
                                                <div class="text-muted extra-small">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $l->desa ? $l->desa->nama_desa : ($l->nama_desa_manual ?: '-') }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($l->source == 'web_form')
                                                    <span class="badge bg-soft-info text-info"><i class="fas fa-globe me-1"></i>
                                                        Warga</span>
                                                @elseif($l->source == 'admin_input')
                                                    <span class="badge bg-soft-dark text-dark"><i
                                                            class="fas fa-user-shield me-1"></i> Admin</span>
                                                @else
                                                    <span class="badge bg-soft-success text-success"><i
                                                            class="fab fa-whatsapp me-1"></i> WA</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $l->status_badge }}">
                                                    {{ $l->status_label }}
                                                </span>
                                                @if($l->is_sensitive)
                                                    <span class="badge bg-danger rounded-circle p-1 ms-1" title="Sensitif/Privat">
                                                        <i class="fas fa-eye-slash extra-small"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('kecamatan.loker.edit', $l->id) }}"
                                                        class="btn btn-sm btn-icon btn-soft-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('kecamatan.loker.destroy', $l->id) }}" method="POST"
                                                        onsubmit="return confirm('Hapus loker ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-soft-danger"
                                                            title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <img src="{{ asset('img/no-data.svg') }}"
                                                    onerror="this.src='https://illustrations.popsy.co/white/surreal-hourglass.svg'"
                                                    style="height: 120px;" class="mb-3 d-block mx-auto opacity-50">
                                                Data lowongan tidak ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-0 py-3 px-4">
                            {{ $loker->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .extra-small {
            font-size: 0.7rem;
        }

        .bg-soft-primary {
            background-color: rgba(22, 163, 74, 0.05);
        }

        .bg-soft-info {
            background-color: rgba(56, 189, 248, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(22, 163, 74, 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection