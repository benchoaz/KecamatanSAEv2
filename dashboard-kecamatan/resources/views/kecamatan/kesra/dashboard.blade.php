@extends('layouts.kecamatan')

@section('title', 'Dashboard Kesejahteraan Rakyat')

@section('content')
    <div class="content-header mb-4">
        <div class="header-title">
            <h1 class="text-slate-900 fw-bold display-6">Dashboard Kesejahteraan Rakyat</h1>
            <p class="text-slate-500 fs-5 mb-0">Pemantauan Bidang Pelayanan & Pemberdayaan Sosial (Pasal 439)</p>
            <div class="header-accent"></div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-premium h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-box icon-box-indigo shadow-sm">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <span class="badge-pill-premium badge-indigo">
                            {{ $stats['waiting'] }} LAPORAN
                        </span>
                    </div>
                    <h5 class="fw-bold text-slate-800 mb-2">Menunggu Telaah</h5>
                    <p class="text-slate-500 small mb-4">Laporan desa yang perlu ditinjau administrasinya sebelum validasi.
                    </p>
                    <a href="{{ route('kecamatan.kesra.bansos.index') }}"
                        class="btn btn-outline-indigo w-100 rounded-3 fw-bold">
                        Buka Laporan <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-premium h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-box icon-box-amber shadow-sm">
                            <i class="fas fa-undo"></i>
                        </div>
                        <span class="badge-pill-premium badge-amber">
                            {{ $stats['returned'] }} LAPORAN
                        </span>
                    </div>
                    <h5 class="fw-bold text-slate-800 mb-2">Dikembalikan</h5>
                    <p class="text-slate-500 small mb-4">Laporan yang memerlukan perbaikan dokumen atau data dari pihak
                        desa.</p>
                    <a href="{{ route('kecamatan.kesra.bansos.index') }}"
                        class="btn btn-outline-amber w-100 rounded-3 fw-bold">
                        Lihat Detail <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-premium h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-box icon-box-emerald shadow-sm">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <span class="badge-pill-premium badge-emerald">
                            {{ $stats['reviewed'] }} SELESAI
                        </span>
                    </div>
                    <h5 class="fw-bold text-slate-800 mb-2">Direkomendasikan</h5>
                    <p class="text-slate-500 small mb-4">Laporan yang telah tervalidasi dan diteruskan ke tingkat
                        pimpinan/Camat.</p>
                    <a href="{{ route('kecamatan.kesra.rekomendasi.index') }}"
                        class="btn btn-outline-emerald w-100 rounded-3 fw-bold">
                        Daftar Rekomendasi <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tools & Exports Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <a href="{{ route('kecamatan.kesra.export', ['desa_id' => request('desa_id')]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden"
                    style="transition: all 0.3s ease; border: 2px dashed #f43f5e !important; background: #fff1f2;">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="bg-rose-100 text-rose-600 rounded-circle p-4 mb-3">
                            <i class="fas fa-box-archive fa-3x"></i>
                        </div>
                        <h5 class="fw-bold text-slate-800 mb-2">Paket Audit Kesra</h5>
                        <p class="text-slate-500 small mb-0">Export Dokumen Sosial (PDF ZIP)</p>
                    </div>
                    <div class="card-footer border-0 p-3 d-flex justify-content-between align-items-center"
                        style="background: #f43f5e;">
                        <span class="text-white fw-bold small uppercase">Unduh Paket Audit</span>
                        <i class="fas fa-download text-white"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Monitoring Table -->
    <div class="section-header-premium mb-4">
        <h4><i class="fas fa-clock-rotate-left me-3 text-indigo-600"></i>Laporan Terbaru Bidang Kesra</h4>
        <div class="section-divider"></div>
        <a href="{{ route('kecamatan.kesra.bansos.index') }}" class="text-decoration-none fw-bold text-indigo-600 small">
            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="card card-premium overflow-hidden mt-4">
        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ASAL DESA</th>
                        <th>PROGRAM / ASPEK</th>
                        <th class="text-center">TAHUN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubmissions as $s)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-slate-800">Desa {{ $s->desa->nama_desa }}</div>
                                <div class="text-slate-400 small fw-500">{{ $s->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-slate-700">{{ $s->aspek->nama_aspek }}</div>
                                <div class="small text-slate-400">Verifikasi Bidang Kesejahteraan</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-slate-100 text-slate-600 border border-slate-200 rounded-pill px-3 py-1">
                                    {{ $s->tahun }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $sStyle = [
                                        'submitted' => 'badge-sky',
                                        'reviewed' => 'badge-indigo',
                                        'approved' => 'badge-emerald',
                                        'returned' => 'badge-rose'
                                    ][$s->status] ?? 'badge-slate';
                                @endphp
                                <span class="badge-pill-premium {{ $sStyle }}">
                                    {{ strtoupper($s->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('kecamatan.verifikasi.show', $s->uuid) }}"
                                    class="btn btn-icon btn-light rounded-circle shadow-sm border">
                                    <i class="fas fa-chevron-right text-slate-400 px-2 py-2"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-slate-50 text-slate-300 d-inline-flex p-4 rounded-circle mb-3">
                                    <i class="fas fa-inbox fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-slate-800">Antrian Kosong</h5>
                                <p class="text-slate-500 small">Belum ada laporan masuk untuk bidang kesejahteraan rakyat saat
                                    ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .btn-outline-indigo {
            border-color: #4f46e5;
            color: #4f46e5;
        }

        .btn-outline-indigo:hover {
            background: #4f46e5;
            color: #fff;
        }

        .btn-outline-amber {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .btn-outline-amber:hover {
            background: #f59e0b;
            color: #fff;
        }

        .btn-outline-emerald {
            border-color: #10b981;
            color: #10b981;
        }

        .btn-outline-emerald:hover {
            background: #10b981;
            color: #fff;
        }
    </style>
@endpush