@extends('layouts.desa')

@section('title', 'Penyaluran BLT Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item active">BLT Desa</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-slate-800">Penyaluran BLT Desa</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('desa.blt.create') }}" class="btn btn-sky text-white rounded-pill px-4">
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
                                <th class="ps-4 text-slate-500 small fw-bold text-uppercase py-3">Tahun / Periode</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Jumlah KPM</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Dana Tersalurkan</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Status Penyaluran</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Laporan</th>
                                <th class="pe-4 text-end text-slate-500 small fw-bold text-uppercase py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blt as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-slate-800">{{ $item->tahun_anggaran }}</div>
                                        <div class="small text-slate-500">Penyaluran Terakhir:
                                            {{ $item->updated_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-slate-800">{{ $item->kpm_terealisasi }} /
                                            {{ $item->jumlah_kpm }} KPM</div>
                                        <div class="progress" style="height: 4px; width: 100px;">
                                            @php $pct = ($item->jumlah_kpm > 0) ? ($item->kpm_terealisasi / $item->jumlah_kpm * 100) : 0; @endphp
                                            <div class="progress-bar bg-sky-500" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-800">Rp
                                            {{ number_format($item->total_dana_tersalurkan, 0, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $color = [
                                                'Tepat Waktu' => 'text-success',
                                                'Bertahap' => 'text-primary',
                                                'Tertunda' => 'text-danger',
                                            ][$item->status_penyaluran] ?? 'text-slate-600';
                                        @endphp
                                        <span class="fw-medium {{ $color }}"><i class="fas fa-circle small me-1"></i>
                                            {{ $item->status_penyaluran }}</span>
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
                                            <a href="{{ route('desa.blt.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-slate rounded-pill me-2">Detail</a>
                                            @if($item->status_laporan === 'Draft' || $item->status_laporan === 'Dikembalikan')
                                                <form action="{{ route('desa.blt.submit', $item->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-sky text-white rounded-pill px-3">Kirim</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-slate-400 mb-3">
                                            <i class="fas fa-hand-holding-dollar fa-3x"></i>
                                        </div>
                                        <p class="text-slate-500">Belum ada data laporan penyaluran BLT Desa.</p>
                                        <a href="{{ route('desa.blt.create') }}"
                                            class="btn btn-sm btn-sky text-white rounded-pill">Tambah Laporan</a>
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
        .bg-sky-500 {
            background-color: #0ea5e9;
        }

        .btn-sky {
            background-color: #0ea5e9;
            color: white !important;
        }

        .btn-sky:hover {
            background-color: #0284c7;
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