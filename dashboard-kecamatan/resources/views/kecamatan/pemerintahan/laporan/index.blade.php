@extends('layouts.kecamatan')

@section('title', 'Monitoring LKPJ & LPPD Desa')

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
                <h2 class="fw-bold text-primary-900 mb-1">Monitoring LKPJ & LPPD Desa</h2>
                <p class="text-tertiary mb-0">
                    @if($desa_id)
                        <i class="fas fa-file-signature me-1"></i> Laporan Keterangan Penyelenggaraan Pemerintahan Desa.
                    @else
                        <i class="fas fa-map-location-dot me-1"></i> Pilih Desa untuk Melihat Status Penyampaian Laporan.
                    @endif
                </p>
            </div>
            @if($desa_id)
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-premium" data-bs-toggle="modal"
                    data-bs-target="#uploadLaporanModal">
                    <i class="fas fa-upload me-2"></i> Arsipkan Laporan
                </button>
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
                            <th class="text-center">Laporan LKPJ/LPPD Tersimpan</th>
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
                                        style="font-size: 0.85rem;">{{ $desa->dokumens_count }} Laporan</span>
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
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="card bg-white border-gray-200 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4">Tahun Laporan</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Tgl Disampaikan</th>
                                    <th>Status Verifikasi</th>
                                    <th>File</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporans as $l)
                                    <tr>
                                        <td><span class="year-badge-sm">{{ $l->tahun }}</span></td>
                                        <td>
                                            <div class="fw-bold">{{ $l->tipe_dokumen }}</div>
                                        </td>
                                        <td>{{ $l->tanggal_penyampaian ? $l->tanggal_penyampaian->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'disampaikan' => 'bg-info-soft text-info',
                                                    'perbaikan' => 'bg-warning-soft text-warning',
                                                    'terverifikasi' => 'bg-success-soft text-success',
                                                ][$l->status_verifikasi] ?? 'bg-secondary-soft text-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }} text-uppercase">{{ $l->status_verifikasi }}</span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-xs btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-icon btn-sm"><i class="fas fa-search-plus"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-file-signature fa-3x mb-3 text-muted"></i>
                                            <p class="text-muted">Belum ada laporan tahunan yang diarsipkan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Upload -->
        <div class="modal fade" id="uploadLaporanModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Arsip Laporan Tahunan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('kecamatan.pemerintahan.detail.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="desa_id" value="{{ $desa_id }}">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Jenis Laporan</label>
                                    <select name="tipe_dokumen" class="form-select" required>
                                        <option value="LKPJ">LKPJ (Kepada BPD)</option>
                                        <option value="LPPD">LPPD (Kepada Bupati via Camat)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tahun Laporan</label>
                                    <input type="number" name="tahun" class="form-control" value="{{ date('Y') - 1 }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Penyampaian</label>
                                    <input type="date" name="tanggal_penyampaian" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pilih File PDF (Max 5MB)</label>
                                    <input type="file" name="file_dokumen" class="form-control" accept="application/pdf"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .year-badge-sm {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .bg-info-soft {
            background-color: rgba(6, 182, 212, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(16, 185, 129, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .btn-xs {
            padding: 0.2rem 0.6rem;
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