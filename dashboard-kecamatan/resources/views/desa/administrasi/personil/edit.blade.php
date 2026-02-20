@extends('layouts.desa')

@section('title', 'Edit Data Personil')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-4">
                    <a href="{{ route('desa.administrasi.personil.index', ['kategori' => $kategori]) }}"
                        class="text-decoration-none text-slate-500 fw-medium">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                </div>

                <!-- FORM CARD -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold text-slate-800 mb-0">Form Data
                            {{ $kategori == 'perangkat' ? 'Perangkat Desa' : 'Anggota BPD' }}
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('desa.administrasi.personil.update', $personil->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @php $readonly = !$personil->isEditable(); @endphp

                            <!-- 2. INFORMASI DASAR -->
                            <div class="mb-5">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-id-card me-2"></i> Informasi Dasar</h6>

                                <x-desa.form.input label="Nama Lengkap" name="nama" :value="$personil->nama"
                                    :readonly="$readonly" placeholder="Nama sesuai KTP" required="true" />

                                <x-desa.form.input label="Nomor Induk Kependudukan (NIK)" name="nik" :value="$personil->nik"
                                    :readonly="$readonly" placeholder="16 Digit Angka" required="true" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <x-desa.form.input label="Tempat Lahir" name="tempat_lahir" :value="$personil->tempat_lahir"
                                            :readonly="$readonly" placeholder="Kota/Kabupaten Lahir" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-desa.form.input label="Tanggal Lahir" name="tanggal_lahir" 
                                            type="date" :value="$personil->tanggal_lahir ? $personil->tanggal_lahir->format('Y-m-d') : ''" 
                                            :readonly="$readonly" required="true" />
                                    </div>
                                </div>

                                <x-desa.form.input label="Jabatan" name="jabatan" :value="$personil->jabatan"
                                    :readonly="$readonly" placeholder="Contoh: Sekretaris Desa" required="true"
                                    helper="Sesuai yang tertera pada SK Pengangkatan." />

                                @if($kategori == 'perangkat')
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-desa.form.input label="Mulai Menjabat (TMT)" name="masa_jabatan_mulai"
                                            type="date" :value="$personil->masa_jabatan_mulai ? $personil->masa_jabatan_mulai->format('Y-m-d') : ''" :readonly="$readonly"
                                            required="true" />
                                    </div>
                                </div>
                                @endif
                            </div>

                            <hr class="border-light my-5">

                            <!-- 3. DOKUMEN -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-file-contract me-2"></i> Dokumen Legalitas</h6>

                                <div class="row">
                                    <div class="col-md-8">
                                        <x-desa.form.input label="Nomor SK Pengangkatan" name="nomor_sk"
                                            :value="$personil->nomor_sk" :readonly="$readonly" placeholder="Nomor Surat Keputusan"
                                            required="true" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-desa.form.input label="Tanggal SK" name="tanggal_sk"
                                            type="date" :value="$personil->tanggal_sk ? $personil->tanggal_sk->format('Y-m-d') : ''"
                                            :readonly="$readonly" required="true" />
                                    </div>
                                </div>

                                <x-desa.form.upload label="File SK (Scan PDF)" name="file_sk"
                                    :currentFile="$personil->file_sk" :readonly="$readonly"
                                    :downloadUrl="$personil->file_sk ? route('desa.administrasi.file.personil', $personil->id) : null"
                                    helper="Lampirkan scan asli SK Pengangkatan. Pastikan tulisan terbaca jelas." />
                            </div>

                            <!-- 3. STATUS & AKSI -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary-600 text-uppercase small ls-1 mb-4"><i
                                        class="fas fa-tasks me-2"></i> Status & Aksi</h6>

                                <div class="bg-slate-50 border rounded-4 p-4">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-slate-700">Status Saat Ini</label>
                                        <div>
                                            <x-desa.form.status-badge :status="$personil->status"
                                                :catatan="$personil->catatan" />
                                        </div>
                                    </div>

                                    @if($personil->isEditable())
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
                        @if($personil->isEditable())
                            <form id="delete-form" action="{{ route('desa.administrasi.personil.destroy', $personil->id) }}"
                                method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            <form id="submit-form" action="{{ route('desa.administrasi.personil.submit', $personil->id) }}"
                                method="POST" class="d-none">
                                @csrf
                            </form>
                        @endif

                    </div>
                </div>
            </div>
@endsection