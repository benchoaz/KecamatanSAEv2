@extends(auth()->user()->desa_id ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', 'Seksi Ekonomi & Pembangunan')

@section('content')
    <div class="dashboard container-fluid px-4 py-4">
        <!-- Premium Section Header -->
        <div class="welcome-banner p-5 rounded-5 mb-5 position-relative overflow-hidden shadow-2xl animate-entrance"
            style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); border: 1px solid rgba(255,255,255,0.05);">

            <div class="position-absolute top-0 right-0 w-100 h-100 opacity-10 pointer-events-none">
                <div
                    class="position-absolute top-0 end-0 translate-middle w-50 h-100 bg-emerald-400 rounded-circle blur-3xl opacity-20">
                </div>
            </div>

            <div class="position-relative z-2">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span
                        class="badge bg-emerald-400 bg-opacity-10 text-emerald-400 border border-emerald-400 border-opacity-20 px-3 py-1.5 rounded-pill small tracking-widest text-uppercase fw-bold">
                        <i class="fas fa-leaf me-1"></i> Seksi Ekbang
                    </span>
                    <div class="h-px bg-white bg-opacity-10 flex-grow-1"></div>
                </div>

                <h1 class="display-5 fw-black text-white mb-2 tracking-tight">Ekonomi & Pembangunan</h1>
                <p class="text-emerald-100 fs-5 mb-0 font-medium max-w-2xl">
                    {{ $isOperator ? 'Modul Pelaporan & Input Data Desa' : 'Monitoring Pembangunan & Realisasi Wilayah' }}
                </p>
            </div>

            <div class="position-absolute end-0 bottom-0 opacity-5 mb-n5 me-n4 z-1">
                <i class="fas fa-city fa-12x text-white"></i>
            </div>
        </div>

        @if($healthMetrics)
            <div class="row g-4 mb-5">
                <!-- Health Check Pillar: Realisasi APBDes -->
                <div class="col-xl-4 col-md-6">
                    <div
                        class="card border-0 glass-card-v2 shadow-premium rounded-5 p-4 h-100 transition-all hover:scale-[1.02]">
                        <div class="d-flex align-items-center gap-4">
                            <div class="icon-box {{ $healthMetrics['realisasi'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-4xl d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 65px; height: 65px;">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span
                                    class="d-block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Realisasi
                                    APBDes</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="fw-black text-primary-900 mb-0">Tahun {{ date('Y') }}</h5>
                                    @if($healthMetrics['realisasi'])
                                        <span
                                            class="badge bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-pill font-black text-[10px] uppercase">Terlapor</span>
                                    @else
                                        <span
                                            class="badge bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1.5 rounded-pill font-black text-[10px] uppercase">Belum
                                            Lapor</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Check Pillar: Monev DD -->
                <div class="col-xl-4 col-md-6">
                    <div
                        class="card border-0 glass-card-v2 shadow-premium rounded-5 p-4 h-100 transition-all hover:scale-[1.02]">
                        <div class="d-flex align-items-center gap-4">
                            <div class="icon-box {{ $healthMetrics['monev'] ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} rounded-4xl d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 65px; height: 65px;">
                                <i class="fas fa-hand-holding-dollar fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="d-block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Monev
                                    Dana Desa</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="fw-black text-primary-900 mb-0">Monitoring</h5>
                                    @if($healthMetrics['monev'])
                                        <span
                                            class="badge bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-pill font-black text-[10px] uppercase">Aktif</span>
                                    @else
                                        <span
                                            class="badge bg-amber-50 text-amber-600 border border-amber-100 px-3 py-1.5 rounded-pill font-black text-[10px] uppercase">Kosong</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Check Pillar: Status Umum -->
                <div class="col-xl-4 col-md-6">
                    <div
                        class="card border-0 glass-card-v2 shadow-premium rounded-5 p-4 h-100 transition-all hover:scale-[1.02]">
                        <div class="d-flex align-items-center gap-4">
                            <div class="icon-box {{ $healthMetrics['status'] === 'Sehat' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} rounded-4xl d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 65px; height: 65px;">
                                <i class="fas fa-shield-heart fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span
                                    class="d-block text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Integritas
                                    Pembangunan</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="fw-black text-primary-900 mb-0">Status</h5>
                                    <span
                                        class="badge {{ $healthMetrics['status'] === 'Sehat' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} border px-3 py-1.5 rounded-pill font-black text-[10px] uppercase">
                                        {{ strtoupper($healthMetrics['status']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-black text-primary-900 mb-1 tracking-tight">
                    {{ $isOperator ? 'Tugas & Pelaporan Desa' : 'Manajemen & Monitoring Wilayah' }}
                </h4>
                <p class="text-tertiary small mb-0 font-medium">Navigasi kontrol dan entri data sektoral</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            {{-- Unified Menu Logic --}}
            @php
                $menus = [
                    ['key' => 'A', 'icon' => 'fa-hand-holding-dollar', 'route' => $isOperator ? 'desa.ekbang.dana-desa.index' : 'kecamatan.ekbang.dana-desa.index', 'title' => $isOperator ? 'Lapor Dana Desa' : 'Monitoring Dana Desa', 'desc' => $isOperator ? 'Input data pencairan & penyaluran BLT' : 'Pantau penyaluran Dana Desa wilayah'],
                    ['key' => 'B', 'icon' => 'fa-person-digging', 'route' => $isOperator ? 'desa.ekbang.fisik.index' : 'kecamatan.ekbang.fisik.index', 'title' => $isOperator ? 'Input Progres Fisik' : 'Monitoring Fisik', 'desc' => $isOperator ? 'Update persentase & foto proyek fisik' : 'Pantau capaian infrastruktur desa'],
                    ['key' => 'C', 'icon' => 'fa-chart-line', 'route' => $isOperator ? 'desa.ekbang.realisasi.index' : 'kecamatan.ekbang.realisasi.index', 'title' => $isOperator ? 'Laporan Realisasi' : 'Evaluasi Realisasi', 'desc' => $isOperator ? 'Submit capaian belanja APBDes' : 'Analisa penyerapan anggaran desa'],
                    ['key' => 'D', 'icon' => 'fa-file-shield', 'route' => $isOperator ? 'desa.ekbang.kepatuhan.index' : 'kecamatan.ekbang.kepatuhan.index', 'title' => $isOperator ? 'Kelengkapan Dokumen' : 'Verifikasi Kepatuhan', 'desc' => $isOperator ? 'Upload Perdes & dokumen pelaporan' : 'Audit kelengkapan regulasi desa'],
                    ['key' => 'E', 'icon' => 'fa-magnifying-glass-chart', 'route' => $isOperator ? 'desa.ekbang.audit.index' : 'kecamatan.ekbang.audit.index', 'title' => $isOperator ? 'Tindak Lanjut Temuan' : 'Pengawasan & Audit', 'desc' => $isOperator ? 'Jawab temuan & bukti perbaikan' : 'Kelola temuan audit lintas desa'],
                ];
            @endphp

            @foreach($menus as $menu)
                <div class="col-xl-2 col-lg-4 col-md-6" style="flex: 0 0 20%; max-width: 20%;">
                    <a href="{{ route($menu['route']) }}" class="text-decoration-none group">
                        <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                            style="background: white;">
                            <div
                                class="position-absolute top-0 end-0 w-24 h-24 bg-emerald-900 opacity-0 group-hover:opacity-5 blur-2xl -translate-y-1/2 translate-x-1/2 transition-opacity">
                            </div>

                            <div class="card-body p-0 d-flex flex-column h-100 text-center">
                                <div class="bg-slate-50 text-slate-400 rounded-pill px-3 py-1 font-black uppercase tracking-widest group-hover:bg-emerald-950 group-hover:text-emerald-100 transition-colors mx-auto mb-4"
                                    style="font-size: 9px; width: fit-content;">
                                    Modul {{ $menu['key'] }}
                                </div>

                                <div class="icon-box bg-slate-100 text-slate-900 rounded-4xl d-flex align-items-center justify-content-center shadow-sm group-hover:bg-emerald-950 group-hover:text-white transition-all duration-500 mx-auto mb-4"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas {{ $menu['icon'] }} fs-5"></i>
                                </div>

                                <h6 class="fw-black text-primary-900 mb-2 group-hover:text-emerald-700 transition-colors">
                                    {{ $menu['title'] }}
                                </h6>
                                <p class="text-tertiary small mb-0 leading-tight font-medium">{{ $menu['desc'] }}</p>
                            </div>
                        </div>
                    </a>
            @endforeach
            </div>
        </div>

        {{-- Status Table Section --}}
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-black text-primary-900 mb-1 tracking-tight">
                    {{ $isOperator ? 'Aktivitas Pelaporan Terakhir' : 'Laporan Masuk Wilayah' }}
                </h4>
                <p class="text-tertiary small mb-0 font-medium">Monitoring sinkronisasi data ekonomi-pembangunan</p>
            </div>
        </div>

        <div class="card border-0 glass-card-v2 shadow-premium rounded-5 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50/50">
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="ps-5 py-4">Sektor & Aspek</th>
                            <th class="py-4">Status Pengawasan</th>
                            <th class="text-end pe-5 py-4">Aksi Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $recent)
                            <tr class="group transition-colors">
                                <td class="ps-5 py-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 d-flex align-items-center justify-content-center font-black shadow-sm group-hover:bg-emerald-950 group-hover:text-white transition-all">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <div
                                                class="fw-black text-primary-900 mb-0.5 group-hover:text-emerald-700 transition-colors">
                                                {{ $recent->menu->nama_menu }}</div>
                                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter">
                                                {{ $recent->aspek->nama_aspek }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    @php
                                        $sStyle = [
                                            'draft' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            'submitted' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'returned' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'reviewed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                            'approved' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        ][$recent->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                    @endphp
                                    <span
                                        class="badge border px-3 py-2 rounded-xl text-[10px] font-black tracking-widest {{ $sStyle }}">
                                        {{ strtoupper($recent->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-5 py-4">
                                    <a href="{{ $isOperator ? route('desa.submissions.edit', $recent->id) : route('kecamatan.verifikasi.show', $recent->uuid) }}"
                                        class="btn btn-sm bg-emerald-950 hover:bg-emerald-900 text-white border-0 px-4 py-2 rounded-xl font-bold text-[10px] uppercase shadow-lg shadow-emerald-900/10 active:scale-95 transition-all">
                                        Buka Modul <i class="fas fa-chevron-right ms-2 scale-75"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10">
                                    <div class="text-emerald-100 mb-4">
                                        <i class="fas fa-box-open fa-4x opacity-20"></i>
                                    </div>
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Arsip Laporan
                                        Masih Kosong</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
            from { opacity: 0; transform: translateY(30px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .glass-card-v2 {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 1) !important;
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

        .rounded-4xl { border-radius: 1.5rem; }
        .rounded-5 { border-radius: 2rem; }
        .fw-black { font-weight: 900 !important; }

        @media (max-width: 1200px) {
            .col-xl-2 {
                flex: 0 0 33.333% !important;
                max-width: 33.333% !important;
            }
        }

        @media (max-width: 768px) {
            .col-xl-2 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
        }
    </style>
@endpush