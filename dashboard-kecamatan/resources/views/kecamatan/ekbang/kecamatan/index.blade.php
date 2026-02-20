@extends('layouts.kecamatan')

@section('title', 'Monev Keuangan & Pembangunan (Ekbang)')

@section('content')
    <div class="content-header mb-4">
        <div class="header-title">
            <h1 class="text-slate-900 fw-bold display-6">Command Center Ekbang</h1>
            <p class="text-slate-500 fs-5 mb-0">Monitoring Realisasi APBDes: DD, ADD, PAD, & Bagi Hasil Pajak (BHP)</p>
            <div class="header-accent"></div>
        </div>
    </div>

    <!-- Pagu Realisasi Agregat -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100"
                style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); transition: transform 0.3s ease;">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="badge bg-white shadow-sm text-slate-900 px-3 py-2 rounded-pill fw-bold small">DANA DESA
                        </div>
                        <div class="bg-white bg-opacity-10 p-2 rounded-3">
                            <i class="fas fa-landmark text-white"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h2 class="mb-1 fw-800 fs-1">Rp 24,5 M</h2>
                        <p class="small mb-0 opacity-75">Total Pagu Agregat 2025</p>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-pill p-1" style="height: 10px;">
                        <div class="bg-primary rounded-pill h-100" style="width: 45%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small opacity-75">
                        <span>Realisasi: 45%</span>
                        <span>Target: 100%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100"
                style="background: linear-gradient(135deg, #312e81 0%, #4338ca 100%); transition: transform 0.3s ease;">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="badge bg-white shadow-sm text-indigo-900 px-3 py-2 rounded-pill fw-bold small">ADD / PAD
                        </div>
                        <div class="bg-white bg-opacity-10 p-2 rounded-3">
                            <i class="fas fa-hand-holding-dollar text-white"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h2 class="mb-1 fw-800 fs-1">Rp 8,2 M</h2>
                        <p class="small mb-0 opacity-75">Realisasi ADD & Pendapatan Asli</p>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-pill p-1" style="height: 10px;">
                        <div class="bg-emerald-400 rounded-pill h-100" style="width: 60%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small opacity-75">
                        <span>Realisasi: 60%</span>
                        <span>Target: 100%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100"
                style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); transition: transform 0.3s ease;">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="badge bg-white shadow-sm text-emerald-900 px-3 py-2 rounded-pill fw-bold small">BAGI
                            HASIL</div>
                        <div class="bg-white bg-opacity-10 p-2 rounded-3">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h2 class="mb-1 fw-800 fs-1">Rp 3,1 M</h2>
                        <p class="small mb-0 opacity-75">Hutang Pajak & Retribusi Daerah</p>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-pill p-1" style="height: 10px;">
                        <div class="bg-amber-400 rounded-pill h-100" style="width: 30%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small opacity-75">
                        <span>Realisasi: 30%</span>
                        <span>Target: 100%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100"
                style="background: linear-gradient(135deg, #991b1b 0%, #be123c 100%); transition: transform 0.3s ease;">
                <div class="card-body p-4 text-white d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="badge bg-white shadow-sm text-rose-900 px-3 py-2 rounded-pill fw-bold small">ZONA
                                AUDIT</div>
                            <div class="bg-white bg-opacity-10 p-2 rounded-3 ripple">
                                <i class="fas fa-shield-virus text-white"></i>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h2 class="mb-1 fw-800 fs-1">2 DESA</h2>
                            <p class="small mb-0 opacity-75">Keterlambatan SPJ Tahap II</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 border border-white border-opacity-20 rounded-3 p-2 text-center">
                        <span class="fw-bold small text-uppercase tracking-wider">Tingkat Risiko: Prioritas Tinggi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tools & Exports Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <a href="{{ route('kecamatan.ekbang.export', ['desa_id' => request('desa_id')]) }}"
                class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden"
                    style="transition: all 0.3s ease; border: 2px dashed #4f46e5 !important; background: #eef2ff;">
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="bg-indigo-100 text-indigo-600 rounded-circle p-4 mb-3">
                            <i class="fas fa-box-archive fa-3x"></i>
                        </div>
                        <h5 class="fw-bold text-slate-800 mb-2">Paket Audit Ekbang</h5>
                        <p class="text-slate-500 small mb-0">Export Dokumen Pembangunan (PDF ZIP)</p>
                    </div>
                    <div class="card-footer border-0 p-3 d-flex justify-content-between align-items-center"
                        style="background: #4f46e5;">
                        <span class="text-white fw-bold small uppercase">Unduh Paket Audit</span>
                        <i class="fas fa-download text-white"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Monitoring Grid -->
    <div class="section-header-premium mb-4">
        <h4><i class="fas fa-tower-broadcast me-3 text-indigo-600"></i>Log Realisasi Pembangunan</h4>
        <div class="section-divider"></div>
        <div class="d-flex gap-2">
            <button class="btn btn-white shadow-sm border border-slate-200 rounded-3 px-3 py-2 fw-bold text-slate-700">
                <i class="fas fa-filter me-2 text-slate-400"></i> SARING
            </button>
            <button class="btn btn-indigo shadow-sm rounded-3 px-3 py-2 fw-bold">
                <i class="fas fa-download me-2"></i> EKSPOR
            </button>
        </div>
    </div>

    <div class="card card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">IDENTITAS DESA</th>
                        <th>PROGRAM & SUMBER DANA</th>
                        <th class="text-center">PROGRES FISIK</th>
                        <th class="text-center">STATUS AUDIT</th>
                        <th class="text-end pe-4">VALIDASI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubmissions as $s)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-slate-100 text-slate-700 rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 40px; height: 40px;">
                                        {{ substr($s->desa->nama_desa, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-900">Desa {{ $s->desa->nama_desa }}</div>
                                        <div class="text-slate-400 small fw-500">REF: #{{ substr($s->uuid, 0, 8) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-slate-700">{{ $s->aspek->nama_aspek }}</div>
                                <div class="d-flex gap-2 mt-2">
                                    <span
                                        class="badge bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-pill px-2 py-1"
                                        style="font-size: 0.65rem;">
                                        <i class="fas fa-money-bill-transfer me-1"></i> DANA DESA
                                    </span>
                                    <span
                                        class="badge bg-slate-100 text-slate-600 border border-slate-200 rounded-pill px-2 py-1"
                                        style="font-size: 0.65rem;">
                                        <i class="fas fa-calendar me-1"></i> TA {{ $s->tahun }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div style="width: 140px; margin: 0 auto;">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="fw-bold text-slate-700">75%</span>
                                        <span class="text-slate-400">Lengkap</span>
                                    </div>
                                    <div class="progress rounded-pill bg-slate-100" style="height: 6px;">
                                        <div class="progress-bar bg-emerald-500 rounded-pill shadow-sm" style="width: 75%">
                                        </div>
                                    </div>
                                </div>
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
                                    <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i>
                                    {{ strtoupper($s->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('kecamatan.verifikasi.show', $s->uuid) }}"
                                    class="btn btn-icon btn-light rounded-circle shadow-sm border">
                                    <i class="fas fa-magnifying-glass text-slate-600 px-2 py-2"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-emerald-50 text-emerald-600 d-inline-flex p-4 rounded-circle mb-3">
                                    <i class="fas fa-shield-check fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-slate-800">Wilayah Kondusif</h5>
                                <p class="text-slate-500 small">Belum ada anomali atau laporan pembangunan yang memerlukan
                                    atensi segera.</p>
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

        .btn-indigo {
            background: #4f46e5;
            color: #fff;
        }

        .ripple {
            animation: ripple-animation 2s infinite;
        }

        @keyframes ripple-animation {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
    </style>
@endpush