@extends('layouts.kecamatan')

@section('title', 'Statistik Pelayanan Masyarakat')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4 d-flex justify-content-between align-items-end pb-2">
            <div>
                <h1 class="text-slate-900 fw-bold fs-3 mb-1">Pusat Analitik & Statistik</h1>
                <p class="text-slate-400 small mb-0">Visualisasi data pelayanan, ekonomi (UMKM), dan ketenagakerjaan (Loker).</p>
            </div>
            <div class="text-end">
                <span class="badge bg-slate-100 text-slate-500 rounded-pill px-3 py-2 fw-bold text-[10px] uppercase">
                    <i class="fas fa-calendar-alt me-1"></i> Terakhir Diperbarui: {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="alert bg-emerald-50 border-emerald-100 rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
            <div class="w-10 h-10 rounded-3 bg-emerald-500 flex items-center justify-center text-white flex-shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-emerald-900 small">Ringkasan Ekonomi & Sosial</h6>
                <p class="mb-0 text-emerald-700/70 text-[11px]">Kecamatan kini memiliki {{ $stats['umkm_total'] }} UMKM terdaftar dan {{ $stats['loker_active'] }} lowongan kerja aktif.</p>
            </div>
        </div>

        <!-- Metric Row 1: Pelayanan -->
        <h6 class="text-[10px] text-slate-400 fw-bold uppercase tracking-[0.2em] mb-3">
            <i class="fas fa-concierge-bell me-2"></i> Kinerja Pelayanan & Pengaduan
        </h6>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-slate-100 h-100">
                    <p class="text-[10px] text-slate-400 fw-bold uppercase tracking-wider mb-1">Total Laporan</p>
                    <h3 class="fw-bold text-slate-900 mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-amber-100 bg-amber-50/30 h-100">
                    <p class="text-[10px] text-amber-500 fw-bold uppercase tracking-wider mb-1">Menunggu Respon</p>
                    <h3 class="fw-bold text-amber-600 mb-0">{{ $stats['pending'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-blue-100 bg-blue-50/30 h-100">
                    <p class="text-[10px] text-blue-500 fw-bold uppercase tracking-wider mb-1">Giat Proses</p>
                    <h3 class="fw-bold text-blue-600 mb-0">{{ $stats['processed'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-emerald-100 bg-emerald-50/30 h-100">
                    <p class="text-[10px] text-emerald-500 fw-bold uppercase tracking-wider mb-1">Sudah Selesai</p>
                    <h3 class="fw-bold text-emerald-600 mb-0">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Metric Row 2: Ekonomi & Ketenagakerjaan -->
        <h6 class="text-[10px] text-slate-400 fw-bold uppercase tracking-[0.2em] mb-3 mt-5">
            <i class="fas fa-store me-2"></i> Statistik Sektoral (UMKM & Loker)
        </h6>
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-indigo-100 bg-indigo-50/20 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <p class="text-[10px] text-indigo-500 fw-bold uppercase tracking-wider mb-0">Total UMKM</p>
                        <i class="fas fa-store text-indigo-200"></i>
                    </div>
                    <h3 class="fw-bold text-slate-900 mb-0">{{ $stats['umkm_total'] }}</h3>
                    <p class="text-[10px] text-indigo-600/70 mt-1 font-medium">{{ $stats['umkm_active'] }} Bisnis Aktif</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-teal-100 bg-teal-50/20 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <p class="text-[10px] text-teal-500 fw-bold uppercase tracking-wider mb-0">Lowongan Kerja</p>
                        <i class="fas fa-briefcase text-teal-200"></i>
                    </div>
                    <h3 class="fw-bold text-slate-900 mb-0">{{ $stats['loker_total'] }}</h3>
                    <p class="text-[10px] text-teal-600/70 mt-1 font-medium">{{ $stats['loker_active'] }} Lowongan Aktif</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-violet-100 bg-violet-50/20 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <p class="text-[10px] text-violet-500 fw-bold uppercase tracking-wider mb-0">Tenaga Ahli</p>
                        <i class="fas fa-user-ninja text-violet-200"></i>
                    </div>
                    <h3 class="fw-bold text-slate-900 mb-0">{{ $stats['pekerja_total'] }}</h3>
                    <p class="text-[10px] text-violet-600/70 mt-1 font-medium">{{ $stats['pekerja_public'] }} Siap Kerja (Publik)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 border border-rose-100 bg-rose-50/20 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <p class="text-[10px] text-rose-500 fw-bold uppercase tracking-wider mb-0">Total Laporan</p>
                        <i class="fas fa-file-contract text-rose-200"></i>
                    </div>
                    <h3 class="fw-bold text-slate-900 mb-0">{{ $stats['total'] }}</h3>
                    <p class="text-[10px] text-rose-600/70 mt-1 font-medium">Pengaduan & Pelayanan</p>
                </div>
            </div>
        </div>

        <h6 class="text-[10px] text-slate-400 fw-bold uppercase tracking-[0.2em] mb-3">
            <i class="fas fa-project-diagram me-2"></i> Analisis Detail Sebaran
        </h6>
        <div class="row g-4">
            <!-- Categorization -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 border border-slate-100 h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-slate-50">
                        <h6 class="mb-0 fw-bold text-slate-800 small">Klasifikasi Layanan</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="space-y-4">
                            @foreach($stats['by_category'] as $cat)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-slate-600 small fw-medium">{{ $cat->jenis_layanan }}</span>
                                        <span class="text-slate-900 fw-bold small">{{ $cat->total }}</span>
                                    </div>
                                    @php 
                                        $percent = $stats['total'] > 0 ? ($cat->total / $stats['total']) * 100 : 0; 
                                        $colorClass = $cat->jenis_layanan == 'Pengaduan Pelayanan' ? 'bg-rose-400' : 
                                                     ($cat->jenis_layanan == 'Permohonan Informasi' ? 'bg-blue-400' : 'bg-emerald-400');
                                    @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $colorClass }} rounded-pill" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Village Distribution -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 border border-slate-100 h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-slate-50">
                        <h6 class="mb-0 fw-bold text-slate-800 small">Sebaran Wilayah (Top Desa)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-slate-50/50 sticky-top">
                                    <tr>
                                        <th class="ps-4 py-2 text-slate-400 text-[10px] fw-bold uppercase">Nama Desa</th>
                                        <th class="pe-4 py-2 text-end text-slate-400 text-[10px] fw-bold uppercase">Volume Laporan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['by_village'] as $vil)
                                        <tr>
                                            <td class="ps-4 py-3 text-slate-600 small">{{ $vil->desa ? $vil->desa->nama_desa : 'Umum' }}</td>
                                            <td class="pe-4 py-3 text-end fw-bold text-slate-900 small">{{ $vil->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection