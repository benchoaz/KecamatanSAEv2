@extends('layouts.trantibum')

@section('title', 'Monev Trantibum Wilayah')

@section('content')
    <div class="content-header mb-4">
        <div class="header-title">
            <h1 class="text-slate-900 fw-bold display-6">Monitoring Ketertiban & Keamanan</h1>
            <p class="text-slate-500 fs-5 mb-0">Pengawasan Situasi Kamtibmas & Response Koordinasi Desa</p>
            <div class="header-accent"></div>
        </div>
    </div>

    <!-- Security Radar Summary -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card card-premium h-100">
                <div class="card-body p-4 text-center">
                    <div class="icon-box icon-box-emerald mx-auto mb-3 shadow-sm">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h6 class="text-slate-500 small text-uppercase fw-800 tracking-wider">Rasio Insiden</h6>
                    <h2 class="text-slate-900 fw-bold my-2">Rendah</h2>
                    <span class="badge bg-emerald-100 text-emerald-700 px-3 py-1 rounded-pill small fw-bold">
                        <i class="fas fa-arrow-down me-1"></i> 12% Turun
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-premium h-100 border-rose-100">
                <div class="card-body p-4 text-center">
                    <div class="icon-box icon-box-rose mx-auto mb-3 shadow-sm pulse-rose">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <h6 class="text-slate-500 small text-uppercase fw-800 tracking-wider">Tugas Mendesak</h6>
                    <h2 class="text-slate-900 fw-bold my-2">2</h2>
                    <span class="badge bg-rose-100 text-rose-700 px-3 py-1 rounded-pill small fw-bold">
                        Prioritas Tinggi
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-premium h-100">
                <div class="card-body p-4 text-center">
                    <div class="icon-box icon-box-indigo mx-auto mb-3 shadow-sm">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <h6 class="text-slate-500 small text-uppercase fw-800 tracking-wider">Linmas Aktif</h6>
                    <h2 class="text-slate-900 fw-bold my-2">144</h2>
                    <span class="text-slate-400 small fw-500">Personil Terjun</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-premium h-100">
                <div class="card-body p-4 text-center">
                    <div class="icon-box icon-box-sky mx-auto mb-3 shadow-sm">
                        <i class="fas fa-satellite-dish"></i>
                    </div>
                    <h6 class="text-slate-500 small text-uppercase fw-800 tracking-wider">Status Sistem</h6>
                    <h2 class="text-sky-600 fw-bold my-2">Aktif</h2>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <span class="pulse-sky"></span>
                        <span class="text-slate-400 small fw-500">Node Terenkripsi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tools & Exports Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <a href="{{ route('kecamatan.trantibum.export', ['desa_id' => request('desa_id')]) }}"
                class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden"
                    style="transition: all 0.3s ease; border: 2px dashed #099279 !important; background: #ecfcf9;">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="bg-emerald-100 text-emerald-600 rounded-circle p-4 mb-3">
                            <i class="fas fa-box-archive fa-3x"></i>
                        </div>
                        <h5 class="fw-bold text-slate-800 mb-2">Paket Audit Trantibum</h5>
                        <p class="text-slate-500 small mb-0">Export Dokumen Trantibum (PDF ZIP)</p>
                    </div>
                    <div class="card-footer border-0 p-3 d-flex justify-content-between align-items-center"
                        style="background: #099268;">
                        <span class="text-white fw-bold small uppercase">Unduh Paket Audit</span>
                        <i class="fas fa-download text-white"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="section-header-premium mb-4">
        <h4><i class="fas fa-tower-observation me-3 text-indigo-600"></i>Laporan Masuk Seluruh Wilayah</h4>
        <div class="section-divider"></div>
        <button class="btn btn-white shadow-sm border border-slate-200 rounded-3 px-3 py-2 fw-bold text-slate-700">
            <i class="fas fa-filter me-2 text-slate-400"></i> SARING DESA
        </button>
    </div>

    <div class="card card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ASAL DESA</th>
                        <th>JENIS INSIDEN / KATEGORI</th>
                        <th class="text-center">URGENSI</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allReports as $report)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-slate-800">Desa {{ $report->desa->nama_desa }}</div>
                                <div class="text-slate-400 small fw-500">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="text-slate-900 fw-bold">{{ $report->aspek->nama_aspek }}</div>
                                <div class="small text-slate-500">
                                    {{ Str::limit($report->catatan_review ?? 'Pemantauan rutin situasi keamanan wilayah.', 60) }}
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $urgency = ['submitted' => 'Tinggi', 'draft' => 'Rendah', 'approved' => 'Normal'][$report->status] ?? 'Normal';
                                    $uClass = ['submitted' => 'text-rose-600', 'draft' => 'text-slate-400', 'approved' => 'text-emerald-600'][$report->status] ?? 'text-indigo-600';
                                    $bgClass = ['submitted' => 'bg-rose-50', 'draft' => 'bg-slate-50', 'approved' => 'bg-emerald-50'][$report->status] ?? 'bg-indigo-50';
                                @endphp
                                <span class="px-3 py-1 rounded-pill small fw-800 {{ $bgClass }} {{ $uClass }} border">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ strtoupper($urgency) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $sStyle = [
                                        'submitted' => 'badge-sky',
                                        'reviewed' => 'badge-indigo',
                                        'approved' => 'badge-emerald',
                                        'returned' => 'badge-rose'
                                    ][$report->status] ?? 'badge-slate';
                                @endphp
                                <span class="badge-pill-premium {{ $sStyle }}">
                                    {{ strtoupper($report->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('kecamatan.trantibum.show', $report->id) }}"
                                    class="btn btn-icon btn-light rounded-circle shadow-sm border">
                                    <i class="fas fa-magnifying-glass text-slate-600 px-2 py-2"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-slate-50 text-slate-200 d-inline-flex p-4 rounded-circle mb-3">
                                    <i class="fas fa-shield-heart fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-slate-800">Situasi Kondusif</h5>
                                <p class="text-slate-500 small">Belum ada laporan gangguan keamanan atau ketertiban dari desa.
                                </p>
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
        .fw-800 {
            font-weight: 800;
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .btn-white {
            background: #fff;
            color: #334155;
        }

        .pulse-rose {
            animation: pulse-rose-animation 2s infinite;
        }

        @keyframes pulse-rose-animation {
            0% {
                box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(244, 63, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(244, 63, 94, 0);
            }
        }

        .pulse-sky {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #0ea5e9;
            animation: pulse-sky-animation 2s infinite;
        }

        @keyframes pulse-sky-animation {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(14, 165, 233, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(14, 165, 233, 0);
            }
        }
    </style>
@endpush