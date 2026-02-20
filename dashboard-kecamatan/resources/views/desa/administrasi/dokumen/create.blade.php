@extends('layouts.desa')

@section('title', 'Tambah Dokumen Baru')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-4">
                    <a href="{{ route('desa.administrasi.dokumen.index', ['tipe' => in_array($tipe, ['Perdes', 'Perkades']) ? 'perdes' : 'laporan']) }}"
                        class="text-decoration-none text-slate-500 fw-medium">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                </div>

                <div class="card border-0 shadow-layered rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-gradient-premium border-0 py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-file-plus text-primary-600"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white mb-0" style="font-size: 1.1rem;">Input Arsip Dokumen</h5>
                                <small class="text-white opacity-75" style="font-size: 0.75rem;">Lengkapi informasi dokumen
                                    dengan teliti</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('desa.administrasi.dokumen.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- 1. IDENTIFIKASI DOKUMEN -->
                            <div class="mb-4">
                                <div class="section-header-premium mb-3">
                                    <div class="accent-bar"></div>
                                    <div>
                                        <h6 class="fw-bold text-slate-800 mb-1"><i
                                                class="fas fa-search me-2 text-primary-500"></i> Identifikasi Dokumen</h6>
                                        <small class="text-slate-500">Jenis dan metadata dokumen</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-slate-700">Jenis Dokumen <span
                                                class="text-danger">*</span></label>
                                        <select name="tipe_dokumen" class="form-select rounded-3 border-slate-300 shadow-sm"
                                            required>
                                            @if($tipe == 'Perdes')
                                                <option value="Perdes" selected>Peraturan Desa (Perdes)</option>
                                                <option value="Perkades">Peraturan Kepala Desa (Perkades)</option>
                                                <option value="SK_Desa">SK Kepala Desa</option>
                                            @else
                                                <option value="LKPJ" {{ $tipe == 'LKPJ' ? 'selected' : '' }}>LKPJ Akhir Tahun
                                                </option>
                                                <option value="LPPD" {{ $tipe == 'LPPD' ? 'selected' : '' }}>LPPD</option>
                                                <option value="APBDes" {{ $tipe == 'APBDes' ? 'selected' : '' }}>APBDes</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <x-desa.form.input label="Tahun Anggaran" name="tahun" type="number"
                                            placeholder="202X" value="{{ date('Y') }}" required="true" />
                                    </div>
                                </div>

                                <x-desa.form.input label="Nomor Dokumen" name="nomor_dokumen"
                                    placeholder="Contoh: 01/PERDES/DS/202X atau 02 Tahun 202X" required="true" />

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-slate-700">Perihal / Judul Dokumen <span
                                            class="text-danger">*</span></label>
                                    <textarea name="perihal" class="form-control rounded-3 border-slate-300 shadow-sm"
                                        rows="3" placeholder="Sebutkan inti dari dokumen ini..." required></textarea>
                                </div>
                            </div>

                            <hr class="border-light my-5">

                            <!-- 2. FILE UPLOAD -->
                            <div class="mb-4">
                                <div class="section-header-premium mb-4">
                                    <div class="accent-bar"></div>
                                    <div>
                                        <h6 class="fw-bold text-slate-800 mb-1"><i
                                                class="fas fa-file-pdf me-2 text-primary-500"></i> File Dokumen</h6>
                                        <small class="text-slate-500">Unggah salinan resmi dalam PDF</small>
                                    </div>
                                </div>

                                <x-desa.form.upload label="File Scan (PDF)" name="file_dokumen"
                                    helper="Unggah salinan resmi dokumen dalam format PDF (Maks. 5MB). Pastikan stempel dan tanda tangan terlihat jelas."
                                    required="true" />
                            </div>

                            <!-- 3. AKSI -->
                            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                <a href="{{ route('desa.administrasi.dokumen.index', ['tipe' => in_array($tipe, ['Perdes', 'Perkades']) ? 'perdes' : 'laporan']) }}"
                                    class="btn btn-light rounded-pill px-4">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                                    <i class="fas fa-save me-2"></i> Simpan Draft
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection