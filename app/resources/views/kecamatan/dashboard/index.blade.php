@extends('layouts.kecamatan')

@section('title', 'Dashboard Monitoring')

@section('content')
    <div class="dashboard container-fluid px-4 py-4">
        <!-- Modern Formal Welcome Section -->
        <div class="welcome-banner premium-welcome p-5 rounded-5 mb-5 position-relative overflow-hidden shadow-2xl animate-entrance"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255,255,255,0.05);">

            {{-- Animated Background Objects --}}
            <div class="position-absolute top-0 right-0 w-100 h-100 opacity-10 pointer-events-none">
                <div
                    class="position-absolute top-0 end-0 translate-middle w-50 h-100 bg-info rounded-circle blur-3xl opacity-20">
                </div>
                <div
                    class="position-absolute bottom-0 start-0 translate-middle w-50 h-100 bg-primary rounded-circle blur-3xl opacity-20">
                </div>
            </div>

            <div class="position-relative z-2">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span
                        class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1.5 rounded-pill small tracking-widest text-uppercase fw-bold">
                        <i class="fas fa-shield-check me-1"></i> Otoritas Sistem
                    </span>
                    <div class="h-px bg-white bg-opacity-10 flex-grow-1"></div>
                    <span class="text-slate-400 small fw-bold uppercase tracking-wider">
                        <i class="far fa-calendar-alt me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                    @if($stats['last_updated'])
                        <div class="h-px bg-white bg-opacity-10 w-4"></div>
                        <span class="text-slate-500 small tracking-wider">
                            <i class="fas fa-sync-alt me-1"></i> Terakhir Sinkron: <span class="text-info">{{ \Carbon\Carbon::parse($stats['last_updated'])->diffForHumans() }}</span>
                        </span>
                    @endif
                </div>

                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-4 mb-2">
                            <div class="welcome-avatar">
                                <div
                                    class="w-16 h-16 rounded-3xl bg-gradient-to-br from-info to-primary flex items-center justify-center text-white shadow-xl">
                                    <i class="fas fa-user-tie fa-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="display-5 fw-black text-white mb-1 tracking-tight">
                                    Selamat Datang, <span class="text-info">{{ auth()->user()->nama_lengkap }}</span>
                                </h1>
                                <p class="text-slate-400 fs-5 mb-0 font-medium">
                                    Pusat Kendali Operasional Administrasi <span
                                        class="text-slate-200 fw-bold">{{ appProfile()->region_level }}
                                        {{ appProfile()->region_name }}</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Icon Background -->
            <div class="position-absolute end-0 bottom-0 opacity-5 mb-n5 me-n4 z-1">
                <i class="fas fa-landmark fa-12x text-white"></i>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6 animate-entrance" style="animation-delay: 0.1s">
                <div class="card border-0 glass-card-v2 shadow-premium rounded-5 h-100 overflow-hidden stat-item group">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="flex-shrink-0 bg-primary-50 text-primary-600 rounded-4xl d-flex align-items-center justify-content-center shadow-sm group-hover:bg-primary-600 group-hover:text-white transition-all duration-500"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-users-viewfinder fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-tertiary small fw-black text-uppercase tracking-[0.2em] mb-1">Total
                                Penduduk</span>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-black text-primary-900 stat-value tracking-tighter"
                                    data-count="{{ $stats['total_penduduk'] ?? 0 }}">0</h2>
                                <span class="text-success small fw-bold"><i class="fas fa-chart-line"></i></span>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="h-1 w-100 bg-primary opacity-10"></div>
                    <div class="px-4 py-2 bg-slate-50 border-top border-slate-100">
                        <span class="text-[10px] fw-black text-slate-400 uppercase tracking-widest">
                            <i class="fas fa-globe me-1"></i> Sumber: tatadesa.com
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 animate-entrance" style="animation-delay: 0.2s">
                <div class="card border-0 glass-card-v2 shadow-premium rounded-5 h-100 overflow-hidden stat-item group">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="flex-shrink-0 bg-success-50 text-success-600 rounded-4xl d-flex align-items-center justify-content-center shadow-sm group-hover:bg-success-600 group-hover:text-white transition-all duration-500"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-file-circle-check fa-2x"></i>
                        </div>
                        <div>
                            <span
                                class="d-block text-tertiary small fw-black text-uppercase tracking-[0.2em] mb-1">Permohonan</span>
                            <h2 class="mb-0 fw-black text-primary-900 stat-value tracking-tighter"
                                data-count="{{ $stats['laporan_masuk'] ?? 0 }}">0</h2>
                        </div>
                    </div>
                    <div class="h-1 w-100 bg-success opacity-10"></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 animate-entrance" style="animation-delay: 0.3s">
                <div class="card border-0 glass-card-v2 shadow-premium rounded-5 h-100 overflow-hidden stat-item group">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="flex-shrink-0 bg-info-50 text-info-500 rounded-4xl d-flex align-items-center justify-content-center shadow-sm group-hover:bg-info-500 group-hover:text-white transition-all duration-500"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-map-location-dot fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-tertiary small fw-black text-uppercase tracking-[0.2em] mb-1">Jumlah
                                Desa</span>
                            <h2 class="mb-0 fw-black text-primary-900 stat-value tracking-tighter"
                                data-count="{{ $stats['jumlah_desa'] ?? 0 }}">0</h2>
                        </div>
                    </div>
                    <div class="h-1 w-100 bg-info opacity-10"></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 animate-entrance" style="animation-delay: 0.4s">
                <div class="card border-0 glass-card-v2 shadow-premium rounded-5 h-100 overflow-hidden stat-item group">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="flex-shrink-0 bg-warning-50 text-warning-500 rounded-4xl d-flex align-items-center justify-content-center shadow-sm group-hover:bg-warning-500 group-hover:text-white transition-all duration-500"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-tertiary small fw-black text-uppercase tracking-[0.2em] mb-1">Visitor
                                Hari Ini</span>
                            <h2 class="mb-0 fw-black text-primary-900 stat-value tracking-tighter"
                                data-count="{{ $stats['pengunjung_hari_ini'] ?? 0 }}">0</h2>
                        </div>
                    </div>
                    <div class="h-1 w-100 bg-warning opacity-10"></div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            @if(!auth()->user()->isAdminPelayanan())
                <!-- Domain Grid -->
                <div class="col-xl-8 animate-entrance" style="animation-delay: 0.5s">
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-black text-primary-900 mb-1 tracking-tight">Bidang Pengawasan & Layanan Wilayah</h4>
                            <p class="text-tertiary small mb-0 font-medium">Akses cepat ke setiap modul operasional kecamatan
                            </p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.pemerintahan.index') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div
                                        class="position-absolute top-0 end-0 w-32 h-32 bg-primary opacity-0 group-hover:opacity-5 blur-3xl -translate-y-1/2 translate-x-1/2 transition-opacity">
                                    </div>
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-slate-900 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg group-hover:scale-110 transition-transform"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-building-user fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5 group-hover:text-primary-600">
                                                Pemerintahan</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Monitoring SK, BPD, & Profil Desa
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.ekbang.index') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div
                                        class="position-absolute top-0 end-0 w-32 h-32 bg-teal-500 opacity-0 group-hover:opacity-5 blur-3xl -translate-y-1/2 translate-x-1/2 transition-opacity">
                                    </div>
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-teal-600 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg group-hover:scale-110 transition-transform"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-chart-line-up fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5 group-hover:text-teal-600">Ekonomi &
                                                Pembangunan</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Monitoring APBDes & Realisasi Fisik
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.kesra.index') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div
                                        class="position-absolute top-0 end-0 w-32 h-32 bg-rose-500 opacity-0 group-hover:opacity-5 blur-3xl -translate-y-1/2 translate-x-1/2 transition-opacity">
                                    </div>
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-rose-600 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg group-hover:scale-110 transition-transform"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-heart-pulse fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5 group-hover:text-rose-600">
                                                Kesejahteraan Rakyat</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Data Bantuan Sosial & Layanan Warga
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.trantibum.index') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div
                                        class="position-absolute top-0 end-0 w-32 h-32 bg-amber-500 opacity-0 group-hover:opacity-5 blur-3xl -translate-y-1/2 translate-x-1/2 transition-opacity">
                                    </div>
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-amber-500 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg group-hover:scale-110 transition-transform"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-shield-halved fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5 group-hover:text-amber-600">Trantibum
                                                & Linmas</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Laporan Ketertiban & Keamanan
                                                Wilayah</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Pelayanan Domain quick access for Admin Pelayanan -->
                <div class="col-xl-8 animate-entrance" style="animation-delay: 0.5s">
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-black text-primary-900 mb-1 tracking-tight">Bidang Pelayanan Terpadu</h4>
                            <p class="text-tertiary small mb-0 font-medium">Akses cepat ke manajemen pelayanan dan pengaduan</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.pelayanan.inbox') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-indigo-600 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-inbox fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5">Inbox Terpadu</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Kelola semua berkas permohonan
                                                warga</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.pelayanan.pengaduan') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-rose-500 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-bullhorn fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5">Pengaduan Masyarakat</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Pantau laporan via WhatsApp</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('kecamatan.pelayanan.visitor.index') }}" class="text-decoration-none group">
                                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                                    style="background: white;">
                                    <div class="d-flex align-items-center gap-4 position-relative z-10">
                                        <div class="icon-box bg-emerald-500 text-white rounded-4xl d-flex align-items-center justify-content-center shadow-lg"
                                            style="width: 65px; height: 65px;">
                                            <i class="fas fa-book fa-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-black text-primary-900 fs-5">Buku Tamu</h6>
                                            <p class="mb-0 text-tertiary small leading-snug">Catat kunjungan warga</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Log -->
            <div class="col-xl-4 animate-entrance" style="animation-delay: 0.6s">
                <div class="card border-0 glass-card-v2 shadow-premium rounded-5 overflow-hidden h-100 border border-white">
                    <div class="card-header bg-white bg-opacity-80 border-0 py-4 px-4">
                        <h5 class="fw-black text-primary-900 mb-0 tracking-tight flex items-center justify-between">
                            Audit Aktivitas
                            <span class="badge bg-slate-100 text-slate-500 rounded-pill font-bold">Terbaru</span>
                        </h5>
                    </div>
                    <div class="card-body p-0 max-h-[500px] overflow-y-auto custom-scrollbar">
                        <div class="list-group list-group-flush">
                            @foreach($activities as $activity)
                                <div
                                    class="list-group-item border-start-0 border-end-0 border-top-0 border-bottom border-primary-50 px-4 py-4 bg-transparent hover:bg-slate-50 transition-colors">
                                    <div class="d-flex gap-4">
                                        <div class="flex-shrink-0 bg-{{ $activity->type }} text-white rounded-3xl d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 40px; height: 40px; font-size: 14px;">
                                            <i class="fas {{ $activity->icon }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small text-primary-900 fw-black mb-1">{{ $activity->message }}</div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-tertiary fw-bold"
                                                    style="font-size: 10px; color: #94a3b8 !important;">
                                                    <i class="far fa-clock me-1"></i> {{ $activity->time }}
                                                </span>
                                                <span
                                                    class="text-[10px] font-bold text-{{ $activity->type }} uppercase tracking-widest opacity-70">
                                                    {{ $activity->type }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 text-center py-4">
                        <a href="{{ route('kecamatan.laporan.index') }}"
                            class="text-brand-600 font-extrabold text-[11px] uppercase tracking-[2px] text-decoration-none hover:text-brand-800 transition-colors">
                            RIWAYAT LENGKAP <i class="fas fa-chevron-right ms-2 scale-75"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .animate-entrance {
            opacity: 1 !important;
            transform: none !important;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .glass-card-v2 {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 1) !important;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .glass-card-v2:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-item .stat-value {
            font-size: 2.2rem;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .shadow-soft {
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
        }

        .domain-card {
            border: 1px solid #f1f5f9 !important;
        }

        .domain-card:hover {
            border-color: #e2e8f0 !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05) !important;
            transform: scale(1.02);
        }

        .rounded-4xl {
            border-radius: 1.5rem;
        }

        .rounded-5 {
            border-radius: 2rem;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        .fw-black {
            font-weight: 900 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const counters = document.querySelectorAll('.stat-value');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count')) || 0;
                if (target === 0) {
                    counter.textContent = '0';
                    return;
                }
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString('id-ID');
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString('id-ID');
                    }
                }, 16);
            });
        });
    </script>
@endpush