@extends('layouts.kecamatan')

@section('title', 'Detail Aparatur Desa')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header & Action -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('kecamatan.pemerintahan.aparatur.index') }}">Aparatur
                                Desa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $aparatur->nama_lengkap }}</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0">Profil Administratif Aparatur Desa</h4>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('kecamatan.pemerintahan.aparatur.edit', $aparatur->id) }}"
                    class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>Ubah Data</span>
                </a>
                <a href="{{ route('kecamatan.pemerintahan.aparatur.index') }}" class="btn btn-light">Kembali</a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Identity & Personal -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="avatar-xl bg-soft-teal text-teal-600 rounded-circle fw-bold d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 100px; height: 100px; font-size: 40px;">
                            {{ strtoupper(substr($aparatur->nama_lengkap, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $aparatur->nama_lengkap }}</h5>
                        <p class="text-muted mb-3">{{ $aparatur->nik }}</p>

                        <div class="d-grid mb-3">
                            <span class="badge bg-soft-teal text-teal-700 py-2 rounded-pill border-0 fs-6">
                                {{ $aparatur->jabatan }}
                                @if($aparatur->status_jabatan == 'Pj') (Pejabat) @endif
                            </span>
                        </div>
                        <hr class="border-light my-4">

                        <div class="text-start">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Pendidikan Terakhir</small>
                                <div class="fw-bold text-dark">{{ $aparatur->pendidikan_terakhir ?? '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Jenis Kelamin</small>
                                <div class="fw-bold text-dark">
                                    {{ $aparatur->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted d-block mb-1">Status Verifikasi</small>
                                @if($aparatur->status_verifikasi == 'Terverifikasi')
                                    <span class="badge bg-success py-2 rounded-pill w-100"><i
                                            class="fas fa-check-circle me-1"></i> Terverifikasi</span>
                                @elseif($aparatur->status_verifikasi == 'Perlu Perbaikan')
                                    <span class="badge bg-danger py-2 rounded-pill w-100"><i
                                            class="fas fa-exclamation-circle me-1"></i> Perlu Perbaikan</span>
                                @else
                                    <span class="badge bg-secondary py-2 rounded-pill w-100">Belum Diverifikasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-folder-open me-2 text-primary"></i> Dokumen Pendukung</h6>
                    </div>
                    <div class="card-body p-3">
                        @forelse($aparatur->documents->where('is_active', true) as $doc)
                            <div class="d-flex align-items-center p-3 border rounded-3 bg-light mb-2">
                                <div
                                    class="avatar-sm bg-soft-danger text-danger rounded d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="small fw-bold text-dark text-truncate">{{ $doc->original_filename }}</div>
                                    <small class="text-muted">{{ $doc->document_type }}</small>
                                </div>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                    class="btn btn-sm btn-icon btn-white shadow-sm rounded-circle ms-2" title="Unduh">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted x-small mb-0">Belum ada dokumen yang diunggah.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Administrative Details -->
            <div class="col-lg-8">
                <!-- Appointment Details -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-certificate me-2 text-warning-600"></i> Informasi
                            Legalitas Pengangkatan</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Nomor Surat Keputusan (SK)</small>
                                <div class="fw-bold fs-6 text-dark">{{ $aparatur->nomor_sk }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Tanggal Surat Keputusan (SK)</small>
                                <div class="fw-bold fs-6 text-dark">{{ $aparatur->tanggal_sk->format('d F Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Awal Masa Jabatan</small>
                                <div class="fw-bold fs-6 text-dark text-teal-700">
                                    {{ $aparatur->tanggal_mulai->format('d F Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Akhir Masa Jabatan</small>
                                <div class="fw-bold fs-6 text-dark">
                                    {{ $aparatur->tanggal_akhir ? $aparatur->tanggal_akhir->format('d F Y') : 'Hingga Selesai' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert bg-soft-teal border-0 rounded-3">
                                    <div class="d-flex gap-3">
                                        <i class="fas fa-info-circle text-teal-700 mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-teal-700 small">Masa Jabatan</div>
                                            <p class="mb-0 small text-teal-600">
                                                Aparatur ini menjabat di <strong>{{ $aparatur->desa->nama_desa }}</strong>.
                                                Pastikan data SK telah sesuai dengan arsip kecamatan untuk kepentingan
                                                administrasi dan pembinaan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verification Action Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-clipboard-check me-2"></i> Verifikasi
                            Administratif (Kecamatan)</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.pemerintahan.aparatur.verify', $aparatur->id) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Keputusan Verifikasi</label>
                                    <select name="status_verifikasi" class="form-select border-light bg-light" required>
                                        <option value="{{ \App\Models\AparaturDesa::VERIFIKASI_SUDAH }}" {{ $aparatur->status_verifikasi == \App\Models\AparaturDesa::VERIFIKASI_SUDAH ? 'selected' : '' }}>Setujui & Verifikasi</option>
                                        <option value="{{ \App\Models\AparaturDesa::VERIFIKASI_REVISI }}" {{ $aparatur->status_verifikasi == \App\Models\AparaturDesa::VERIFIKASI_REVISI ? 'selected' : '' }}>Kembalikan untuk Perbaikan</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Catatan Pembinaan / Perbaikan</label>
                                    <textarea name="catatan_kecamatan" class="form-control border-light bg-light" rows="4"
                                        placeholder="Tuliskan catatan administratif jika ada berkas yang perlu diperbaiki atau instruksi pembinaan...">{{ $aparatur->catatan_kecamatan }}</textarea>
                                    <div class="form-text small">Gunakan bahasa yang netral dan bersifat membina bagi
                                        operasional desa.</div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-4">Update Status Verifikasi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Audit Info -->
                <div class="text-center p-3 text-muted small">
                    Diperbarui terakhir oleh: <strong>{{ $aparatur->updater->name ?? 'System' }}</strong> pada
                    {{ $aparatur->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-teal {
            background: rgba(20, 184, 166, 0.1);
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.1);
        }

        .text-teal-600 {
            color: #0d9488;
        }

        .text-teal-700 {
            color: #0f766e;
        }

        .text-warning-600 {
            color: #d97706;
        }

        .x-small {
            font-size: 0.75rem;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }
    </style>
@endsection