@extends('layouts.desa')

@section('title', 'Edit Dokumen')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-4">
                    <a href="{{ route('desa.administrasi.dokumen.index', ['tipe' => in_array($dokumen->tipe_dokumen, ['Perdes', 'Perkades']) ? 'perdes' : 'laporan']) }}"
                        class="text-decoration-none text-slate-500 fw-medium">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                </div>

                @if($dokumen->status == 'dikembalikan')
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Perlu Revisi dari Kecamatan:</h6>
                                <p class="mb-0 small">
                                    {{ $dokumen->catatan ?? 'Silakan perbaiki data atau dokumen sesuai instruksi.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold text-slate-800 mb-0">Edit Arsip Dokumen</h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('desa.administrasi.dokumen.update', $dokumen->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- 1. IDENTIFIKASI DOKUMEN -->
                            <div class="mb-5">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-search me-2"></i> Identifikasi Dokumen</h6>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-slate-700">Jenis Dokumen <span
                                                class="text-danger">*</span></label>
                                        <select name="tipe_dokumen" class="form-select rounded-3 border-slate-300 shadow-sm"
                                            required>
                                            <option value="Perdes" {{ $dokumen->tipe_dokumen == 'Perdes' ? 'selected' : '' }}>
                                                Peraturan Desa (Perdes)</option>
                                            <option value="Perkades" {{ $dokumen->tipe_dokumen == 'Perkades' ? 'selected' : '' }}>Peraturan Kepala Desa (Perkades)</option>
                                            <option value="LKPJ" {{ $dokumen->tipe_dokumen == 'LKPJ' ? 'selected' : '' }}>LKPJ
                                                Akhir Tahun</option>
                                            <option value="LPPD" {{ $dokumen->tipe_dokumen == 'LPPD' ? 'selected' : '' }}>LPPD
                                            </option>
                                            <option value="APBDes" {{ $dokumen->tipe_dokumen == 'APBDes' ? 'selected' : '' }}>
                                                APBDes</option>
                                            <option value="SK_Desa" {{ $dokumen->tipe_dokumen == 'SK_Desa' ? 'selected' : '' }}>SK Kepala Desa</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <x-desa.form.input label="Tahun Anggaran" name="tahun" type="number"
                                            placeholder="202X" value="{{ $dokumen->tahun }}" required="true" />
                                    </div>
                                </div>

                                <x-desa.form.input label="Nomor Dokumen" name="nomor_dokumen"
                                    placeholder="Contoh: 01/PERDES/DS/202X" value="{{ $dokumen->nomor_dokumen }}"
                                    required="true" />

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-slate-700">Perihal / Judul Dokumen <span
                                            class="text-danger">*</span></label>
                                    <textarea name="perihal" class="form-control rounded-3 border-slate-300 shadow-sm"
                                        rows="3" placeholder="Sebutkan inti dari dokumen ini..."
                                        required>{{ $dokumen->perihal }}</textarea>
                                </div>
                            </div>

                            <hr class="border-light my-5">

                            <!-- 2. FILE UPLOAD -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-file-pdf me-2"></i> File Dokumen</h6>

                                <div class="bg-slate-50 border rounded-4 p-3 mb-4 d-flex align-items-center">
                                    <div class="bg-white p-2 rounded-3 border me-3">
                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-slate-800 small text-truncate" style="max-width: 300px;">
                                            {{ basename($dokumen->file_path) }}
                                        </div>
                                        <a href="{{ route('desa.administrasi.file.dokumen', $dokumen->id) }}"
                                            target="_blank" class="small text-primary text-decoration-none">
                                            <i class="fas fa-eye me-1"></i> Lihat file saat ini
                                        </a>
                                    </div>
                                </div>

                                <x-desa.form.upload label="Ganti File Scan (Opsional)" name="file_dokumen"
                                    :downloadUrl="$dokumen->file_path ? route('desa.administrasi.file.dokumen', $dokumen->id) : null"
                                    helper="Kosongkan jika tidak ingin mengganti file. Gunakan format PDF (Maks. 5MB)." />
                            </div>

                            <!-- 3. AKSI -->
                            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                <a href="{{ route('desa.administrasi.dokumen.index', ['tipe' => in_array($dokumen->tipe_dokumen, ['Perdes', 'Perkades']) ? 'perdes' : 'laporan']) }}"
                                    class="btn btn-light rounded-pill px-4">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                                    <i class="fas fa-save me-2"></i> Update Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection