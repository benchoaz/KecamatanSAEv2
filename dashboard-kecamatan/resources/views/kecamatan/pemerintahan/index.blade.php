@extends(auth()->user()->desa_id ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', 'Menu Pemerintahan')

@section('content')
    <div class="dashboard container-fluid px-0">
        <!-- Premium Section Header -->
        <div class="welcome-banner p-5 rounded-5 mb-5 position-relative overflow-hidden shadow-2xl animate-entrance"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255,255,255,0.05);">

            <div class="position-absolute top-0 right-0 w-100 h-100 opacity-10 pointer-events-none">
                <div
                    class="position-absolute top-0 end-0 translate-middle w-50 h-100 bg-primary rounded-circle blur-3xl opacity-20">
                </div>
            </div>

            <div class="position-relative z-2">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span
                        class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-1.5 rounded-pill small tracking-widest text-uppercase fw-bold">
                        <i class="fas fa-building-columns me-1"></i> Seksi Pemerintahan
                    </span>
                    <div class="h-px bg-white bg-opacity-10 flex-grow-1"></div>
                </div>

                <h1 class="display-5 fw-black text-white mb-2 tracking-tight">Tata Kelola Administrasi</h1>
                <p class="text-slate-400 fs-5 mb-0 font-medium max-w-2xl">
                    Pusat monitoring buku induk, personil, dan kepatuhan dokumen digital sewilayah
                    {{ appProfile()->region_level }} {{ appProfile()->region_name }}.
                </p>
            </div>

            <div class="position-absolute end-0 bottom-0 opacity-5 mb-n5 me-n4 z-1">
                <i class="fas fa-shield-halved fa-12x text-white"></i>
            </div>
        </div>
    </div>

    {{-- Grid Modul --}}
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-black text-primary-900 mb-1 tracking-tight">Modul Administrasi Sektoral (A-H)</h4>
            <p class="text-tertiary small mb-0 font-medium">Pilih modul spesifik untuk pengelolaan data mendalam</p>
        </div>
        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
            <i class="fas fa-grid-2"></i>
        </div>
    </div>

    <div class="row g-4 mb-5">
        @foreach($pemerintahanMenus as $key => $menu)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}" class="text-decoration-none group">
                    <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden domain-card transition-all"
                        style="background: white;">
                        <div
                            class="position-absolute top-0 end-0 w-24 h-24 bg-slate-900 opacity-0 group-hover:opacity-5 blur-2xl -translate-y-1/2 translate-x-1/2 transition-opacity">
                        </div>

                        <div class="card-body p-0 d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="icon-box bg-slate-100 text-slate-900 rounded-4xl d-flex align-items-center justify-content-center shadow-sm group-hover:bg-slate-900 group-hover:text-white transition-all duration-500"
                                    style="width: 55px; height: 55px;">
                                    <i class="fas {{ $menu['icon'] }} fs-5"></i>
                                </div>
                                <div class="bg-slate-50 text-slate-400 rounded-pill px-3 py-1 font-black uppercase tracking-widest group-hover:bg-slate-900 group-hover:text-slate-100 transition-colors"
                                    style="font-size: 9px;">
                                    {{ $key }}
                                </div>
                            </div>

                            <h5 class="fw-black text-primary-900 mb-2 group-hover:text-primary-600 transition-colors">
                                {{ $menu['title'] }}
                            </h5>
                            <p class="text-tertiary small mb-5 leading-relaxed font-medium">{{ $menu['desc'] }}</p>

                            <div
                                class="mt-auto pt-4 border-t border-slate-50 d-flex align-items-center justify-content-between">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Akses Modul</span>
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-50 text-slate-900 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-all scale-90 group-hover:scale-100">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

        <!-- Export Audit Package Card -->
        <div class="col-xl-3 col-lg-4 col-md-6">
            <a href="{{ auth()->user()->desa_id ? route('desa.pemerintahan.export') : route('kecamatan.pemerintahan.export') }}"
                class="text-decoration-none group">
                <div class="card border-0 shadow-soft rounded-5 p-4 h-100 position-relative overflow-hidden transition-all"
                    style="background: #fffbeb; border: 2px dashed #f59e0b !important;">
                    <div
                        class="card-body p-0 d-flex flex-column align-items-center justify-content-center text-center py-4">
                        <div class="icon-box bg-amber-100 text-amber-600 rounded-full d-flex align-items-center justify-content-center shadow-sm group-hover:scale-110 transition-transform mb-4"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-box-archive fa-2x"></i>
                        </div>
                        <h5 class="fw-black text-amber-900 mb-2">Paket Audit Desa</h5>
                        <p class="text-amber-700 small mb-0 font-medium">Download arsip SK & Dokumen Desa (PDF ZIP)</p>
                    </div>
                    <div
                        class="mt-4 pt-3 border-t border-amber-200/50 w-100 d-flex align-items-center justify-content-center">
                        <span class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em]">Unduh Arsip Lengkap
                            <i class="fas fa-download ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Status Table Section --}}
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-black text-primary-900 mb-1 tracking-tight">Status Laporan Terakhir</h4>
            <p class="text-tertiary small mb-0 font-medium">Monitoring real-time aktivitas penginputan data desa</p>
        </div>
    </div>

    <div class="card border-0 glass-card-v2 shadow-premium rounded-5 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-slate-50/50">
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                        <th class="ps-5 py-4">Aspek Pemerintahan</th>
                        <th class="py-4">Status Verifikasi</th>
                        <th class="text-end pe-5 py-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubmissions as $recent)
                            <tr class="group transition-colors">
                                <td class="ps-5 py-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 d-flex align-items-center justify-content-center font-black shadow-sm group-hover:bg-primary-900 group-hover:text-white transition-all">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <div
                                                class="fw-black text-primary-900 mb-0.5 group-hover:text-primary-600 transition-colors">
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
                                            'submitted' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'returned' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'reviewed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                            'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        ][$recent->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                    @endphp
                        <span
                                        class="badge border px-3 py-2 rounded-xl text-[10px] font-black tracking-widest {{ $sStyle }}">
                                        {{ strtoupper($recent->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-5 py-4">
                                    <a href="{{ auth()->user()->desa_id ? route('desa.submissions.edit', $recent->id) : route('kecamatan.verifikasi.show', $recent->uuid) }}"
                                        class="btn btn-sm bg-slate-900 hover:bg-slate-800 text-white border-0 px-4 py-2 rounded-xl font-bold text-[10px] uppercase shadow-lg shadow-slate-900/10 active:scale-95 transition-all">
                                        Periksa <i class="fas fa-arrow-right ms-2 scale-75"></i>
                                    </a>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-10">
                                <div class="text-slate-200 mb-4">
                                    <i class="fas fa-clipboard-question fa-4x opacity-20"></i>
                                </div>
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Belum Ada Aktivitas
                                    Laporan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5 p-5 rounded-5 border border-primary-100 bg-primary-50/30 flex items-center gap-5">
        <div class="w-12 h-12 bg-primary text-white rounded-2xl flex items-center justify-center shrink-0 shadow-lg">
            <i class="fas fa-info-circle"></i>
        </div>
        <div>
            <h6 class="fw-black text-primary-900 mb-1 tracking-tight">Informasi Tata Kelola</h6>
            <p class="mb-0 text-slate-600 small font-medium leading-relaxed">
                Seksi Pemerintahan melakukan monitoring berkala untuk memastikan kepatuhan administrasi desa. Gunakan arsip
                digital berformat PDF untuk redundansi data optimal.
            </p>
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
    </style>
@endpush