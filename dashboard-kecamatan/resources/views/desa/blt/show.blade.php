@extends('layouts.desa')

@section('title', 'Detail BLT Desa - ' . $item->tahun_anggaran)

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa.blt.index') }}" class="text-decoration-none">BLT
                                Desa</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold text-slate-800 mb-0">Laporan BLT TA {{ $item->tahun_anggaran }}</h2>
                    <div class="d-flex gap-2">
                        @php
                            $statusClass = [
                                'Draft' => 'bg-slate-100 text-slate-600',
                                'Dikirim' => 'bg-blue-100 text-blue-600',
                                'Dikembalikan' => 'bg-amber-100 text-amber-600',
                                'Dicatat' => 'bg-emerald-100 text-emerald-600',
                            ][$item->status_laporan] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 fw-normal">Status:
                            {{ $item->status_laporan }}</span>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-8">
                        <!-- Penerima Manfaat -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-500 text-uppercase small mb-4">Penerima Manfaat (KPM)</h6>
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-sky-50 text-sky-600 rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 48px; height: 48px;">
                                                <i class="fas fa-users fa-lg"></i>
                                            </div>
                                            <div>
                                                <div class="small text-slate-400">Total KPM Terealisasi</div>
                                                <div class="h4 fw-bold text-slate-800 mb-0">{{ $item->kpm_terealisasi }} /
                                                    {{ $item->jumlah_kpm }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        @php $pct = ($item->jumlah_kpm > 0) ? ($item->kpm_terealisasi / $item->jumlah_kpm * 100) : 0; @endphp
                                        <div class="small text-slate-500 mb-1">Presentase Penyaluran</div>
                                        <div class="fw-bold h5 text-sky-600">{{ number_format($pct, 1) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Realisasi Dana -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-500 text-uppercase small mb-4">Realisasi Dana BLT</h6>
                                <div class="p-4 bg-emerald-50 rounded-4 border border-emerald-100 text-center">
                                    <div class="text-slate-500 small mb-1">Total Dana Tersalurkan</div>
                                    <div class="display-6 fw-bold text-emerald-700">Rp
                                        {{ number_format($item->total_dana_tersalurkan, 0, ',', '.') }}</div>
                                </div>
                                <div class="mt-4 row">
                                    <div class="col-md-6 border-end">
                                        <div class="small text-slate-400">Status Penyaluran</div>
                                        <div class="fw-bold text-slate-800">{{ $item->status_penyaluran }}</div>
                                    </div>
                                    <div class="col-md-6 ps-md-4">
                                        <div class="small text-slate-400">Periode Terakhir</div>
                                        <div class="fw-bold text-slate-800">{{ $item->periode_terakhir ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        @if($item->catatan_desa)
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-slate-500 text-uppercase small mb-3">Catatan Pelaksanaan</h6>
                                    <div
                                        class="p-3 bg-slate-50 rounded-3 text-slate-700 italic border-start border-slate-300 border-4">
                                        "{{ $item->catatan_desa }}"
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <!-- File Bukti -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-500 text-uppercase small mb-4">Lampiran Bukti</h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 border-0 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-indigo-50 text-indigo-600 rounded p-2 me-3">
                                                <i class="fas fa-file-signature fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="small fw-bold text-slate-800">Berita Acara (BA)</div>
                                                @if($item->dokumen_ba)
                                                    <a href="{{ asset('storage/' . $item->dokumen_ba) }}" target="_blank"
                                                        class="small text-decoration-none">Unduh Dokumen</a>
                                                @else
                                                    <span class="small text-slate-400 italic">Belum diunggah</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-amber-50 text-amber-600 rounded p-2 me-3">
                                                <i class="fas fa-image fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="small fw-bold text-slate-800">Foto Penyaluran</div>
                                                @if($item->foto_penyaluran)
                                                    <a href="{{ asset('storage/' . $item->foto_penyaluran) }}" target="_blank"
                                                        class="small text-decoration-none text-amber-600">Buka Foto</a>
                                                @else
                                                    <span class="small text-slate-400 italic">Belum diunggah</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5 mb-5">
                    <a href="{{ route('desa.blt.index') }}" class="btn btn-outline-slate rounded-pill px-4">Kembali Ke
                        Daftar</a>
                    <div class="d-flex gap-2">
                        @if($item->status_laporan === 'Draft' || $item->status_laporan === 'Dikembalikan')
                            <a href="{{ route('desa.blt.edit', $item->id) }}"
                                class="btn btn-slate-700 text-white rounded-pill px-4">Edit Laporan</a>
                            <form action="{{ route('desa.blt.submit', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sky text-white rounded-pill px-4">Kirim Ke
                                    Kecamatan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection