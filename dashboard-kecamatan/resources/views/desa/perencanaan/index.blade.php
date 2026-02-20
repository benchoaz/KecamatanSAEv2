@extends('layouts.desa')

@section('title', 'Perencanaan Desa - ' . auth()->user()->desa->nama_desa)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('desa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('desa.pemerintahan.index') }}">Pemerintahan</a>
                            </li>
                            <li class="breadcrumb-item active">Perencanaan</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold mb-0">Modul Perencanaan Desa</h2>
                </div>
                <div>
                    <div class="dropdown">
                        <button class="btn btn-brand-600 text-white dropdown-toggle rounded-pill px-4 shadow-sm"
                            type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-plus me-2"></i> Tambah Dokumen
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                            <li><a class="dropdown-item py-2"
                                    href="{{ route('desa.pemerintahan.detail.perencanaan.create', ['tahun' => 2025]) }}">
                                    <i class="fas fa-archive me-2 text-muted"></i> Arsip Pra-Sistem (≤ 2025)
                                </a></li>
                            <li><a class="dropdown-item py-2"
                                    href="{{ route('desa.pemerintahan.detail.perencanaan.create', ['tahun' => 2026]) }}">
                                    <i class="fas fa-sync me-2 text-primary"></i> Transisi Sistem (2026)
                                </a></li>
                            <li><a class="dropdown-item py-2"
                                    href="{{ route('desa.pemerintahan.detail.perencanaan.create', ['tahun' => 2027]) }}">
                                    <i class="fas fa-project-diagram me-2 text-success"></i> Terstruktur (≥ 2027)
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Planning Cycle Timeline -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-slate-700"><i class="fas fa-history me-2"></i> Siklus Perencanaan Desa</h6>
            </div>
            <div class="card-body bg-light-green-subtle">
                <div class="row text-center g-4 position-relative">
                    <!-- Connecting Line -->
                    <div class="d-none d-md-block position-absolute start-0 top-50 translate-middle-y w-100"
                        style="z-index: 0; padding: 0 50px;">
                        <div class="border-top border-2 border-secondary border-opacity-25 dashed-line"></div>
                    </div>

                    <!-- Step 1: Musdes -->
                    <div class="col-md-3 position-relative" style="z-index: 1;">
                        <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-bottom border-4 border-success">
                            <div class="timeline-badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                style="width: 40px; height: 40px; font-weight: bold;">
                                1
                            </div>
                            <h6 class="fw-bold mb-1">Musyawarah Desa</h6>
                            <span class="badge bg-success-soft text-success mb-2">Juni</span>
                            <p class="small text-muted mb-0">Dilaksanakan oleh BPD paling lambat bulan Juni tahun
                                sebelumnya.</p>
                        </div>
                    </div>

                    <!-- Step 2: RKP Desa -->
                    <div class="col-md-3 position-relative" style="z-index: 1;">
                        <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-bottom border-4 border-warning">
                            <div class="timeline-badge bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                style="width: 40px; height: 40px; font-weight: bold;">
                                2
                            </div>
                            <h6 class="fw-bold mb-1">Penyusunan RKP</h6>
                            <span class="badge bg-warning-soft text-warning-900 mb-2">Juli - Sept</span>
                            <p class="small text-muted mb-0">Mulai disusun Juli, penetapan paling lambat akhir September.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3: APB Desa -->
                    <div class="col-md-3 position-relative" style="z-index: 1;">
                        <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-bottom border-4 border-danger">
                            <div class="timeline-badge bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                style="width: 40px; height: 40px; font-weight: bold;">
                                3
                            </div>
                            <h6 class="fw-bold mb-1">Penetapan APBDes</h6>
                            <span class="badge bg-danger-soft text-danger mb-2">Okt - Des</span>
                            <p class="small text-muted mb-0">Pembahasan & penetapan Perdes APBDes paling lambat 31 Desember.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4: Realisasi -->
                    <div class="col-md-3 position-relative" style="z-index: 1;">
                        <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-bottom border-4 border-primary">
                            <div class="timeline-badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                style="width: 40px; height: 40px; font-weight: bold;">
                                4
                            </div>
                            <h6 class="fw-bold mb-1">Realisasi APBDes</h6>
                            <span class="badge bg-primary-soft text-primary mb-2">Jan - Des</span>
                            <p class="small text-muted mb-0">Pelaksanaan APB Desa mulai Januari s.d Desember tahun berjalan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-light-green text-brand-600 rounded-3 p-3">
                            <i class="fas fa-file-invoice fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $perencanaan->count() }}</h4>
                            <p class="text-muted small mb-0">Total Dokumen</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-light-blue text-primary rounded-3 p-3">
                            <i class="fas fa-paper-plane fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $perencanaan->where('status', 'dikirim')->count() }}</h4>
                            <p class="text-muted small mb-0">Sedang Dikirim</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-light-warning text-warning rounded-3 p-3">
                            <i class="fas fa-reply fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $perencanaan->where('status', 'dikembalikan')->count() }}</h4>
                            <p class="text-muted small mb-0">Perlu Revisi</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-light-success text-success rounded-3 p-3">
                            <i class="fas fa-check-double fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $perencanaan->where('status', 'diterima')->count() }}</h4>
                            <p class="text-muted small mb-0">Disetujui</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tahun</th>
                            <th>Dokumen</th>
                            <th>Mode</th>
                            <th>Nomor & Tanggal Perdes</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perencanaan as $p)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark fs-5">{{ $p->tahun }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light p-2 rounded">
                                            <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                        </div>
                                        <div class="lh-sm">
                                            <span class="d-block fw-bold text-slate-800">{{ $p->tipe_dokumen }}</span>
                                            @if($p->mode_input === 'arsip')
                                                <small class="text-muted">Dokumen Arsip (Pra Sistem)</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($p->mode_input === 'arsip')
                                        <span class="badge bg-secondary-soft text-secondary rounded-pill px-3">Archive</span>
                                    @elseif($p->mode_input === 'transisi')
                                        <span class="badge bg-primary-soft text-primary rounded-pill px-3">Transition</span>
                                    @else
                                        <span class="badge bg-success-soft text-success rounded-pill px-3">Structured</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->nomor_perdes)
                                        <div class="lh-sm">
                                            <span class="d-block small text-dark fw-bold">{{ $p->nomor_perdes }}</span>
                                            <small
                                                class="text-muted">{{ $p->tanggal_perdes ? $p->tanggal_perdes->format('d/m/Y') : '' }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'draft' => ['class' => 'bg-secondary', 'label' => 'DRAFT'],
                                            'dikirim' => ['class' => 'bg-primary', 'label' => 'DIKIRIM'],
                                            'dikembalikan' => ['class' => 'bg-warning', 'label' => 'REVISI'],
                                            'diterima' => ['class' => 'bg-success', 'label' => 'DITERIMA']
                                        ];
                                        $s = $statusMap[$p->status] ?? ['class' => 'bg-secondary', 'label' => strtoupper($p->status)];
                                    @endphp
                                    <span class="badge {{ $s['class'] }} rounded-pill px-3">{{ $s['label'] }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                        <a href="{{ route('desa.pemerintahan.detail.perencanaan.show', $p->id) }}"
                                            class="btn btn-sm btn-white px-3 border-0" title="Lihat">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                        @if($p->status === 'draft')
                                            <form action="{{ route('desa.pemerintahan.detail.perencanaan.submit', $p->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-white px-3 border-0 border-start"
                                                    title="Kirim ke Kecamatan" onclick="return confirm('Kirim dokumen ini?')">
                                                    <i class="fas fa-paper-plane text-success"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ asset('storage/' . $p->file_ba) }}" target="_blank"
                                            class="btn btn-sm btn-white px-3 border-0 border-start" title="Download">
                                            <i class="fas fa-download text-dark"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <img src="https://illustrations.popsy.co/gray/data-report.svg" alt="Empty"
                                            style="height: 151px;" class="mb-3 opacity-50">
                                        <h5 class="text-muted">Belum ada data perencanaan</h5>
                                        <p class="text-muted small">Silakan tambah dokumen perencanaan baru berdasarkan tahun
                                            berjalan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-light-green {
            background-color: rgba(157, 193, 131, 0.1);
        }

        .bg-light-blue {
            background-color: rgba(37, 99, 235, 0.1);
        }

        .bg-light-warning {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .bg-light-success {
            background-color: rgba(16, 185, 129, 0.1);
        }

        .bg-primary-soft {
            background-color: #eef2ff;
            color: #4f46e5;
        }

        .bg-secondary-soft {
            background-color: #f1f5f9;
            color: #475569;
        }

        .bg-success-soft {
            background-color: #ecfdf5;
            color: #059669;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        .btn-brand-600 {
            background-color: #9DC183;
            border-color: #9DC183;
        }

        .btn-brand-600:hover {
            background-color: #7fa665;
            border-color: #7fa665;
        }

        .bg-light-green-subtle {
            background-color: #f8fbf8;
        }

        .bg-warning-soft {
            background-color: #fef3c7;
        }

        .text-warning-900 {
            color: #78350f;
        }

        .bg-danger-soft {
            background-color: #fee2e2;
        }

        .dashed-line {
            border-top-style: dashed !important;
        }
    </style>
@endsection