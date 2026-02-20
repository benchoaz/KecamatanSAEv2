@extends('layouts.kecamatan')

@section('title', 'Laporan Rekap Umum Kecamatan')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-slate-100 text-slate-600 px-3 py-1 rounded-pill small fw-800 border">LAPORAN
                MANAJERIAL</span>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-900 text-slate-900 mb-1">Rekap Umum Kecamatan</h2>
                <p class="text-slate-500 mb-0">Ringkasan hasil monitoring dan evaluasi penyelenggaraan pemerintahan desa.
                </p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light border rounded-pill px-4 shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Cetak
                </button>
                <a href="{{ route('kecamatan.laporan.export') }}?type=pdf"
                    class="btn btn-slate-900 text-white rounded-pill px-4 shadow-sm">
                    <i class="fas fa-file-pdf me-2"></i> Ekspor PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Row -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <form action="{{ route('kecamatan.laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-slate-700">Tahun Anggaran</label>
                    <select name="year" class="form-select rounded-3">
                        @for($i = date('Y'); $i >= 2024; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-slate-700">Desa Wilayah</label>
                    <select name="desa_id" class="form-select rounded-3">
                        <option value="">Semua Desa (Agregat)</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ $desaId == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-brand-600 text-white px-4 rounded-3 h-48 w-100 shadow-sm">
                        <i class="fas fa-filter me-2"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #64748b !important;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase tracking-wider">Cakupan Monitoring</span>
                    <h2 class="fw-900 text-slate-900 mt-2 mb-1">{{ $totalDesa }} Desa</h2>
                    <p class="text-slate-500 small mb-0">Wilayah kerja {{ appProfile()->region_level }}
                        {{ appProfile()->region_name }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase tracking-wider">Laporan Masuk</span>
                    <h2 class="fw-900 text-slate-900 mt-2 mb-1">{{ $totalSubmissions }} Kegiatan</h2>
                    <p class="text-slate-500 small mb-0">Telah diverifikasi administratif</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase tracking-wider">Perlu Pembinaan</span>
                    <h2 class="fw-900 text-slate-900 mt-2 mb-1">{{ $statusCounts['returned'] ?? 0 }} Desa</h2>
                    <p class="text-slate-500 small mb-0">Membutuhkan klarifikasi administratif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Navigation Cards -->
    <div class="section-title mb-4 d-flex align-items-center gap-3">
        <h5 class="fw-bold text-slate-900 mb-0">Daftar Laporan Sektoral</h5>
        <div class="flex-grow-1 border-bottom border-slate-100"></div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Ekbang -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up bg-white">
                <div class="card-body p-4">
                    <div class="bg-slate-100 text-slate-700 rounded-4 p-3 fs-3 d-inline-block mb-4 shadow-sm">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-3">Laporan Monev Ekbang</h5>
                    <p class="text-slate-500 small mb-4">Rekapitulasi progres fisik dan realisasi pembangunan wilayah tahap
                        I & II.</p>
                    <a href="{{ route('kecamatan.laporan.ekbang') }}?year={{ $year }}&desa_id={{ $desaId }}"
                        class="btn btn-outline-slate-900 w-100 rounded-pill fw-bold">
                        Buka Laporan <i class="fas fa-arrow-right-long ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pemerintahan -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up bg-white">
                <div class="card-body p-4">
                    <div class="bg-slate-100 text-slate-700 rounded-4 p-3 fs-3 d-inline-block mb-4 shadow-sm">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-3">Administrasi Pemerintahan</h5>
                    <p class="text-slate-500 small mb-4">Status kepatuhan dokumen RPJMDes, RKPDes, LKPJ, dan LPPD desa.</p>
                    <a href="{{ route('kecamatan.laporan.pemerintahan') }}?year={{ $year }}&desa_id={{ $desaId }}"
                        class="btn btn-outline-slate-900 w-100 rounded-pill fw-bold">
                        Buka Laporan <i class="fas fa-arrow-right-long ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kesra -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up bg-white">
                <div class="card-body p-4">
                    <div class="bg-slate-100 text-slate-700 rounded-4 p-3 fs-3 d-inline-block mb-4 shadow-sm">
                        <i class="fas fa-dove"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-3">Kesejahteraan Rakyat</h5>
                    <p class="text-slate-500 small mb-4">Monitoring pelaksanaan program bantuan sosial dan kegiatan
                        kemasyarakatan.</p>
                    <a href="{{ route('kecamatan.laporan.kesra') }}?year={{ $year }}&desa_id={{ $desaId }}"
                        class="btn btn-outline-slate-900 w-100 rounded-pill fw-bold">
                        Buka Laporan <i class="fas fa-arrow-right-long ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Trantibum -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up bg-white">
                <div class="card-body p-4">
                    <div class="bg-slate-100 text-slate-700 rounded-4 p-3 fs-3 d-inline-block mb-4 shadow-sm">
                        <i class="fas fa-masks-theater"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-3">Ketentraman & Ketertiban</h5>
                    <p class="text-slate-500 small mb-4">Monitoring kegiatan pembinaan linmas, jaga warga, dan stabilitas
                        wilayah.</p>
                    <a href="{{ route('kecamatan.laporan.trantibum') }}?year={{ $year }}&desa_id={{ $desaId }}"
                        class="btn btn-outline-slate-900 w-100 rounded-pill fw-bold">
                        Buka Laporan <i class="fas fa-arrow-right-long ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Disclaimer Footer -->
    <div class="official-footer mt-5 p-4 rounded-4 border bg-light">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="mb-0 text-slate-600 small italic">
                    <strong>Catatan Penting:</strong> Laporan ini bersifat administratif sebagai bahan monitoring dan
                    evaluasi internal Kecamatan.
                    Data yang ditampilkan adalah agregat hasil verifikasi dan tidak ditujukan untuk audit investigatif
                    keuangan.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <span class="text-slate-400 small">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-slate-900 {
            background-color: #0f172a;
        }

        .btn-slate-900:hover {
            background-color: #1e293b;
        }

        .btn-outline-slate-900 {
            border-color: #0f172a;
            color: #0f172a;
        }

        .btn-outline-slate-900:hover {
            background-color: #0f172a;
            color: #fff;
        }

        .h-48 {
            height: 48px;
        }

        @media print {

            .sidebar,
            .header,
            .card-body form,
            .btn {
                display: none !important;
            }

            .app-container {
                padding: 0 !important;
            }

            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
@endpush