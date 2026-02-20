@extends('layouts.desa')

@section('title', 'Input Musdes Baru')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('desa.musdes.index') }}" class="btn btn-light rounded-circle shadow-sm me-3 border">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0">Input Musyawarah Desa</h4>
                    <p class="text-muted small mb-0">Isi data awal kegiatan, upload dokumen dilakukan di tahap selanjutnya.
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('desa.musdes.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Kegiatan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control form-control-lg bg-light"
                                value="{{ old('judul', 'Musyawarah Desa') }}" placeholder="Contoh: Musdes RKP Desa 2026">
                            @error('judul') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Jenis Musdes <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_musdes" class="form-select form-select-lg bg-light">
                                    <option value="">Pilih Jenis...</option>
                                    <option value="Musdes Perencanaan" {{ old('jenis_musdes') == 'Musdes Perencanaan' ? 'selected' : '' }}>Musdes Perencanaan</option>
                                    <option value="Musdes RKP Desa" {{ old('jenis_musdes') == 'Musdes RKP Desa' ? 'selected' : '' }}>Musdes RKP Desa</option>
                                    <option value="Musdes APBDes" {{ old('jenis_musdes') == 'Musdes APBDes' ? 'selected' : '' }}>Musdes APBDes</option>
                                    <option value="Musdes Khusus" {{ old('jenis_musdes') == 'Musdes Khusus' ? 'selected' : '' }}>Musdes Khusus</option>
                                </select>
                                @error('jenis_musdes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Tanggal
                                            Pelaksanaan <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_pelaksanaan"
                                            class="form-control form-control-lg bg-light"
                                            value="{{ old('tanggal_pelaksanaan', date('Y-m-d')) }}">
                                        @error('tanggal_pelaksanaan') <div class="text-danger small mt-1">{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Rerata
                                            Undangan</label>
                                        <input type="number" name="jumlah_undangan"
                                            class="form-control form-control-lg bg-light"
                                            value="{{ old('jumlah_undangan', 15) }}" min="1">
                                        @error('jumlah_undangan') <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Tahun Anggaran
                                            <span class="text-danger">*</span></label>
                                        <select name="periode" class="form-select form-select-lg bg-light">
                                            @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                                <option value="{{ $i }}" {{ old('periode') == $i ? 'selected' : '' }}>{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('periode') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Lokasi Kegiatan
                                    <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control form-control-lg bg-light"
                                    value="{{ old('lokasi', 'Balai Desa') }}" placeholder="Contoh: Balai Desa Suka Maju">
                                @error('lokasi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Keterangan
                                    Singkat</label>
                                <textarea name="keterangan" class="form-control bg-light" rows="3"
                                    placeholder="Tambahkan catatan jika perlu (opsional)...">{{ old('keterangan') }}</textarea>
                            </div>

                            <hr class="my-4 text-slate-200">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('desa.musdes.index') }}"
                                    class="btn btn-light px-4 fw-bold text-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill">
                                    Lanjut ke Upload Dokumen <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection