@extends('layouts.kecamatan')

@section('title', $title ?? 'Arsip Dokumen Perencanaan Desa')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ auth()->user()->desa_id ? route('desa.pemerintahan.index') : route('kecamatan.pemerintahan.index') }}"
                class="btn btn-xs btn-light rounded-pill px-3 text-secondary text-decoration-none border shadow-sm">
                <i class="fas fa-arrow-left-long me-2"></i> Kembali ke Menu Utama
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-bold text-primary-900 mb-1">{{ $title ?? 'Arsip Dokumen Perencanaan' }}</h2>
                <p class="text-tertiary mb-0">
                    @if($desa_id)
                        <i class="fas fa-folder-open me-1"></i>
                        {{ $desc ?? 'Monitoring kelengkapan dokumen administratif desa.' }}
                    @else
                        <i class="fas fa-map-location-dot me-1"></i>
                        {{ $desc_pilih_desa ?? 'Pilih Desa untuk Memantau Kelengkapan Dokumen.' }}
                    @endif
                </p>
            </div>
            @if($desa_id)
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-premium" data-bs-toggle="modal"
                    data-bs-target="#uploadDokumenModal">
                    <i class="fas fa-file-arrow-up me-2"></i> Arsipkan Dokumen
                </button>
            @endif
        </div>
    </div>

    @if(!$desa_id)
        <div class="row g-4 mt-2">
            @foreach($desas as $index => $desa)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden"
                        style="border: 1px solid #e2e8f0 !important; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="bg-indigo-600 text-white rounded-3 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ substr($desa->nama_desa, 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="fw-bold text-slate-800 mb-0">Desa {{ $desa->nama_desa }}</h5>
                                    <span class="text-slate-400 small">{{ appProfile()->region_level }}
                                        {{ appProfile()->region_name }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-slate-500 small">Dokumen Tersimpan</span>
                                    <span class="badge bg-indigo-100 text-indigo-700 rounded-pill px-3">{{ $desa->dokumens_count }}
                                        Dokumen</span>
                                </div>
                                <div class="progress rounded-pill shadow-inner" style="height: 8px; background: #e2e8f0;">
                                    <div class="progress-bar bg-indigo-500 rounded-pill" role="progressbar"
                                        style="width: {{ min(100, ($desa->dokumens_count / 10) * 100) }}%"></div>
                                </div>
                            </div>

                            <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                                class="btn btn-outline-indigo w-100 rounded-3 fw-bold">
                                Lihat Detail Arsip <i class="fas fa-arrow-right-long ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-slate-100 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-slate-800 mb-0">Daftar Dokumen Digital</h5>
                            <span class="badge bg-slate-100 text-slate-600 px-3 py-2 border rounded-pill small">
                                Filter: {{ $tipe_filter ?? 'Semua Dokumen' }}
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-slate-50 border-bottom border-slate-200">
                                <tr class="text-slate-500 small fw-bold">
                                    <th class="ps-4 py-3">TAHUN</th>
                                    <th class="py-3 text-center">TIPE</th>
                                    <th class="py-3">NAMA DOKUMEN</th>
                                    <th class="py-3">TGL PENGARSIPAN</th>
                                    <th class="py-3 text-center">BERKAS</th>
                                    <th class="text-end pe-4 py-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dokumens as $d)
                                    <tr>
                                        <td class="ps-4">
                                            <span
                                                class="badge bg-slate-800 text-white px-3 py-2 rounded-3 fs-6">{{ $d->tahun }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $tStyle = $d->tipe_dokumen === 'RPJMDes'
                                                    ? 'bg-purple-100 text-purple-700 border-purple-200'
                                                    : 'bg-teal-100 text-teal-700 border-teal-200';
                                            @endphp
                                            <span class="badge border px-3 py-2 rounded-pill small {{ $tStyle }}">
                                                {{ $d->tipe_dokumen }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-slate-800">
                                                @if($d->tipe_dokumen == 'RPJMDes')
                                                    Rencana Pembangunan Jangka Menengah Desa
                                                @elseif($d->tipe_dokumen == 'RKPDes')
                                                    Rencana Kerja Pemerintah Desa
                                                @else
                                                    {{ $d->tipe_dokumen }}
                                                @endif
                                            </div>
                                            <div class="small text-slate-400">ID: DOC-{{ $d->id }}-{{ $d->tahun }}</div>
                                        </td>
                                        <td class="text-slate-600">{{ $d->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('kecamatan.file.dokumen', $d->id) }}" target="_blank"
                                                class="btn btn-sm btn-light border text-indigo-600 px-3 rounded-pill shadow-sm">
                                                <i class="fas fa-file-pdf me-1"></i> Lihat PDF
                                            </a>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm" title="Edit">
                                                    <i class="fas fa-pen-to-square text-slate-500"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm text-danger"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="p-5">
                                                <div class="bg-slate-50 d-inline-flex p-4 rounded-circle mb-4">
                                                    <i class="fas fa-file-circle-exclamation fa-3x text-slate-300"></i>
                                                </div>
                                                <h5 class="text-slate-800 fw-bold">Belum Ada Arsip</h5>
                                                <p class="text-slate-500 small mx-auto" style="max-width: 300px;">
                                                    Desa ini belum mengunggah dokumen digital
                                                    {{ strtolower($tipe_filter ?? 'perencanaan pembangunan') }}.
                                                </p>
                                            </div>
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

    <!-- Modal Upload -->
    <div class="modal fade" id="uploadDokumenModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom border-slate-100 p-4">
                    <h5 class="modal-title fw-bold text-slate-800">Pengarsipan {{ $tipe_filter ?? 'Dokumen Inti' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('kecamatan.pemerintahan.detail.dokumen.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="desa_id" value="{{ $desa_id }}">
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-slate-700">Jenis Dokumen</label>
                                <div class="d-flex gap-3">
                                    @if(isset($tipe_filter) && $tipe_filter == 'Peraturan Desa')
                                        <div class="flex-fill">
                                            <input type="radio" class="btn-check" name="tipe_dokumen" id="type_perdes"
                                                value="Peraturan Desa" checked>
                                            <label class="btn btn-outline-primary w-100 py-3 rounded-4" for="type_perdes">
                                                <i class="fas fa-gavel mb-1 d-block opacity-50"></i>
                                                Peraturan Desa
                                            </label>
                                        </div>
                                    @else
                                        <div class="flex-fill">
                                            <input type="radio" class="btn-check" name="tipe_dokumen" id="type_rpjm"
                                                value="RPJMDes" checked>
                                            <label class="btn btn-outline-indigo w-100 py-3 rounded-4" for="type_rpjm">
                                                <i class="fas fa-calendar-days mb-1 d-block opacity-50"></i>
                                                RPJMDes
                                            </label>
                                        </div>
                                        <div class="flex-fill">
                                            <input type="radio" class="btn-check" name="tipe_dokumen" id="type_rkp"
                                                value="RKPDes">
                                            <label class="btn btn-outline-teal w-100 py-3 rounded-4" for="type_rkp">
                                                <i class="fas fa-calendar-check mb-1 d-block opacity-50"></i>
                                                RKPDes
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-slate-700">Tahun Berlaku / Anggaran</label>
                                <input type="number" name="tahun"
                                    class="form-control form-control-lg rounded-3 border-slate-200" value="{{ date('Y') }}"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-slate-700">Unggah Berkas PDF (Max 5MB)</label>
                                <div
                                    class="border-2 border-dashed border-slate-200 rounded-4 p-4 text-center bg-slate-50 position-relative transition-all hover-border-indigo">
                                    <i class="fas fa-cloud-arrow-up fa-2x text-slate-300 mb-3"></i>
                                    <input type="file" name="file_dokumen" class="stretched-link opacity-0"
                                        accept="application/pdf" required>
                                    <div class="small text-slate-500">Klik atau seret file PDF ke sini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-slate-50 p-4 border-0">
                        <button type="button" class="btn btn-light border px-4 rounded-3 h-48"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-indigo px-4 rounded-3 h-48 shadow-sm">Simpan ke Arsip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-indigo {
            background: #4f46e5;
            color: #fff;
        }

        .btn-indigo:hover {
            background: #4338ca;
            color: #fff;
        }

        .btn-outline-indigo {
            border-color: #4f46e5;
            color: #4f46e5;
        }

        .btn-outline-indigo:hover {
            background: #4f46e5;
            color: #fff;
        }

        .btn-outline-teal {
            border-color: #0d9488;
            color: #0d9488;
        }

        .btn-outline-teal:hover {
            background: #0d9488;
            color: #fff;
        }

        .bg-indigo-500 {
            background: #6366f1;
        }

        .bg-indigo-600 {
            background: #4f46e5;
        }

        .bg-indigo-100 {
            background: #eef2ff;
        }

        .text-indigo-600 {
            color: #4f46e5;
        }

        .text-indigo-700 {
            color: #3730a3;
        }

        .bg-purple-100 {
            background: #f5f3ff;
        }

        .text-purple-700 {
            color: #6d28d9;
        }

        .border-purple-200 {
            border-color: #ddd6fe;
        }

        .bg-teal-100 {
            background: #f0fdfa;
        }

        .text-teal-700 {
            color: #0f766e;
        }

        .border-teal-200 {
            border-color: #99f6e4;
        }

        .bg-slate-50 {
            background: #f8fafc;
        }

        .bg-slate-100 {
            background: #f1f5f9;
        }

        .border-slate-100 {
            border-color: #f1f5f9;
        }

        .border-slate-200 {
            border-color: #e2e8f0;
        }

        .h-48 {
            height: 48px;
        }

        .hover-border-indigo:hover {
            border-color: #4f46e5 !important;
            background-color: #f5f3ff !important;
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }
    </style>
@endpush