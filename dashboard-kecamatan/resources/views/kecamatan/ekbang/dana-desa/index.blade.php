@extends($isOperator ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', $isOperator ? 'Input & Monitoring Dana Desa' : 'Monitoring Wilayah Dana Desa')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ $isOperator ? route('ekbang.index') : route('kecamatan.ekbang.index') }}" class="text-primary"><i
                    class="fas fa-arrow-left"></i> Kembali ke Menu
                Ekbang</a>
        </div>
        <div class="header-title d-flex justify-content-between align-items-center w-100">
            <div>
                <h1>{{ $isOperator ? 'Input & Progres Dana Desa' : 'Monitoring Dana Desa' }}</h1>
                <p class="text-muted">
                    @if($desa_id)
                        {{ $isOperator ? 'Pemantauan penyaluran Dana Desa & BLT-DD Anda' : 'Pemantauan penyaluran Dana Desa & BLT-DD' }}
                    @else
                        Pilih Desa untuk Melihat Status Dana Desa
                    @endif
                </p>
            </div>
            @if($desa_id && $isOperator)
                @can('submission.create')
                    <a href="{{ route('submissions.create', ['menu_id' => 2]) }}" class="btn btn-primary">
                        <i class="fas fa-file-export me-2"></i> Input Laporan Baru
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
                            <th class="text-center">Laporan Dana Desa Masuk</th>
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
        <!-- Summary Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-white border-gray-200 shadow-sm p-3">
                    <div class="text-muted small">Alokasi Dasar</div>
                    <div class="h4 text-gray-900 fw-bold mb-0">Rp 600.000.000</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-white border-gray-200 shadow-sm p-3">
                    <div class="text-muted small">Status Tahap I</div>
                    <div class="h4 text-success fw-bold mb-0"><i class="fas fa-check-circle me-1"></i> Cair</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-white border-gray-200 shadow-sm p-3">
                    <div class="text-muted small">Status Tahap II</div>
                    <div class="h4 text-warning fw-bold mb-0"><i class="fas fa-hourglass-half me-1"></i> Diproses</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-white border-gray-200 shadow-sm p-3">
                    <div class="text-muted small">KPM BLT</div>
                    <div class="h4 text-info fw-bold mb-0">25 KPM</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card bg-white border-gray-200 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-gray-800">Riwayat Laporan Monev Dana Desa</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Tanggal Laporan</th>
                                <th>Tahun/Periode</th>
                                <th>Status Verifikasi</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danaDesa as $d)
                                <tr class="align-middle">
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td>{{ $d->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $d->tahun }} / {{ ucfirst($d->periode) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $d->status === 'approved' ? 'success' : ($d->status === 'returned' ? 'danger' : 'info') }}">
                                            {{ strtoupper($d->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('submissions.edit', $d->id) }}" class="btn btn-sm btn-icon"><i
                                                class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">Belum ada rincian laporan yang diinput.</td>
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