@extends('layouts.public')

@section('title', 'Lowongan Kerja Warga')

@section('content')
    <!-- Hero Section -->
    <section class="position-relative overflow-hidden pt-5 pb-4"
        style="background: linear-gradient(135deg, #16a34a 0%, #059669 100%);">
        <div class="container py-4 position-relative z-index-2">
            <div class="row align-items-center">
                <div class="col-lg-7 text-white">
                    <span
                        class="badge bg-white text-success px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm animate__animated animate__fadeInDown">
                        <i class="fas fa-briefcase me-2"></i>PAPAN LOKER WARGA
                    </span>
                    <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInLeft">
                        Cari Kerja atau Pasang Info Kerja
                    </h1>
                    <p class="lead mb-4 opacity-90 animate__animated animate__fadeInLeft animate__delay-1s">
                        Layanan papan informasi kerja warga Kecamatan {{ appProfile()->kecamatan_name }}.
                        Informal, cepat, dan terpercaya langsung ke warga sekitar.
                    </p>
                    <div class="d-flex flex-wrap gap-3 animate__animated animate__fadeInUp animate__delay-1s">
                        <a href="{{ route('public.loker.create') }}"
                            class="btn btn-white btn-lg rounded-pill px-4 fw-bold shadow-sm text-success hover-lift">
                            <i class="fas fa-plus-circle me-2"></i>Pasang Info Kerja
                        </a>
                        <a href="#daftar-loker" class="btn btn-outline-white btn-lg rounded-pill px-4 fw-bold hover-lift">
                            <i class="fas fa-search me-2"></i>Cari Lowongan
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block animate__animated animate__fadeInRight">
                    <img src="{{ asset('img/illustrations/working.svg') }}"
                        onerror="this.src='https://illustrations.popsy.co/white/work-from-home.svg'" alt="Loker Animation"
                        class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section id="daftar-loker" class="py-5 bg-light">
        <div class="container">
            <div class="card border-0 shadow-sm rounded-4 mb-5 mt-n5 animate__animated animate__fadeInUp">
                <div class="card-body p-4">
                    <form action="{{ route('public.loker.index') }}" method="GET" class="row g-3">
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="q" class="form-control border-start-0 rounded-end-pill py-2"
                                    placeholder="Cari posisi atau jenis kerja..." value="{{ request('q') }}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <select name="category" class="form-select rounded-pill py-2">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select name="desa" class="form-select rounded-pill py-2">
                                <option value="">Semua Desa</option>
                                @foreach($desas as $desa)
                                    <option value="{{ $desa->id }}" {{ request('desa') == $desa->id ? 'selected' : '' }}>
                                        {{ $desa->nama_desa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-success rounded-pill w-100 py-2 fw-bold">
                                Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                @forelse($lokers as $l)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-loker hover-lift">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="badge bg-soft-success text-success px-3 py-2 rounded-pill fs-xs">
                                        <i class="fas fa-tag me-1 small"></i> {{ $l->job_category }}
                                    </span>
                                    @if($l->is_available_today)
                                        <span
                                            class="badge bg-danger px-3 py-2 rounded-pill fs-xs animate__animated animate__pulse animate__infinite">
                                            <i class="fas fa-bolt me-1"></i> Dibutuhkan Hari Ini
                                        </span>
                                    @endif
                                </div>

                                <h5 class="fw-bold text-slate-800 mb-2">{{ $l->title }}</h5>
                                <p class="text-slate-500 mb-3 small line-clamp-2">
                                    {{ $l->description ?: 'Butuh tenaga kerja cepat untuk posisi ' . $l->title . '.' }}
                                </p>

                                <div class="d-flex flex-column gap-2 mb-4">
                                    <div class="d-flex align-items-center text-slate-600 small">
                                        <i class="fas fa-map-marker-alt text-danger me-2" style="width: 16px;"></i>
                                        {{ $l->desa ? $l->desa->nama_desa : ($l->nama_desa_manual ?: 'Sekitar Kecamatan') }}
                                    </div>
                                    <div class="d-flex align-items-center text-slate-600 small">
                                        <i class="fas fa-clock text-primary me-2" style="width: 16px;"></i>
                                        {{ $l->work_time ?: 'Harian' }}
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $l->contact_wa) }}?text=Halo%2C%20saya%20tertarik%20dengan%20info%20loker%20{{ urlencode($l->title) }}%20di%20Aplikasi%20Kecamatan."
                                        target="_blank" class="btn btn-success rounded-pill py-2 fw-bold">
                                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                    </a>
                                </div>
                            </div>
                            <div
                                class="card-footer bg-light border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                                <small class="text-slate-400">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $l->created_at->diffForHumans() }}
                                </small>
                                <a href="tel:{{ $l->contact_wa }}"
                                    class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" title="Telepon Langsung">
                                    <i class="fas fa-phone-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-briefcase fa-4x text-slate-200"></i>
                        </div>
                        <h4 class="fw-bold text-slate-400">Belum ada lowongan</h4>
                        <p class="text-slate-400">Jadilah yang pertama untuk memasang info kerja!</p>
                        <a href="{{ route('public.loker.create') }}" class="btn btn-success rounded-pill px-4 mt-2">
                            <i class="fas fa-plus-circle me-1"></i>Pasang Sekarang
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $lokers->links() }}
            </div>
        </div>
    </section>

    <style>
        .card-loker {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.02) !important;
        }

        .card-loker:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .bg-soft-success {
            background-color: #ecfdf5;
        }

        .text-slate-800 {
            color: #1e293b;
        }

        .text-slate-600 {
            color: #475569;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .text-slate-400 {
            color: #94a3b8;
        }

        .text-slate-200 {
            color: #e2e8f0;
        }

        .fs-xs {
            font-size: 0.75rem;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn-white {
            background-color: #fff;
            color: #16a34a;
        }

        .btn-white:hover {
            background-color: #f8fafc;
            color: #15803d;
        }

        .btn-outline-white {
            border-color: #fff;
            color: #fff;
        }

        .btn-outline-white:hover {
            background-color: #fff;
            color: #16a34a;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection