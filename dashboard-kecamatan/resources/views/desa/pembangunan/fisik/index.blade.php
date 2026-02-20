@extends('layouts.desa')

@section('title', 'Laporan Pembangunan Fisik')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item active">Fisik</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-slate-800">Pembangunan Fisik Desa</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('desa.pembangunan.fisik.create') }}" class="btn btn-emerald text-white rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>Tambah Laporan
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom">
                            <tr>
                                <th class="ps-4 text-slate-500 small fw-bold text-uppercase py-3">Nama Kegiatan / Lokasi
                                </th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Tahun</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Progres</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Anggaran</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Status</th>
                                <th class="pe-4 text-end text-slate-500 small fw-bold text-uppercase py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembangunan as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-slate-800">{{ $item->nama_kegiatan }}</div>
                                        <div class="small text-slate-500"><i
                                                class="fas fa-map-marker-alt me-1"></i>{{ $item->lokasi }}</div>
                                    </td>
                                    <td>{{ $item->tahun_anggaran }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                                @php
                                                    $percentage = (int) Str::replace('%', '', $item->progres_fisik);
                                                @endphp
                                                <div class="progress-bar bg-emerald-500" role="progressbar"
                                                    style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="small fw-bold text-slate-700">{{ $item->progres_fisik }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-slate-500">Pagu: Rp
                                            {{ number_format($item->pagu_anggaran, 0, ',', '.') }}</div>
                                        <div class="fw-medium text-slate-800">Realisasi: Rp
                                            {{ number_format($item->realisasi_anggaran, 0, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'Draft' => 'bg-slate-100 text-slate-600',
                                                'Dikirim' => 'bg-blue-100 text-blue-600',
                                                'Dikembalikan' => 'bg-amber-100 text-amber-600',
                                                'Dicatat' => 'bg-emerald-100 text-emerald-600',
                                            ][$item->status_laporan] ?? 'bg-slate-100 text-slate-600';
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 fw-normal">
                                            {{ $item->status_laporan }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('desa.pembangunan.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-slate rounded-pill me-2">Detail</a>
                                            @if($item->status_laporan === 'Draft' || $item->status_laporan === 'Dikembalikan')
                                                <form action="{{ route('desa.pembangunan.submit', $item->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-emerald text-white rounded-pill px-3">Kirim</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-slate-400 mb-3">
                                            <i class="fas fa-folder-open fa-3x"></i>
                                        </div>
                                        <p class="text-slate-500">Belum ada data laporan pembangunan fisik.</p>
                                        <a href="{{ route('desa.pembangunan.fisik.create') }}"
                                            class="btn btn-sm btn-emerald text-white rounded-pill">Tambah Sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-emerald-500 {
            background-color: #10b981;
        }

        .text-emerald-600 {
            color: #059669;
        }

        .btn-emerald {
            background-color: #10b981;
            color: white !important;
        }

        .btn-emerald:hover {
            background-color: #059669;
        }

        .btn-outline-slate {
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-outline-slate:hover {
            background-color: #f8fafc;
            color: #334155;
        }
    </style>
@endsection