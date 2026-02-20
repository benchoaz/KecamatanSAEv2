@extends('layouts.desa')

@section('title', 'Input Data Lembaga Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-4">
                    <a href="{{ route('desa.administrasi.lembaga.index') }}"
                        class="text-decoration-none text-slate-500 fw-medium">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold text-slate-800 mb-0">Input Data Lembaga Desa</h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('desa.administrasi.lembaga.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- 1. INFORMASI DASAR -->
                            <div class="mb-5">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-sitemap me-2"></i> Informasi Dasar</h6>

                                <x-desa.form.input label="Nama Lembaga" name="nama_lembaga"
                                    placeholder="Contoh: LPM Desa Maju Jaya" required="true" />

                                <x-desa.form.input label="Nama Ketua Lembaga" name="ketua" placeholder="Nama Lengkap Ketua"
                                    required="true" />
                            </div>

                            <hr class="border-light my-5">

                            <!-- 2. DOKUMEN -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-file-contract me-2"></i> Dokumen Pendirian</h6>

                                <x-desa.form.input label="Nomor SK Pendirian/Pengukuhan" name="sk_pendirian"
                                    placeholder="Nomor SK" required="true" />

                                <x-desa.form.upload label="File SK (Scan PDF)" name="file_sk"
                                    helper="Lampirkan scan SK Pendirian. Pastikan tulisan terbaca jelas." required="true" />
                            </div>

                            <!-- 3. AKSI -->
                            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                <a href="{{ route('desa.administrasi.lembaga.index') }}"
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