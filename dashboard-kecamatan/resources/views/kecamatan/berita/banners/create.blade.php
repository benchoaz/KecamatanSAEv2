@extends('layouts.kecamatan')

@section('title', 'Tambah Banner Iklan Baru')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('kecamatan.berita.index', ['tab' => 'banners']) }}" class="btn btn-light rounded-3 me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold text-slate-800 mb-0">Tambah Banner Iklan</h4>
                        <p class="text-slate-500 small mb-0">Unggah banner promosi untuk ditampilkan di portal berita.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-premium rounded-4">
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.berita.banners.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-700">Judul / Label Banner</label>
                                <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror" 
                                    placeholder="Contoh: Banner Promo UMKM Desa" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-700">Gambar Banner</label>
                                <div class="bg-slate-50 p-4 rounded-4 border-2 border-dashed border-slate-200 text-center mb-2">
                                    <div id="image-preview-container" class="mb-3 d-none">
                                        <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded-3 shadow-sm mx-auto" style="max-height: 200px;">
                                    </div>
                                    <div id="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-slate-300 mb-3"></i>
                                        <p class="text-slate-500 small mb-0">Klik atau seret gambar ke sini</p>
                                        <p class="text-slate-400 text-[10px]">Rekomendasi rasio 4:1 atau 3:1 (Maks. 2MB)</p>
                                    </div>
                                    <input type="file" name="image" id="banner-image" class="form-control d-none" accept="image/*" required>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-3 px-4 rounded-pill" onclick="document.getElementById('banner-image').click()">
                                        Pilih Gambar
                                    </button>
                                </div>
                                @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-700">Tautan Tujuan (Link URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-slate-50 border-end-0 text-slate-400"><i class="fas fa-link"></i></span>
                                    <input type="url" name="link_url" class="form-control rounded-3 border-start-0 @error('link_url') is-invalid @enderror" 
                                        placeholder="https://contoh.com/halaman-tujuan" value="{{ old('link_url') }}">
                                </div>
                                <div class="form-text small">Opsional. Pengunjung akan diarahkan ke link ini saat mengklik banner.</div>
                                @error('link_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Prioritas Tampilan</label>
                                    <input type="number" name="priority" class="form-control rounded-3" value="0" min="0">
                                    <div class="form-text small">Angka lebih tinggi akan muncul lebih atas.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                        <label class="form-check-label fw-bold text-slate-700 ms-2" for="isActive">Aktifkan Sekarang</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-slate-100">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('kecamatan.berita.index', ['tab' => 'banners']) }}" class="btn btn-light px-4 rounded-3">Batal</a>
                                <button type="submit" class="btn btn-primary px-5 rounded-3 fw-bold">Simpan Banner</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('banner-image').onchange = function (evt) {
            const [file] = this.files
            if (file) {
                document.getElementById('image-preview').src = URL.createObjectURL(file)
                document.getElementById('image-preview-container').classList.remove('d-none')
                document.getElementById('upload-placeholder').classList.add('d-none')
            }
        }
    </script>
@endsection
