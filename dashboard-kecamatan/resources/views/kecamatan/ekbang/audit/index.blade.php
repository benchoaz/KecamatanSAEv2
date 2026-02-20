@extends($isOperator ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', $isOperator ? 'Tindak Lanjut Temuan Audit' : 'Pengawasan & Akuntabilitas Audit')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ $isOperator ? route('ekbang.index') : route('kecamatan.ekbang.index') }}" class="text-primary"><i
                    class="fas fa-arrow-left"></i> Kembali ke Menu Ekbang</a>
        </div>
        <div class="header-title d-flex justify-content-between align-items-center w-100">
            <div>
                <h1>{{ $isOperator ? 'Tindak Lanjut Hasil Pemeriksaan (TLHP)' : 'Monitoring Akuntabilitas & Audit' }}</h1>
                <p class="text-muted">
                    @if($desa_id)
                        {{ $isOperator ? 'Manajemen jawaban & perbaikan atas temuan audit desa' : 'Pemantauan penyelesaian temuan audit di wilayah' }}
                    @else
                        Pilih Desa untuk Melihat Status Accountabilitas & Audit
                    @endif
                </p>
            </div>
            @if($desa_id && $isOperator)
                @can('submission.create')
                    <a href="{{ route('submissions.create', ['menu_id' => 2]) }}" class="btn btn-primary">
                        <i class="fas fa-reply me-2"></i> Kirim Jawaban Perbaikan
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
                                    <span class="badge bg-primary-soft text-primary px-3 py-2" style="font-size: 0.85rem;">0
                                        Proyek</span>
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
        <!-- Audit Status Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-white border-gray-200 shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="audit-icon bg-teal-50 text-teal-600">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tuntas / Selesai</div>
                            <div class="h3 text-gray-900 fw-bold mb-0">12</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-gray-200 shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="audit-icon bg-amber-50 text-amber-600">
                            <i class="fas fa-hourglass-start"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Dalam Proses</div>
                            <div class="h3 text-gray-900 fw-bold mb-0">3</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-gray-200 shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="audit-icon bg-red-50 text-red-600">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Belum Ditindaklanjuti</div>
                            <div class="h3 text-gray-900 fw-bold mb-0">1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Findings Table -->
        <div class="card bg-white border-gray-200 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 text-gray-800 fw-bold">Daftar Temuan Pemeriksaan</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Oleh</th>
                                <th>Uraian Temuan / Rekomendasi</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditLogs as $a)
                                <!-- Dynamic data will be loaded here -->
                            @empty
                                <tr class="border-secondary border-opacity-25">
                                    <td class="ps-4">1</td>
                                    <td>Irda (Inspektorat)</td>
                                    <td>
                                        <div class="fw-bold text-gray-800 small">Kelebihan Bayar Honorarium</div>
                                        <div class="text-muted extra-small">Rekomendasi: Penyetoran kembali ke Kas Desa</div>
                                    </td>
                                    <td><span class="badge bg-danger">Mendesak</span></td>
                                    <td><span class="badge bg-warning-soft text-warning">Proses</span></td>
                                    <td class="text-end pe-4"><button class="btn btn-sm btn-icon"><i
                                                class="fas fa-reply"></i></button></td>
                                </tr>
                                <tr class="border-secondary border-opacity-25">
                                    <td class="ps-4">2</td>
                                    <td>Kecamatan</td>
                                    <td>
                                        <div class="fw-bold text-gray-800 small">Dokumen LPJ BLT Tahap II Belum Tanda Tangan</div>
                                        <div class="text-muted extra-small">Rekomendasi: Melengkapi spesimen tanda tangan KPM</div>
                                    </td>
                                    <td><span class="badge bg-info">Normal</span></td>
                                    <td><span class="badge bg-success-soft text-success">Selesai</span></td>
                                    <td class="text-end pe-4"><button class="btn btn-sm btn-icon"><i
                                                class="fas fa-check-circle"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .audit-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-success-soft {
            background: rgba(16, 185, 129, 0.1);
        }

        .bg-warning-soft {
            background: rgba(245, 158, 11, 0.1);
        }

        .extra-small {
            font-size: 0.75rem;
        }

        .bg-red-50 {
            background-color: #fef2f2;
        }

        .text-red-600 {
            color: #dc2626;
        }

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