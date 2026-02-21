@extends('layouts.kecamatan')

@section('title', 'Rekapitulasi Pelayanan & Pengaduan - ' . $year)

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('kecamatan.laporan.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <span class="badge bg-slate-100 text-slate-600 px-3 py-1 rounded-pill small fw-800 border">REKAPITULASI PELAYANAN</span>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-900 text-slate-900 mb-1">Pelayanan & Pengaduan Masyarakat</h2>
                <p class="text-slate-500 mb-0">Statistik real-time pengajuan berkas dan laporan aspirasi warga tahun {{ $year }}.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light border rounded-pill px-4 shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Row -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <form action="{{ route('kecamatan.laporan.pelayanan') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-slate-700">Tahun</label>
                    <select name="year" class="form-select rounded-3">
                        @for($i = date('Y'); $i >= 2024; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-slate-700">Filter Wilayah Desa</label>
                    <select name="desa_id" class="form-select rounded-3">
                        <option value="">Semua Desa</option>
                        @php $desas = \App\Models\Desa::orderBy('nama_desa')->get(); @endphp
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ $desaId == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-brand-600 text-white px-4 rounded-3 h-48 w-100 shadow-sm">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 4px solid #6366f1;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase">Total Pengajuan</span>
                    <h2 class="fw-900 text-slate-900 mt-2 mb-0">{{ number_format($stats['total']) }}</h2>
                    <p class="text-slate-400 small mt-1 mb-0">Berkas & Laporan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 4px solid #10b981;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase">Selesai</span>
                    <h2 class="fw-900 text-success mt-2 mb-0">{{ number_format($stats['by_status']['selesai'] ?? 0) }}</h2>
                    <p class="text-slate-400 small mt-1 mb-0">Tindak Lanjut Tuntas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 4px solid #f59e0b;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase">Dalam Proses</span>
                    <h2 class="fw-900 text-warning mt-2 mb-0">{{ number_format($stats['by_status']['diproses'] ?? 0) }}</h2>
                    <p class="text-slate-400 small mt-1 mb-0">Sedang Ditangani</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 4px solid #ef4444;">
                <div class="card-body p-4">
                    <span class="text-slate-500 small fw-bold text-uppercase">Menunggu</span>
                    <h2 class="fw-900 text-danger mt-2 mb-0">{{ number_format($stats['by_status']['menunggu_verifikasi'] ?? 0) }}</h2>
                    <p class="text-slate-400 small mt-1 mb-0">Antrian Verifikasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Breakdown by Category -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold text-slate-900 mb-0">Sebaran Kategori</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @foreach($stats['by_category'] as $cat => $count)
                            <div class="list-group-item px-0 border-slate-100 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-slate-700 text-uppercase">{{ $cat }}</span>
                                    <div class="progress mt-1" style="height: 6px; width: 200px;">
                                        @php $percent = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0; @endphp
                                        <div class="progress-bar bg-brand-600" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                                <span class="badge bg-slate-100 text-slate-700 rounded-pill">{{ $count }}</span>
                            </div>
                        @endforeach
                        @if($stats['by_category']->isEmpty())
                            <p class="text-center text-slate-400 py-4">Belum ada data</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold text-slate-900 mb-0">Tren Bulanan ({{ $year }})</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @php
                            $months = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun', 7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'];
                        @endphp
                        @foreach($months as $mNum => $mName)
                            <div class="d-flex align-items-center gap-3">
                                <span class="small text-slate-500 fw-bold" style="width: 40px;">{{ $mName }}</span>
                                <div class="flex-grow-1 bg-slate-50 rounded-pill p-1" style="height: 12px;">
                                    @php 
                                        $max = $trend->max() ?: 1;
                                        $val = $trend[$mNum] ?? 0;
                                        $w = ($val / $max) * 100;
                                    @endphp
                                    <div class="bg-brand-500 rounded-pill h-100" style="width: {{ $w }}%"></div>
                                </div>
                                <span class="small text-slate-700" style="width: 30px;">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Village Breakdown -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between">
            <h5 class="fw-bold text-slate-900 mb-0">Rekap Per Desa</h5>
            <span class="small text-slate-500">Agregasi semua kategori</span>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover border-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="border-0 rounded-start text-slate-600 small fw-800">Nama Desa</th>
                            <th class="border-0 text-slate-600 small text-center fw-800">Total Masuk</th>
                            <th class="border-0 text-slate-600 small text-center fw-800">Persentase</th>
                            <th class="border-0 rounded-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['by_village'] as $vData)
                            <tr>
                                <td class="fw-bold text-slate-700">{{ $vData->desa->nama_desa ?? 'Manual/Umum' }}</td>
                                <td class="text-center">{{ $vData->count }}</td>
                                <td class="text-center">
                                    @php $pV = $stats['total'] > 0 ? ($vData->count / $stats['total']) * 100 : 0; @endphp
                                    {{ number_format($pV, 1) }}%
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 4px; width: 100px; display: inline-flex;">
                                        <div class="progress-bar bg-slate-300" style="width: {{ $pV }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($stats['by_village']->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center text-slate-400 py-4">Data tidak ditemukan untuk periode ini</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Official Footer -->
    <div class="official-footer mt-5 p-4 rounded-4 border bg-light">
        <p class="mb-0 text-slate-600 small italic">
            <strong>Catatan:</strong> Laporan ini merangkum kinerja pelayanan publik dan pengelolaan pengaduan masyarakat tingkat kecamatan secara periodik.
        </p>
    </div>
@endsection

@push('styles')
<style>
    .hover-up:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    .bg-brand-600 { background-color: #0d9488 !important; }
    .bg-brand-500 { background-color: #14b8a6 !important; }
    .h-48 { height: 48px; }
</style>
@endpush
