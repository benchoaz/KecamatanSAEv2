@extends('layouts.desa')

@section('title', 'Edit Data Lembaga')

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

                <!-- FORM CARD -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold text-slate-800 mb-0">Edit Data Lembaga</h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('desa.administrasi.lembaga.update', $lembaga->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @php $readonly = !$lembaga->isEditable(); @endphp

                            <!-- 1. INFORMASI DASAR -->
                            <div class="mb-5">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-sitemap me-2"></i> Informasi Dasar</h6>

                                <x-desa.form.input label="Nama Lembaga" name="nama_lembaga" :value="$lembaga->nama_lembaga"
                                    :readonly="$readonly" placeholder="Nama Lembaga" required="true" />

                                <x-desa.form.input label="Nama Ketua Lembaga" name="ketua" :value="$lembaga->ketua"
                                    :readonly="$readonly" placeholder="Nama Ketua" required="true" />
                            </div>

                            <hr class="border-light my-5">

                            <!-- 2. DOKUMEN -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-file-contract me-2"></i> Dokumen Pendirian</h6>

                                <x-desa.form.input label="Nomor SK Pendirian" name="sk_pendirian"
                                    :value="$lembaga->sk_pendirian" :readonly="$readonly" placeholder="Nomor SK"
                                    required="true" />

                                <x-desa.form.upload label="File SK (Scan PDF)" name="file_sk"
                                    :currentFile="$lembaga->file_sk" :readonly="$readonly" :downloadUrl="$lembaga->file_sk ? route('desa.administrasi.file.lembaga', $lembaga->id) : null"
                                    helper="Lampirkan scan SK Pendirian." />
                            </div>

                            <!-- 3. STATUS & AKSI -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-tasks me-2"></i> Status & Aksi</h6>

                                <div class="bg-slate-50 border rounded-4 p-4">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-slate-700">Status Saat Ini</label>
                                        <div>
                                            <x-desa.form.status-badge :status="$lembaga->status"
                                                :catatan="$lembaga->catatan" />
                                        </div>
                                    </div>

                                    @if($lembaga->isEditable())
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top">
                                            <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                                                onclick="if(confirm('Yakin ingin menghapus?')) document.getElementById('delete-form').submit()">
                                                <i class="fas fa-trash me-2"></i> Hapus
                                            </button>

                                            <div class="d-flex gap-2">
                                                <button type="submit"
                                                    class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                                                    <i class="fas fa-save me-2"></i> Simpan Draft
                                                </button>

                                                <button type="button"
                                                    class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm"
                                                    onclick="if(confirm('Data akan dikunci setelah dikirim. Lanjutkan?')) document.getElementById('submit-form').submit()">
                                                    <i class="fas fa-paper-plane me-2"></i> Kirim ke Kecamatan
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert bg-light border text-center rounded-3 mb-0">
                                            <i class="fas fa-lock me-2 text-secondary"></i> Data terkunci karena sedang dalam
                                            proses verifikasi.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>

                        <!-- Hidden Forms for Actions -->
                        @if($lembaga->isEditable())
                            <form id="delete-form" action="{{ route('desa.administrasi.lembaga.destroy', $lembaga->id) }}"
                                method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            <form id="submit-form" action="{{ route('desa.administrasi.lembaga.submit', $lembaga->id) }}"
                                method="POST" class="d-none">
                                @csrf
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection