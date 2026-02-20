@extends('layouts.kecamatan')

@section('title', 'Laporan Kepatuhan Administrasi Desa')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('kecamatan.laporan.index') }}"
                class="btn btn-xs btn-light rounded-pill px-3 text-secondary text-decoration-none border shadow-sm small">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Rekap Umum
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-900 text-slate-900 mb-1">Kepatuhan Administrasi Desa</h2>
                <p class="text-slate-500 mb-0">Status ketersediaan dokumen perencanaan dan pelaporan wajib desa tahun
                    {{ $year }}.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light border rounded-pill px-4 shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white p-4 border-bottom">
            <h5 class="fw-bold text-slate-900 mb-0">Matriks Kepatuhan Dokumen Digital</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="ps-4 py-3 small fw-bold text-start">NAMA DESA</th>
                            <th class="py-3 small fw-bold">RPJMDES</th>
                            <th class="py-3 small fw-bold">RKPDES {{ $year }}</th>
                            <th class="py-3 small fw-bold">LKPJ {{ $year - 1 }}</th>
                            <th class="py-3 small fw-bold">LPPD {{ $year - 1 }}</th>
                            <th class="py-3 small fw-bold">STATUS AKUMULATIF</th>
                            <th class="pe-4 py-3 small fw-bold text-start">CATATAN ADMINISTRATIF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desas as $desa)
                            <tr>
                                <td class="ps-4 text-start">
                                    <div class="fw-bold text-slate-800">{{ $desa->nama_desa }}</div>
                                </td>
                                <td>
                                    @if($desa->rpjmdes_exists)
                                        <i class="fas fa-circle-check text-success fs-5"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-slate-200 fs-5"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($desa->rkpdes_exists)
                                        <i class="fas fa-circle-check text-success fs-5"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-slate-200 fs-5"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($desa->lkpj_exists)
                                        <i class="fas fa-circle-check text-success fs-5"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-slate-200 fs-5"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($desa->lppd_exists)
                                        <i class="fas fa-circle-check text-success fs-5"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-slate-200 fs-5"></i>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $count = ($desa->rpjmdes_exists ? 1 : 0) + ($desa->rkpdes_exists ? 1 : 0) +
                                            ($desa->lkpj_exists ? 1 : 0) + ($desa->lppd_exists ? 1 : 0);
                                    @endphp
                                    @if($count == 4)
                                        <span class="badge bg-emerald-100 text-emerald-700 rounded-pill px-3">Lengkap</span>
                                    @elseif($count > 0)
                                        <span class="badge bg-amber-100 text-amber-700 rounded-pill px-3">Belum Lengkap</span>
                                    @else
                                        <span class="badge bg-rose-100 text-rose-700 rounded-pill px-3">Monitoring</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-start">
                                    @if($count < 4)
                                        <p class="small text-slate-500 mb-0">Segera lakukan pembinaan untuk pemenuhan dokumen.</p>
                                    @else
                                        <p class="small text-slate-400 mb-0">-</p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Official Footer Content -->
    <div class="official-footer mt-5 p-4 rounded-4 border bg-light">
        <div class="row align-items-center">
            <div class="col-md-9">
                <p class="mb-0 text-slate-600 small italic">
                    <strong>Tujuan Laporan:</strong> Digunakan untuk monitoring kepatuhan administrasi desa sesuai amanat UU
                    Desa.
                    Status lengkap menunjukkan dokumen telah diunggah ke portal kecamatan dan siap diverifikasi secara
                    administratif.
                </p>
            </div>
            <div class="col-md-3 text-end">
                <span class="text-slate-400 small">Data per: {{ now()->format('d F Y') }}</span>
            </div>
        </div>
    </div>
@endsection