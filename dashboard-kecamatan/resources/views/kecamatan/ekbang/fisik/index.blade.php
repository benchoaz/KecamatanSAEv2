@extends($isOperator ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', $isOperator ? 'Input Progres Fisik' : 'Monitoring Fisik Pembangunan')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ $isOperator ? route('ekbang.index') : route('kecamatan.ekbang.index') }}" class="text-primary"><i
                    class="fas fa-arrow-left"></i> Kembali ke Menu Ekbang</a>
        </div>
        <div class="header-title d-flex justify-content-between align-items-center w-100">
            <div>
                <h1>{{ $isOperator ? 'Input Progres Fisik & Infrastruktur' : 'Monitoring Fisik Pembangunan' }}</h1>
                <p class="text-muted">
                    @if($desa_id)
                        {{ $isOperator ? 'Update capaian pelaksanaan proyek fisik desa Anda' : 'Pemantauan capaian realisasi fisik pembangunan' }}
                    @else
                        Pilih Desa untuk Melihat Capaian Fisik & Infrastruktur
                    @endif
                </p>
            </div>
            @if($desa_id && $isOperator)
                @can('submission.create')
                    <a href="{{ route('submissions.create', ['menu_id' => 2]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Entry Proyek Baru
                    </a>
                @endcan
            @endif
        </div>
    </div>

    @if(!$desa_id)
        <div class="card bg-white border-gray-200 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Nama Desa</th>
                            <th class="text-center">Proyek Fisik Terdaftar</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desas as $index => $desa)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-slate-700"> Desa {{ $desa->nama_desa }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-soft text-primary px-3 py-2"
                                        style="font-size: 0.85rem;">{{ $desa->submissions_count }} Proyek</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Filter & Legend -->
        <div class="card bg-white border-gray-200 shadow-sm mb-4">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3">
                    <span class="badge bg-light text-muted border">Total: 0 Proyek</span>
                    <span class="badge bg-teal-50 text-teal-600 border border-teal-100">Selesai: 0</span>
                    <span class="badge bg-amber-50 text-amber-600 border border-amber-100">Dalam Pengerjaan: 0</span>
                </div>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i> Data diupdate secara real-time dari laporan bulanan desa.
                </div>
            </div>
        </div>

        <!-- Project Grid -->
        <div class="row g-4">
            @forelse($projects as $p)
                <!-- Project cards will go here when data exists -->
            @empty
                <div class="col-12 text-center py-5">
                    <div class="card bg-white border-gray-200 shadow-sm py-5">
                        <i class="fas fa-person-digging fa-3x mb-3 text-gray-300"></i>
                        <h5 class="text-gray-400">Belum ada data proyek fisik.</h5>
                        <p class="text-muted">Silakan masukkan laporan pembangunan melalui menu "Tambah Proyek".</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Placeholder for Demo table if no projects -->
        @if(count($projects) == 0)
            <div class="mt-5">
                <h6 class="text-gray-600 mb-3 text-uppercase small letter-spacing-1 fw-bold">Contoh Monitoring Proyek (Simulasi)
                </h6>
                <div class="card bg-white border-gray-200 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="small text-muted border-secondary">
                                    <th class="ps-4">Nama Proyek</th>
                                    <th>Anggaran</th>
                                    <th>Progres Fisik</th>
                                    <th>Status Administrasi</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="align-middle">
                                    <td class="ps-4">
                                        <div class="fw-bold text-info">Pembangunan Jalan Lingkungan Dusun A</div>
                                        <div class="small text-muted">Volume: 200m x 3m</div>
                                    </td>
                                    <td>Rp 150.000.000</td>
                                    <td style="width: 200px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; background: #334155;">
                                                <div class="progress-bar bg-success" style="width: 75%;"></div>
                                            </div>
                                            <span class="small fw-bold">75%</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary text-uppercase">Pengerjaan</span></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-icon"><i class="fas fa-images"></i></button>
                                        <button class="btn btn-sm btn-icon"><i class="fas fa-arrow-right"></i></button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <td class="ps-4">
                                        <div class="fw-bold text-info">Rehabilitasi Posyandu Mawar</div>
                                        <div class="small text-muted">Aktivitas: Penggantian Atap & Keramik</div>
                                    </td>
                                    <td>Rp 45.000.000</td>
                                    <td style="width: 200px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; background: #334155;">
                                                <div class="progress-bar bg-success" style="width: 100%;"></div>
                                            </div>
                                            <span class="small fw-bold">100%</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success text-uppercase">Selesai</span></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-icon"><i class="fas fa-images"></i></button>
                                        <button class="btn btn-sm btn-icon"><i class="fas fa-arrow-right"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .btn-teal {
            background-color: #14b8a6;
            color: white;
        }

        .btn-teal:hover {
            background-color: #0d9488;
            color: white;
        }
    </style>
@endpush