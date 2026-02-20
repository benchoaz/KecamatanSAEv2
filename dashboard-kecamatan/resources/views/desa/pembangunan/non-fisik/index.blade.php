@extends('layouts.desa')

@section('title', 'Laporan Kegiatan Non-Fisik')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item active">Non-Fisik</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-slate-800">Kegiatan Non-Fisik</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('desa.pembangunan.non-fisik.create') }}"
                    class="btn btn-amber text-white rounded-pill px-4">
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
                                <th class="ps-4 text-slate-500 small fw-bold text-uppercase py-3">Nama Kegiatan</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Tahun</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Bidang</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Anggaran</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Status Laporan</th>
                                <th class="pe-4 text-end text-slate-500 small fw-bold text-uppercase py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembangunan as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-slate-800">{{ $item->nama_kegiatan }}</div>
                                        <div class="small text-slate-500">{{ $item->lokasi }}</div>
                                    </td>
                                    <td>{{ $item->tahun_anggaran }}</td>
                                    <td><span class="small text-slate-600">{{ Str::limit($item->bidang_apbdes, 30) }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-slate-800">Rp
                                            {{ number_format($item->pagu_anggaran, 0, ',', '.') }}</div>
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
                                                        class="btn btn-sm btn-amber text-white rounded-pill px-3">Kirim</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-slate-400 mb-3">
                                            <i class="fas fa-users-gear fa-3x"></i>
                                        </div>
                                        <p class="text-slate-500">Belum ada data kegiatan non-fisik.</p>
                                        <a href="{{ route('desa.pembangunan.non-fisik.create') }}"
                                            class="btn btn-sm btn-amber text-white rounded-pill">Tambah Sekarang</a>
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
        .btn-amber {
            background-color: #f59e0b;
            color: white !important;
        }

        .btn-amber:hover {
            background-color: #d97706;
        }

        .btn-outline-slate {
            border-color: #cbd5e1;
            color: #64748b;
        }
    </style>
@endsection