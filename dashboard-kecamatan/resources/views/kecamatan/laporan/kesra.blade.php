@extends('layouts.kecamatan')

@section('title', 'Laporan Monitoring Kesra')

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
                <h2 class="fw-900 text-slate-900 mb-1">Monitoring Kesejahteraan Rakyat</h2>
                <p class="text-slate-500 mb-0">Rekapitulasi pelaksanaan program sosial dan bantuan kemasyarakatan desa.</p>
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
            <h5 class="fw-bold text-slate-900 mb-0">Status Pelaksanaan Program Sosial</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="ps-4 py-3 small fw-bold">DESA</th>
                            <th class="py-3 small fw-bold">PROGRAM SOSIAL</th>
                            <th class="py-3 small fw-bold text-center">JUMLAH KEGIATAN</th>
                            <th class="py-3 small fw-bold text-center">STATUS PELAKSANAAN</th>
                            <th class="pe-4 py-3 small fw-bold">CATATAN PEMBINAAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-slate-800">{{ $report->desa->nama_desa }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-slate-700">{{ $report->aspek->nama_aspek }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-slate-700">1 Kegiatan</span>
                                </td>
                                <td class="text-center">
                                    @if($report->status == 'approved')
                                        <span class="badge bg-emerald-100 text-emerald-700 rounded-pill px-3">Terlaksana</span>
                                    @else
                                        <span class="badge bg-info-100 text-info-700 rounded-pill px-3">Dalam Monitoring</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <p class="small text-slate-500 mb-0">Data penerima manfaat telah divalidasi oleh desa secara
                                        administratif.</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-slate-400 small">
                                    Belum ada aktivitas program sosial yang dilaporkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="official-footer mt-5 p-4 rounded-4 border bg-light">
        <p class="mb-0 text-slate-600 small italic text-center">
            <strong>Keterangan:</strong> Laporan ini bersifat agregat dan non-individual untuk melindungi privasi data KPM
            (Keluarga Penerima Manfaat).
            Fokus monitoring adalah pada ketepatan administrasi penyaluran dan pelaksanaan kegiatan.
        </p>
    </div>
@endsection