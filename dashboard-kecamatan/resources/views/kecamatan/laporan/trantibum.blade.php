@extends('layouts.kecamatan')

@section('title', 'Laporan Monitoring Trantibum')

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
                <h2 class="fw-900 text-slate-900 mb-1">Monitoring Trantibum & Linmas</h2>
                <p class="text-slate-500 mb-0">Laporan koordinasi stabilitas wilayah dan pembinaan satuan perlindungan
                    masyarakat.</p>
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
            <h5 class="fw-bold text-slate-900 mb-0">Hasil Koordinasi & Monitoring Lapangan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="ps-4 py-3 small fw-bold">DESA</th>
                            <th class="py-3 small fw-bold">ASPEK PEMBINAAN</th>
                            <th class="py-3 small fw-bold">KOORDINASI LINTAS SEKTOR</th>
                            <th class="py-3 small fw-bold text-center">HASIL MONITORING</th>
                            <th class="pe-4 py-3 small fw-bold text-end">REKOMENDASI KETERTIBAN</th>
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
                                <td>
                                    <span class="small text-slate-600">Babinsa/Bhabinkamtibmas/Satpol PP</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-slate-100 text-slate-700 border rounded-pill px-3">Kondusif</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <p class="small text-slate-500 mb-0">Tingkatkan koordinasi jaga warga dan perondaan malam.
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-slate-400 small">
                                    Belum ada data monitoring trantibum yang tercatat.
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
            <strong>Catatan:</strong> Laporan ini digunakan sebagai bahan evaluasi triwulanan stabilitas wilayah tingkat
            kecamatan.
            Setiap rekomendasi bersifat administratif untuk pembinaan penyelenggaraan ketertiban di tingkat desa.
        </p>
    </div>
@endsection