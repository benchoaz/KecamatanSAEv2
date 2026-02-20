@extends('layouts.desa')

@section('title', 'Tambah Dokumen Perencanaan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('desa.pemerintahan.detail.perencanaan.index') }}"
                        class="btn btn-sm btn-light border rounded-circle">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0">Input Baru: Mode {{ ucfirst($mode) }}</h4>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span
                                    class="badge bg-{{ $mode === 'arsip' ? 'secondary' : ($mode === 'transisi' ? 'primary' : 'success') }}-soft text-{{ $mode === 'arsip' ? 'secondary' : ($mode === 'transisi' ? 'primary' : 'success') }} rounded-pill px-3 mb-2">
                                    {{ strtoupper($mode) }} MODE
                                </span>
                                <h5 class="mb-0 fw-bold">Perencanaan Tahun {{ $year }}</h5>
                            </div>
                            <i
                                class="fas fa-{{ $mode === 'arsip' ? 'archive' : ($mode === 'transisi' ? 'sync' : 'project-diagram') }} fa-2x text-light"></i>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('desa.pemerintahan.detail.perencanaan.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ $year }}">

                            <div class="row g-4">
                                <!-- Basic Metadata -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tipe Dokumen <span
                                            class="text-danger">*</span></label>
                                    <select name="tipe_dokumen"
                                        class="form-select @error('tipe_dokumen') is-invalid @enderror" required>
                                        <option value="" disabled selected>Pilih tipe...</option>
                                        <option value="RPJMDes">RPJMDes (Rencana Pembangunan Jangka Menengah)</option>
                                        <option value="RKPDes">RKPDes (Rencana Kerja Pemerintah Desa)</option>
                                        <option value="APBDes">APBDes (Anggaran Pendapatan & Belanja Desa)</option>
                                    </select>
                                    @error('tipe_dokumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tahun Berjalan</label>
                                    <input type="text" class="form-control bg-light" value="{{ $year }}" readonly>
                                </div>

                                @if($mode === 'transisi' || $mode === 'terstruktur')
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nomor Perdes / Keputusan <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="nomor_perdes"
                                            class="form-control @error('nomor_perdes') is-invalid @enderror"
                                            placeholder="Contoh: No. 05 Tahun 2026" required>
                                        @error('nomor_perdes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Penetapan <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_perdes"
                                            class="form-control @error('tanggal_perdes') is-invalid @enderror" required>
                                        @error('tanggal_perdes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif

                                <!-- File Upload -->
                                <div class="col-12">
                                    <div class="upload-zone p-4 text-center border-dashed rounded-4 bg-light cursor-pointer"
                                        onclick="document.getElementById('file_dokumen').click()">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h5>Upload Dokumen Utama <span class="text-danger">*</span></h5>
                                        <p class="text-muted small">Wajib format PDF lampiran dokumen lengkap (Max 10MB)</p>
                                        <input type="file" name="file_dokumen" id="file_dokumen" class="d-none"
                                            accept=".pdf" required onchange="updateFileName(this)">
                                        <div id="file-name" class="mt-2 text-primary fw-bold"></div>
                                    </div>
                                    @error('file_dokumen') <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($mode === 'terstruktur')
                                    <div class="col-12 mt-4">
                                        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-0">
                                            <div class="d-flex gap-3">
                                                <i class="fas fa-info-circle fa-2x mt-1"></i>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Rincian Kegiatan (Mode Terstruktur)</h6>
                                                    <p class="mb-0 small">Pada mode terstruktur, Anda dapat menghubungkan data
                                                        ini dengan usulan Musrenbang sebelumnya. Form rincian kegiatan akan
                                                        tampil setelah dokumen utama divalidasi oleh Kecamatan.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12 mt-5">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('desa.pemerintahan.detail.perencanaan.index') }}"
                                            class="btn btn-light px-4 rounded-pill">Batal</a>
                                        <button type="submit"
                                            class="btn btn-brand-600 text-white px-5 rounded-pill shadow-sm">
                                            <i class="fas fa-save me-2"></i> Simpan Draft
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : '';
            document.getElementById('file-name').textContent = fileName ? 'File terpilih: ' + fileName : '';
        }
    </script>

    <style>
        .border-dashed {
            border: 2px dashed #cbd5e1;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .upload-zone:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .bg-primary-soft {
            background-color: #eef2ff;
            color: #4f46e5;
        }

        .bg-secondary-soft {
            background-color: #f1f5f9;
            color: #475569;
        }

        .bg-success-soft {
            background-color: #ecfdf5;
            color: #059669;
        }

        .btn-brand-600 {
            background-color: #9DC183;
            border-color: #9DC183;
        }

        .btn-brand-600:hover {
            background-color: #7fa665;
            border-color: #7fa665;
        }
    </style>
@endsection