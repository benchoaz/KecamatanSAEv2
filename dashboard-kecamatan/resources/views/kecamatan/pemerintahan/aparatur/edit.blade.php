@extends('layouts.kecamatan')

@section('title', 'Ubah Data Aparatur Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Breadcrumb & Header -->
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('kecamatan.pemerintahan.aparatur.index') }}">Aparatur Desa</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('kecamatan.pemerintahan.aparatur.show', $aparatur->id) }}">{{ $aparatur->nama_lengkap }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Ubah Data</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold">Perbarui Data Aparatur Desa</h4>
                    <p class="text-muted">Simpan perubahan data administratif berdasarkan dokumen legalitas terbaru.</p>
                </div>

                <form action="{{ route('kecamatan.pemerintahan.aparatur.update', $aparatur->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Section 1: Data Desa & Jabatan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold text-teal-700"><i class="fas fa-building me-2"></i> Penempatan & Jabatan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Desa</label>
                                    <select name="desa_id" class="form-select @error('desa_id') is-invalid @enderror"
                                        required>
                                        @foreach($villages as $desa)
                                            <option value="{{ $desa->id }}" {{ old('desa_id', $aparatur->desa_id) == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                                        @endforeach
                                    </select>
                                    @error('desa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Jabatan</label>
                                    <select name="jabatan" class="form-select @error('jabatan') is-invalid @enderror"
                                        required>
                                        @php $jabs = ['Kades', 'Sekdes', 'Kaur', 'Kasi', 'Kadus']; @endphp
                                        @foreach($jabs as $jab)
                                            <option value="{{ $jab }}" {{ old('jabatan', $aparatur->jabatan) == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                                        @endforeach
                                    </select>
                                    @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Status Jabatan</label>
                                    <select name="status_jabatan"
                                        class="form-select @error('status_jabatan') is-invalid @enderror" required>
                                        <option value="Aktif" {{ old('status_jabatan', $aparatur->status_jabatan) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Pj" {{ old('status_jabatan', $aparatur->status_jabatan) == 'Pj' ? 'selected' : '' }}>Pj (Pejabat)</option>
                                        <option value="Berakhir" {{ old('status_jabatan', $aparatur->status_jabatan) == 'Berakhir' ? 'selected' : '' }}>Masa Jabatan
                                            Berakhir</option>
                                        <option value="Berhenti" {{ old('status_jabatan', $aparatur->status_jabatan) == 'Berhenti' ? 'selected' : '' }}>
                                            Berhenti/Diberhentikan</option>
                                    </select>
                                    @error('status_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Identitas Pribadi -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold text-teal-700"><i class="fas fa-user-id-badge me-2"></i> Identitas
                                Pribadi</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-medium">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap"
                                        class="form-control @error('nama_lengkap') is-invalid @enderror"
                                        value="{{ old('nama_lengkap', $aparatur->nama_lengkap) }}" required>
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">NIK</label>
                                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                        value="{{ old('nik', $aparatur->nik) }}" maxlength="16" required>
                                    @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Jenis Kelamin</label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jkL"
                                                value="L" {{ old('jenis_kelamin', $aparatur->jenis_kelamin) == 'L' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="jkL">Laki-laki</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jkP"
                                                value="P" {{ old('jenis_kelamin', $aparatur->jenis_kelamin) == 'P' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="jkP">Perempuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Pendidikan Terakhir</label>
                                    <select name="pendidikan_terakhir" class="form-select">
                                        @php $educations = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']; @endphp
                                        @foreach($educations as $edu)
                                            <option value="{{ $edu }}" {{ old('pendidikan_terakhir', $aparatur->pendidikan_terakhir) == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Legalitas (SK) -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold text-teal-700"><i class="fas fa-file-contract me-2"></i> Surat Keputusan
                                (SK) Pengangkatan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Nomor SK</label>
                                    <input type="text" name="nomor_sk"
                                        class="form-control @error('nomor_sk') is-invalid @enderror"
                                        value="{{ old('nomor_sk', $aparatur->nomor_sk) }}" required>
                                    @error('nomor_sk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Tanggal SK</label>
                                    <input type="date" name="tanggal_sk"
                                        class="form-control @error('tanggal_sk') is-invalid @enderror"
                                        value="{{ old('tanggal_sk', $aparatur->tanggal_sk->format('Y-m-d')) }}" required>
                                    @error('tanggal_sk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Tanggal Mulai Menjabat</label>
                                    <input type="date" name="tanggal_mulai"
                                        class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                        value="{{ old('tanggal_mulai', $aparatur->tanggal_mulai->format('Y-m-d')) }}"
                                        required>
                                    @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Tanggal Akhir Jabatan</label>
                                    <input type="date" name="tanggal_akhir"
                                        class="form-control @error('tanggal_akhir') is-invalid @enderror"
                                        value="{{ old('tanggal_akhir', $aparatur->tanggal_akhir ? $aparatur->tanggal_akhir->format('Y-m-d') : '') }}">
                                    @error('tanggal_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-medium">Unggah Berkas Baru <small
                                            class="text-muted">(Biarkan kosong jika tidak ingin ganti SK)</small></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-pdf text-danger"></i></span>
                                        <input type="file" name="dokumen_sk"
                                            class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                                    </div>
                                    <div class="form-text">PDF, Maks 5MB. SK lama akan tetap tersimpan dalam riwayat jika
                                        diganti.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <button type="reset" class="btn btn-light px-4">Reset</button>
                        <button type="submit" class="btn btn-primary px-5 d-flex align-items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection