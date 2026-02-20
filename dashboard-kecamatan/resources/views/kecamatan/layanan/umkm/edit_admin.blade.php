@extends('layouts.kecamatan')

@section('title', 'Koreksi Data UMKM')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.umkm.index') }}"
                class="btn btn-link text-slate-500 text-decoration-none p-0 small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Alert Info -->
                <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
                    <div class="d-flex gap-3">
                        <i class="fas fa-info-circle fa-lg mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Mode Koreksi Admin</h6>
                            <p class="mb-0 small">Kecamatan hanya berwenang memperbaiki data administratif. Perubahan
                                produk, harga, dan foto adalah hak penuh pemilik UMKM.</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                        <h5 class="mb-0 fw-bold fs-6">Koreksi Data Administratif</h5>
                    </div>
                    <form action="{{ route('kecamatan.umkm.update', $umkm->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Nama Usaha / Toko</label>
                                <input type="text" name="nama_usaha" class="form-control bg-slate-50 border-slate-200"
                                    value="{{ old('nama_usaha', $umkm->nama_usaha) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Nama Pemilik</label>
                                <input type="text" name="nama_pemilik" class="form-control bg-slate-50 border-slate-200"
                                    value="{{ old('nama_pemilik', $umkm->nama_pemilik) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-slate-700">Nomor WhatsApp</label>
                                    <input type="text" name="no_wa" class="form-control bg-slate-50 border-slate-200"
                                        value="{{ old('no_wa', $umkm->no_wa) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-slate-700">Asal Desa</label>
                                    <select name="desa" class="form-select bg-slate-50 border-slate-200" required>
                                        @foreach($desas as $desa)
                                            <option value="{{ $desa->nama_desa }}" {{ old('desa', $umkm->desa) == $desa->nama_desa ? 'selected' : '' }}>
                                                {{ $desa->nama_desa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Kategori Usaha</label>
                                <select name="jenis_usaha" class="form-select bg-slate-50 border-slate-200" required>
                                    <option value="Makanan & Minuman" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'Makanan & Minuman' ? 'selected' : '' }}>Makanan & Minuman</option>
                                    <option value="Kerajinan Tangan" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'Kerajinan Tangan' ? 'selected' : '' }}>Kerajinan Tangan</option>
                                    <option value="Fashion & Konveksi" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'Fashion & Konveksi' ? 'selected' : '' }}>Fashion & Konveksi</option>
                                    <option value="Pertanian & Perkebunan" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'Pertanian & Perkebunan' ? 'selected' : '' }}>Pertanian &
                                        Perkebunan</option>
                                    <option value="Jasa" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                    <option value="Lainnya" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <hr class="border-light my-4">

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-700">Tautan Marketplace (Diisi oleh
                                    UMKM)</label>
                                <div class="vstack gap-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-slate-100 border-0 text-xs text-success fw-bold"
                                            style="width: 100px;">Tokopedia</span>
                                        <input type="text" class="form-control bg-slate-50 border-0 text-xs"
                                            value="{{ $umkm->tokopedia_url ?? '-' }}" disabled>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-slate-100 border-0 text-xs text-warning fw-bold"
                                            style="width: 100px;">Shopee</span>
                                        <input type="text" class="form-control bg-slate-50 border-0 text-xs"
                                            value="{{ $umkm->shopee_url ?? '-' }}" disabled>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-slate-100 border-0 text-xs text-dark fw-bold"
                                            style="width: 100px;">TikTok Shop</span>
                                        <input type="text" class="form-control bg-slate-50 border-0 text-xs"
                                            value="{{ $umkm->tiktok_url ?? '-' }}" disabled>
                                    </div>
                                </div>
                                <div class="form-text text-xs mt-1">
                                    <i class="fas fa-info-circle me-1"></i> Admin kecamatan <strong>tidak dapat
                                        mengubah</strong> tautan ini.
                                </div>
                            </div>

                            <div class="mb-0 opacity-50">
                                <label class="form-label small fw-bold text-slate-700">Token Akses (Read-Only)</label>
                                <input type="text" class="form-control bg-slate-100 border-0 text-xs font-monospace"
                                    value="{{ $umkm->manage_token }}" disabled>
                                <div class="form-text text-xs">Untuk mereset token ini, gunakan menu "Reset Akses / Link" di
                                    halaman daftar.</div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-4 text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Pebaikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection