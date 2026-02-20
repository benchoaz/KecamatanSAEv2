@extends($isOperator ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', $isOperator ? 'Kelengkapan Dokumen Regulasi' : 'Verifikasi Kepatuhan Desa')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ $isOperator ? route('ekbang.index') : route('kecamatan.ekbang.index') }}" class="text-primary"><i
                    class="fas fa-arrow-left"></i> Kembali ke Menu
                Ekbang</a>
        </div>
        <div class="header-title d-flex justify-content-between align-items-center w-100">
            <div>
                <h1>
                    {{ $isOperator ? 'Kelengkapan Dokumen & Regulasi' : 'Monitoring Kepatuhan Regulasi' }}
                </h1>
                <p class="text-muted">
                    @if($desa_id)
                        {{ $isOperator ? 'Pastikan dokumen administrasi & laporan desa Anda lengkap' : 'Audit ketepatan waktu & kelengkapan regulasi' }}
                    @else
                        Pilih Desa untuk Melihat Status Kepatuhan Regulasi
                    @endif
                </p>
            </div>
            @if($desa_id && $isOperator)
                @can('submission.create')
                    <a href="{{ route('submissions.create', ['menu_id' => 2]) }}" class="btn btn-primary">
                        <i class="fas fa-file-shield me-2"></i> Upload Dokumen Baru
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
                            <th class="text-center">Laporan Kepatuhan</th>
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
                                        style="font-size: 0.85rem;">{{ $desa->submissions_count }} Laporan</span>
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
        <div class="row g-4 mt-2">
            <!-- Main Compliance List -->
            <div class="col-lg-8">
                <div class="card bg-white border-gray-200 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-gray-800 fw-bold">Checklist Dokumen Wajib Desa</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Batas Waktu</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="align-middle">
                                        <td class="ps-4">1</td>
                                        <td>
                                            <div class="fw-bold">Perdes APBDes {{ date('Y') }}</div>
                                        </td>
                                        <td>31 Des {{ date('Y') - 1 }}</td>
                                        <td><span class="badge bg-success-soft text-success text-uppercase">Tepat Waktu</span>
                                        </td>
                                        <td class="text-end pe-4"><button class="btn btn-sm btn-icon"><i
                                                    class="fas fa-file-pdf"></i></button></td>
                                    </tr>
                                    <tr class="align-middle">
                                        <td class="ps-4">2</td>
                                        <td>
                                            <div class="fw-bold">Perkades Penjabaran APBDes</div>
                                        </td>
                                        <td>Januari {{ date('Y') }}</td>
                                        <td><span class="badge bg-success-soft text-success text-uppercase">Lengkap</span></td>
                                        <td class="text-end pe-4"><button class="btn btn-sm btn-icon"><i
                                                    class="fas fa-file-pdf"></i></button></td>
                                    </tr>
                                    <tr class="align-middle">
                                        <td class="ps-4">3</td>
                                        <td>
                                            <div class="fw-bold">Laporan Semester I (Ekbang)</div>
                                        </td>
                                        <td>Juli {{ date('Y') }}</td>
                                        <td><span class="badge bg-warning-soft text-warning text-uppercase">Tahap
                                                Verifikasi</span></td>
                                        <td class="text-end pe-4"><button class="btn btn-sm btn-icon"><i
                                                    class="fas fa-clock"></i></button></td>
                                    </tr>
                                    <tr class="align-middle">
                                        <td class="ps-4">4</td>
                                        <td>
                                            <div class="fw-bold">Laporan Realisasi DD (Satu Sehat)</div>
                                        </td>
                                        <td>Bulanan</td>
                                        <td><span class="badge bg-danger-soft text-danger text-uppercase">Belum Update</span>
                                        </td>
                                        <td class="text-end pe-4"><button class="btn btn-sm btn-icon"><i
                                                    class="fas fa-plus-circle"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance Integrity Score -->
            <div class="col-lg-4">
                <div class="card bg-white border-gray-200 shadow-sm p-4 h-100 position-relative overflow-hidden">
                    <div class="text-gray-800 fw-bold h5 mb-3">Skor Kepatuhan Desa</div>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="score-circle">75</div>
                        <div>
                            <div class="h3 text-teal-600 fw-bold mb-0">Baik</div>
                            <div class="text-muted small">Update 12 Juli 2024</div>
                        </div>
                    </div>
                    <div class="alert bg-teal-50 border-0 text-teal-700 small">
                        <i class="fas fa-info-circle me-1"></i> Skor berdasarkan ketepatan waktu penyampaian dokumen APBDes dan
                        laporan realisasi.
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .bg-success-soft {
            background: rgba(16, 185, 129, 0.1);
        }

        .bg-warning-soft {
            background: rgba(245, 158, 11, 0.1);
        }

        .bg-danger-soft {
            background: rgba(239, 68, 68, 0.1);
        }

        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #14b8a6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #14b8a6;
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