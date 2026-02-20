@extends($isOperator ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', $isOperator ? 'Laporan Realisasi APBDes' : 'Evaluasi Realisasi APBDes')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ $isOperator ? route('ekbang.index') : route('kecamatan.ekbang.index') }}" class="text-primary"><i
                    class="fas fa-arrow-left"></i> Kembali ke Menu
                Ekbang</a>
        </div>
        <div class="header-title d-flex justify-content-between align-items-center w-100">
            <div>
                <h1>{{ $isOperator ? 'Laporan Realisasi APBDes' : 'Evaluasi Realisasi APBDes' }}</h1>
                <p class="text-muted">
                    @if($desa_id)
                        {{ $isOperator ? 'Submit capaian realisasi pendapatan & belanja desa Anda' : 'Analisa capaian penyerapan anggaran' }}
                    @else
                        Pilih Desa untuk Melihat Capaian Realisasi Anggaran
                    @endif
                </p>
            </div>
            @if($desa_id && $isOperator)
                @can('submission.create')
                    <a href="{{ route('submissions.create', ['menu_id' => 2]) }}" class="btn btn-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Input Realisasi Bulanan
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
                            <th class="text-center">Laporan Realisasi Tersimpan</th>
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
        <!-- Financial Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-gray-200 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small text-uppercase fw-bold">Total Pendapatan</span>
                        <i class="fas fa-download text-success"></i>
                    </div>
                    <div class="h3 text-gray-900 fw-bold">75.5%</div>
                    <div class="progress mt-2" style="height: 4px; background: #f1f5f9;">
                        <div class="progress-bar bg-success" style="width: 75.5%;"></div>
                    </div>
                    <div class="text-muted extra-small mt-2">Target: Rp 1.200.000.000</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-gray-200 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small text-uppercase fw-bold">Total Belanja</span>
                        <i class="fas fa-upload text-danger"></i>
                    </div>
                    <div class="h3 text-gray-900 fw-bold">62.3%</div>
                    <div class="progress mt-2" style="height: 4px; background: #f1f5f9;">
                        <div class="progress-bar bg-danger" style="width: 62.3%;"></div>
                    </div>
                    <div class="text-muted extra-small mt-2">Target: Rp 1.150.000.000</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-gray-200 shadow-sm p-4 h-100 border-start border-4 border-info">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Silpa Berjalan</div>
                    <div class="h3 text-gray-900 fw-bold">Rp 120jt</div>
                    <div class="text-info extra-small mt-1"><i class="fas fa-caret-up me-1"></i> Sisa lebih anggaran</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-gray-200 shadow-sm p-4 h-100 border-start border-4 border-warning">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Status Laporan</div>
                    <div class="h5 text-warning fw-bold mb-0">Update: Juni 2024</div>
                    <div class="text-muted extra-small mt-1">Laporan Semester I Lengkap</div>
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <div class="card bg-white border-gray-200 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 text-gray-800 fw-bold">Data Realisasi Sesuai Dokumen APBDes</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Kode Akun</th>
                                <th>Uraian Anggaran</th>
                                <th>Anggaran (Rp)</th>
                                <th>Realisasi (Rp)</th>
                                <th>%</th>
                                <th class="text-end pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($realisasi as $r)
                                <!-- Dynamic data will be loaded here -->
                            @empty
                                <tr class="border-secondary border-opacity-25">
                                    <td class="ps-4">1</td>
                                    <td>4.1.01</td>
                                    <td>Pendapatan Asli Desa (PAD)</td>
                                    <td>50.000.000</td>
                                    <td>35.000.000</td>
                                    <td>70%</td>
                                    <td class="text-end pe-4"><span class="badge bg-success-soft text-success">Normal</span></td>
                                </tr>
                                <tr class="border-secondary border-opacity-25">
                                    <td class="ps-4">2</td>
                                    <td>4.2.01</td>
                                    <td>Dana Desa (DD)</td>
                                    <td>800.000.000</td>
                                    <td>480.000.000</td>
                                    <td>60%</td>
                                    <td class="text-end pe-4"><span class="badge bg-success-soft text-success">Normal</span></td>
                                </tr>
                                <tr class="border-secondary border-opacity-25">
                                    <td class="ps-4">3</td>
                                    <td>5.2.01</td>
                                    <td>Belanja Pembangunan (Proyek Fisik)</td>
                                    <td>300.000.000</td>
                                    <td>225.000.000</td>
                                    <td>75%</td>
                                    <td class="text-end pe-4"><span class="badge bg-warning-soft text-warning">Dipercepat</span>
                                    </td>
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
        .bg-success-soft {
            background: rgba(16, 185, 129, 0.1);
        }

        .bg-warning-soft {
            background: rgba(245, 158, 11, 0.1);
        }

        .extra-small {
            font-size: 0.75rem;
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