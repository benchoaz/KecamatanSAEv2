@extends('layouts.desa')

@section('title', 'Data Keamanan & Ketertiban')
@section('breadcrumb', 'Trantibum / Data Trantibum')

@section('content')
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title fw-bold text-primary-900">
                        <i class="fas fa-shield-halved text-brand-600 me-2"></i>
                        Data Keamanan & Ketertiban
                    </h1>
                    <p class="text-secondary mt-1 mb-0">Kelola dan pantau data keamanan, ketertiban, dan linmas desa</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('desa.trantibum.create') }}" class="btn btn-brand shadow-sm">
                        <i class="fas fa-plus me-2"></i>Tambah Data Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm rounded-3 p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="stat-value text-success fw-bold">
                                {{ $submissions->where('status', 'approved')->count() }}</div>
                            <div class="stat-label text-muted small">Disetujui</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm rounded-3 p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-value text-warning fw-bold">
                                {{ $submissions->where('status', 'submitted')->count() }}</div>
                            <div class="stat-label text-muted small">Menunggu Verifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm rounded-3 p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-secondary-subtle text-secondary me-3">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <div class="stat-value text-secondary fw-bold">
                                {{ $submissions->where('status', 'draft')->count() }}</div>
                            <div class="stat-label text-muted small">Draft</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm rounded-3 p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="fas fa-list"></i>
                        </div>
                        <div>
                            <div class="stat-value text-primary fw-bold">{{ $submissions->total() }}</div>
                            <div class="stat-label text-muted small">Total Data</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                @if($submissions->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="text-muted mt-3">Belum Ada Data Trantibum</h5>
                        <p class="text-secondary">Mulai dengan menambahkan data keamanan dan ketertiban pertama Anda.</p>
                        <a href="{{ route('desa.trantibum.create') }}" class="btn btn-brand mt-2">
                            <i class="fas fa-plus me-2"></i>Tambah Data Baru
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">Periode</th>
                                    <th class="text-nowrap">Aspek</th>
                                    <th class="text-nowrap">Status</th>
                                    <th class="text-nowrap">Tanggal Submit</th>
                                    <th class="text-nowrap text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td class="fw-medium">{{ \Carbon\Carbon::parse($submission->periode)->format('F Y') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-primary-subtle text-primary">{{ $submission->aspek->nama_aspek ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($submission->status === 'approved')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                                            @elseif($submission->status === 'submitted')
                                                <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Verifikasi</span>
                                            @elseif($submission->status === 'rejected')
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="fas fa-file-alt me-1"></i>Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('desa.trantibum.show', $submission->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($submission->status === 'draft')
                                                    <a href="{{ route('desa.trantibum.edit', $submission->id) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection