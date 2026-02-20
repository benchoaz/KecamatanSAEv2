@use('Illuminate\Support\Facades\Storage')
@extends('layouts.desa')

@section('title', 'Lengkapi Dokumen Musdes')

@section('content')
    <div class="row g-4 justify-content-center">
        <!-- Kolom Kiri: Form Data (Read-Only/Editable) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div
                    class="card-header bg-white border-bottom-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Data Kegiatan</h5>
                        <p class="text-muted small mb-0">Pastikan data kegiatan sudah benar sebelum upload dokumen.</p>
                    </div>
                    <span class="badge bg-amber-50 text-amber-600 px-3 py-2 rounded-pill small fw-bold">
                        Mode Draft
                    </span>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('desa.musdes.update', $submission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-secondary">Nama Kegiatan</label>
                            <input type="text" name="judul" class="form-control bg-light fw-bold"
                                value="{{ old('judul', $submission->judul) }}">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase text-secondary">Jenis Musdes</label>
                                <select name="jenis_musdes" class="form-select bg-light">
                                    <option value="Musdes Perencanaan" {{ $details['jenis_musdes'] == 'Musdes Perencanaan' ? 'selected' : '' }}>Musdes Perencanaan</option>
                                    <option value="Musdes RKP Desa" {{ $details['jenis_musdes'] == 'Musdes RKP Desa' ? 'selected' : '' }}>Musdes RKP Desa</option>
                                    <option value="Musdes APBDes" {{ $details['jenis_musdes'] == 'Musdes APBDes' ? 'selected' : '' }}>Musdes APBDes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase text-secondary">Tahun</label>
                                <select name="periode" class="form-select bg-light">
                                    <option value="{{ $submission->periode }}">{{ $submission->periode }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-uppercase text-secondary">Tanggal</label>
                                <input type="date" name="tanggal_pelaksanaan" class="form-control bg-light"
                                    value="{{ old('tanggal_pelaksanaan', $details['tanggal_pelaksanaan']) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-uppercase text-secondary">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control bg-light"
                                    value="{{ old('lokasi', $details['lokasi']) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-uppercase text-secondary">Undangan</label>
                                <input type="number" name="jumlah_undangan" class="form-control bg-light"
                                    value="{{ old('jumlah_undangan', $details['jumlah_undangan'] ?? 15) }}" min="1">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-light btn-sm text-primary fw-bold">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bagian Upload -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 py-4 px-4">
                    <h5 class="fw-bold mb-1">Upload Dokumen Bukti</h5>
                    <p class="text-muted small mb-0">Format: JPG, PNG, atau PDF. Maks 5MB per file.</p>
                </div>
                <div class="card-body px-4">

                    <!-- 1. Berita Acara -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>1. Berita Acara Musdes <span class="text-danger">*</span></span>
                            @if($submission->files->where('file_type', 'berita_acara')->count() > 0)
                                <span class="text-success small"><i class="fas fa-check-circle me-1"></i> Terupload</span>
                            @else
                                <span class="text-secondary small ms-2"><i class="far fa-circle"></i> Wajib</span>
                            @endif
                        </label>

                        @foreach($submission->files->where('file_type', 'berita_acara') as $file)
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-2 border">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-pdf text-danger fs-4"></i>
                                    <div class="small fw-bold text-dark">Berita Acara.pdf</div>
                                </div>
                                <form
                                    action="{{ route('desa.musdes.deleteFile', ['id' => $submission->id, 'fileId' => $file->id]) }}"
                                    method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon text-muted hover-danger"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        @endforeach

                        @if($submission->files->where('file_type', 'berita_acara')->count() == 0)
                            <form action="{{ route('desa.musdes.upload', $submission->id) }}" method="POST"
                                enctype="multipart/form-data" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="type" value="berita_acara">
                                <input type="file" name="file" class="form-control" accept=".pdf" required>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i></button>
                            </form>
                        @endif
                    </div>

                    <!-- 2. Daftar Hadir -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>2. Daftar Hadir Peserta <span class="text-danger">*</span></span>
                            @if($submission->files->where('file_type', 'daftar_hadir')->count() > 0)
                                <span class="text-success small"><i class="fas fa-check-circle me-1"></i> Terupload</span>
                            @endif
                        </label>

                        @foreach($submission->files->where('file_type', 'daftar_hadir') as $file)
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-2 border">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-image text-indigo index-1 fs-4"></i>
                                    <div class="small fw-bold text-dark">Scan Absen.jpg</div>
                                </div>
                                <form
                                    action="{{ route('desa.musdes.deleteFile', ['id' => $submission->id, 'fileId' => $file->id]) }}"
                                    method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon text-muted hover-danger"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        @endforeach

                        <form action="{{ route('desa.musdes.upload', $submission->id) }}" method="POST"
                            enctype="multipart/form-data" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="type" value="daftar_hadir">
                            <input type="file" name="file" class="form-control" accept="image/*,.pdf" required>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i></button>
                        </form>
                    </div>

                    <!-- 3. Foto Kegiatan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>3. Foto Dokumentasi <span class="text-danger">*</span></span>
                            @if($submission->files->where('file_type', 'foto_kegiatan')->count() > 0)
                                <span class="text-success small"><i class="fas fa-check-circle me-1"></i>
                                    {{ $submission->files->where('file_type', 'foto_kegiatan')->count() }} Foto</span>
                            @endif
                        </label>

                        <div class="row g-2 mb-2">
                            @foreach($submission->files->where('file_type', 'foto_kegiatan') as $file)
                                <div class="col-4 position-relative">
                                    <div class="ratio ratio-4x3 bg-light rounded-3 overflow-hidden border">
                                        <img src="{{ Storage::url($file->file_path) }}" class="object-fit-cover" alt="Foto">
                                    </div>
                                    <form
                                        action="{{ route('desa.musdes.deleteFile', ['id' => $submission->id, 'fileId' => $file->id]) }}"
                                        method="POST" class="position-absolute top-0 end-0 m-2">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-circle shadow-sm py-1 px-2"
                                            style="width: 30px; height: 30px;"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('desa.musdes.upload', $submission->id) }}" method="POST"
                            enctype="multipart/form-data" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="type" value="foto_kegiatan">
                            <input type="file" name="file" class="form-control" accept="image/*" required>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Foto</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Catatan & Submit -->
        <div class="col-lg-4">
            <!-- Jika Returned, Tampilkan Catatan -->
            @if($submission->status == 'returned' && $submission->notes->count() > 0)
                <div class="card border-danger shadow-sm rounded-4 mb-4 bg-rose-50">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="fas fa-exclamation-circle me-1"></i> Catatan Perbaikan
                        </h6>
                        <div class="bg-white p-3 rounded-3 border border-danger-subtle text-sage-800 small">
                            {{ $submission->notes->first()->note }}
                        </div>
                        <div class="mt-2 text-end text-muted small fst-italic">
                            Dari: Kecamatan, {{ $submission->notes->first()->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bantuan Penyusunan Laporan -->
            <div class="card border-0 shadow-sm rounded-4 mb-4"
                style="background: linear-gradient(to bottom right, #f0fdf4, #ffffff);">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-success mb-3">
                        <i class="fas fa-lightbulb me-1"></i> Bantuan Penyusunan
                    </h6>
                    <p class="text-muted small mb-3">Belum punya format dokumen? Gunakan draf standar ini untuk mempercepat
                        laporan Anda.</p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('desa.musdes.template.download', ['type' => 'ba', 'submission_id' => $submission->id]) }}"
                            class="btn btn-outline-success btn-sm text-start py-2">
                            <i class="fas fa-file-word me-2"></i> Templat Berita Acara
                        </a>
                        <a href="{{ route('desa.musdes.template.download', ['type' => 'absen', 'submission_id' => $submission->id]) }}"
                            class="btn btn-outline-success btn-sm text-start py-2">
                            <i class="fas fa-file-word me-2"></i> Templat Daftar Hadir
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Finalisasi</h5>
                    <p class="text-muted small">Jika semua data dan dokumen sudah lengkap, silakan kirim laporan ini.</p>

                    <hr class="text-slate-200">

                    <div class="d-grid gap-2">
                        <form action="{{ route('desa.musdes.submit', $submission->id) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin data sudah lengkap? Data tidak dapat diubah setelah dikirim.');">
                            @csrf
                            <button type="submit" class="btn btn-primary py-3 fw-bold rounded-3 w-100 shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Kirim ke Kecamatan
                            </button>
                        </form>

                        <a href="{{ route('desa.musdes.index') }}"
                            class="btn btn-light py-2 fw-bold rounded-3 text-secondary">
                            Simpan Draft & Keluar
                        </a>
                    </div>

                    @if(session('validation_errors'))
                        <div class="alert alert-danger mt-3 mb-0 small border-0">
                            <ul class="mb-0 ps-3">
                                @foreach(session('validation_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .text-amber-600 {
            color: #d97706;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .hover-danger:hover {
            color: #dc2626 !important;
        }
    </style>
@endsection