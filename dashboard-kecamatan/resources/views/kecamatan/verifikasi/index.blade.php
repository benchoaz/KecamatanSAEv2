@extends('layouts.kecamatan')

@section('title', 'Inbox Verifikasi')

    @section('content')
        <div class="content-header mb-4">
            <div class="header-title">
                <h1>Inbox Verifikasi</h1>
                <p class="text-muted">Tinjau dan proses laporan dari Desa</p>
            </div>
        </div>

        <!-- Stats / Quick Filters (Optional, but let's stick to filters for now) -->

        <div class="card bg-white border-gray-200 shadow-sm mb-4">
            <form action="{{ route('verifikasi.index') }}" method="GET" class="filter-grid p-4">
                <div class="filter-item">
                    <label class="form-label fw-bold text-gray-700">Desa</label>
                    <select name="desa_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>
                                {{ $desa->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <label class="form-label fw-bold text-gray-700">Menu/Sektor</label>
                    <select name="menu_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Sektor</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ request('menu_id') == $menu->id ? 'selected' : '' }}>
                                {{ $menu->nama_menu }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <label class="form-label fw-bold text-gray-700">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Menunggu Kasi
                            (Submitted)</option>
                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Menunggu Camat (Reviewed)
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)
                        </option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan (Returned)
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)
                        </option>
                    </select>
                </div>
                <div class="filter-actions h-100 d-flex align-items-end">
                    <a href="{{ route('verifikasi.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>

        <div class="card bg-white border-gray-200 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="ps-4">Desa</th>
                            <th>Nama Laporan</th>
                            <th>Aspek</th>
                            <th>Tahun/Periode</th>
                            <th>Status</th>
                            <th>Tgl Diajukan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                            <tr>
                                <td class="ps-4"><strong class="text-gray-900">{{ $s->desa->nama_desa }}</strong></td>
                                <td>
                                    <div class="table-main-text text-gray-800">{{ $s->menu->nama_menu }}</div>
                                    <small class="text-muted">By: {{ $s->submittedBy->name }}</small>
                                </td>
                                <td>{{ $s->aspek->nama_aspek }}</td>
                                <td>
                                    <div class="text-gray-700">{{ $s->tahun }}</div>
                                    <small class="badge-periode">{{ ucfirst($s->periode) }}
                                        {{ $s->bulan ? '- ' . $s->bulan : '' }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'draft' => 'bg-gray-100 text-gray-600',
                                            'submitted' => 'bg-amber-100 text-amber-700',
                                            'returned' => 'bg-blue-100 text-blue-700',
                                            'reviewed' => 'bg-teal-100 text-teal-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                        ][$s->status] ?? 'bg-gray-100 text-gray-600';

                                        $statusLabel = [
                                            'draft' => 'Draft',
                                            'submitted' => 'Menunggu Kasi',
                                            'returned' => 'Dikembalikan',
                                            'reviewed' => 'Menunggu Camat',
                                            'approved' => 'Selesai',
                                            'rejected' => 'Ditolak',
                                        ][$s->status] ?? $s->status;
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-2 py-1">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-muted small">{{ $s->submitted_at ? $s->submitted_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('verifikasi.show', $s->uuid) }}" class="btn btn-sm btn-teal">
                                        <i class="fas fa-search-plus me-1"></i> Periksa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x mb-3 text-gray-200"></i>
                                        <p class="text-muted">Tidak ada laporan yang perlu diproses.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top">
                {{ $submissions->links() }}
            </div>
        </div>
    @endsection

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .badge-periode {
            font-size: 0.75rem;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            color: #475569;
        }

        .table-main-text {
            font-weight: 500;
            color: #1e293b;
        }

        .bg-amber-100 {
            background-color: #fef3c7;
        }

        .text-amber-700 {
            color: #b45309;
        }

        .bg-blue-100 {
            background-color: #dbeafe;
        }

        .text-blue-700 {
            color: #1d4ed8;
        }

        .bg-teal-100 {
            background-color: #ccfbf1;
        }

        .text-teal-700 {
            color: #0f766e;
        }

        .bg-emerald-100 {
            background-color: #d1fae5;
        }

        .text-emerald-700 {
            color: #047857;
        }

        .bg-rose-100 {
            background-color: #ffe4e6;
        }

        .text-rose-700 {
            color: #be123c;
        }

        .bg-gray-100 {
            background-color: #f1f5f9;
        }

        .text-gray-600 {
            color: #475569;
        }

        .btn-teal {
            background-color: #14b8a6;
            color: white;
        }

        .btn-teal:hover {
            background-color: #0d9488;
            color: white;
        }
    </style>
@endpush