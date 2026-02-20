@extends('layouts.desa')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Registrasi Relawan Baru</h4>
                        </div>
                        <a href="{{ route('desa.trantibum.relawan.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('desa.trantibum.relawan.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <h6 class="fw-bold text-primary mb-3">Biodata Personil</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" required
                                        placeholder="Nama sesuai KTP">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">NIK</label>
                                    <input type="text" name="nik" class="form-control" placeholder="16 digit NIK">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. HP / WhatsApp</label>
                                    <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 0812...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">Jabatan</label>
                                    <select name="jabatan" class="form-select" required>
                                        <option value="Anggota">Anggota</option>
                                        <option value="Ketua">Ketua Tim</option>
                                        <option value="Koordinator">Koordinator Lapangan</option>
                                        <option value="Sekretaris">Sekretaris</option>
                                        <option value="Bendahara">Bendahara</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Alamat Domisili</label>
                                    <textarea name="alamat" class="form-control" rows="2"
                                        placeholder="Alamat lengkap..."></textarea>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <h6 class="fw-bold text-primary mb-3">Dokumen & Identitas Visual</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Foto Profil</label>
                                    <input type="file" name="foto" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG. Max: 2MB.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">File SK Pengangkatan</label>
                                    <input type="file" name="sk_file" class="form-control" accept=".pdf">
                                    <small class="text-muted">Format: PDF. Max: 5MB.</small>
                                </div>
                            </div>

                            <div class="text-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-save me-1"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection