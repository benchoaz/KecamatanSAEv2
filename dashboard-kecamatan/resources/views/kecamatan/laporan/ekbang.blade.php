@extends('layouts.kecamatan')

@section('title', 'Laporan Monev Ekbang')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('kecamatan.laporan.index') }}"
                class="btn btn-xs btn-light rounded-pill px-3 text-secondary text-decoration-none border shadow-sm small">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Rekap Umum
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-900 text-slate-900 mb-1">Laporan Monev Ekbang</h2>
                <p class="text-slate-500 mb-0">Hasil monitoring pembangunan wilayah dan realisasi serapan anggaran desa.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light border rounded-pill px-4 shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Cetak Format Resmi
                </button>
            </div>
        </div>
    </div>

    <!-- Official Header (Visible on print) -->
    <div class="d-none d-print-block text-center mb-5">
        <h4 class="fw-bold mb-0">PEMERINTAH {{ strtoupper(appProfile()->region_parent ?? 'KABUPATEN PROBOLINGGO') }}</h4>
        <h3 class="fw-bold mb-0">{{ strtoupper(appProfile()->region_level . ' ' . appProfile()->region_name) }}</h3>
        <p class="mb-0">Alamat: {{ appProfile()->address ?? 'Alamat Kantor' }}</p>
        <hr style="border: 2px solid #000; opacity: 1;">
        <h5 class="fw-bold mt-4">LAPORAN MONITORING DAN EVALUASI EKONOMI PEMBANGUNAN</h5>
        <p>Periode Tahun Anggaran {{ $year }}</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white p-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-slate-900 mb-0">Matriks Monitoring Pembangunan</h5>
            <div class="d-flex gap-3">
                <span class="badge bg-success-50 text-success-700 px-3 py-2 rounded-pill border border-success-100">Agregat
                    Data</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="ps-4 py-3 small fw-bold">DESA</th>
                            <th class="py-3 small fw-bold">KEGIATAN</th>
                            <th class="py-3 small fw-bold">LOKASI</th>
                            <th class="py-3 small fw-bold text-center">TAHAP</th>
                            <th class="py-3 small fw-bold text-end">REALISASI (AGREGAT)</th>
                            <th class="py-3 small fw-bold text-center">FISIK (%)</th>
                            <th class="py-3 small fw-bold text-center">HASIL MONEV</th>
                            <th class="pe-4 py-3 small fw-bold">REKOMENDASI PEMBINAAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-slate-800">{{ $report->desa->nama_desa }}</div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-slate-700">{{ $report->aspek->nama_aspek }}</div>
                                    <div class="text-slate-400" style="font-size: 11px;">Ref: #{{ $report->uuid }}</div>
                                </td>
                                <td><span class="small text-slate-600">Wilayah Desa</span></td>
                                <td class="text-center">
                                    <span class="badge bg-info-50 text-info-700 rounded-pill small">TB I</span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-slate-700">Rp 125.000.000</div>
                                    <div class="text-slate-400 small" style="font-size: 10px;">(Total Serapan)</div>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 6px; width: 80px; margin: 0 auto;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%;"></div>
                                    </div>
                                    <span class="small fw-bold text-slate-700 mt-1 d-block">85%</span>
                                </td>
                                <td class="text-center">
                                    @if($report->status == 'approved')
                                        <span class="badge bg-emerald-100 text-emerald-700 rounded-pill px-3">Sesuai</span>
                                    @elseif($report->status == 'reviewed')
                                        <span class="badge bg-amber-100 text-amber-700 rounded-pill px-3">Sebagian Sesuai</span>
                                    @else
                                        <span class="badge bg-rose-100 text-rose-700 rounded-pill px-3">Perlu Klarifikasi</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <p class="small text-slate-500 mb-0">Administrasi telah diverifikasi. Pertahankan progres
                                        fisik.</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-slate-400 small">
                                    Tidak ada data monitoring untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Official Disclaimer Footer -->
    <div class="official-footer mt-5 p-4 rounded-4 border bg-light d-print-block">
        <div class="row align-items-center">
            <div class="col-8">
                <p class="mb-0 text-slate-600 small italic" style="font-size: 11px;">
                    <strong>Pernyataan:</strong> Laporan ini adalah instrumen monitoring administratif sesuai Peraturan
                    Pemerintah Nomor 17 Tahun 2018 tentang Kecamatan.
                    Tidak bersifat menyimpulkan kerugian negara dan murni untuk kepentingan pembinaan penyelenggaraan
                    pemerintahan desa.
                </p>
            </div>
            <div class="col-4 text-end">
                <div class="d-none d-print-block mt-4" style="margin-right: 50px;">
                    <p class="mb-0 small">Mengetahui,</p>
                    <p class="fw-bold mb-5 small">Camat {{ appProfile()->region_name }}</p>
                    <p class="fw-bold mb-0">__________________________</p>
                    <p class="small mb-0">NIP. ..................................</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .btn,
            .sidebar,
            .header,
            .content-header {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }

            .table thead th {
                background-color: #f8fafc !important;
                color: #000 !important;
            }

            .badge {
                border: 1px solid #ccc !important;
                color: #000 !important;
                background: transparent !important;
            }

            body {
                background: #fff !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .page-content {
                padding: 0 !important;
            }
        }
    </style>
@endpush