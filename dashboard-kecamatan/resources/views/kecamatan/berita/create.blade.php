@extends('layouts.kecamatan')

@section('title', 'Tulis Berita Baru')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.berita.index') }}"
                class="btn btn-link text-slate-500 text-decoration-none p-0 small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <form action="{{ route('kecamatan.berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <!-- Main Editor -->
                    <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                            <h5 class="mb-0 fw-bold fs-6">Konten Berita</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-700">Judul Berita</label>
                                <input type="text" name="judul"
                                    class="form-control form-control-lg bg-slate-50 border-slate-200 fw-bold @error('judul') is-invalid @enderror"
                                    placeholder="Masukkan judul berita yang menarik..." value="{{ old('judul') }}" required>
                                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-700">Ringkasan (Snippet)</label>
                                <textarea name="ringkasan" class="form-control bg-slate-50 border-slate-200" rows="3"
                                    placeholder="Tulis ringkasan singkat untuk tampilan kartu berita...">{{ old('ringkasan') }}</textarea>
                                <div class="text-[10px] text-slate-400 mt-1 italic">Tampil pada daftar berita di halaman
                                    depan.</div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-slate-700">Isi Berita Lengkap</label>
                                <textarea name="konten" id="editor" class="form-control bg-slate-50 border-slate-200"
                                    style="min-height: 400px;"
                                    placeholder="Tuliskan berita lengkap di sini...">{{ old('konten') }}</textarea>
                                @error('konten') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Sidebar Control -->
                    <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                            <h5 class="mb-0 fw-bold fs-6">Publikasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-700">Status</label>
                                <select name="status" class="form-select bg-slate-50 border-slate-200" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Simpan sebagai
                                        Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Terbitkan
                                        Langsung</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-700">Kategori</label>
                                <select name="kategori" class="form-select bg-slate-50 border-slate-200" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Pemerintahan" {{ old('kategori') == 'Pemerintahan' ? 'selected' : '' }}>
                                        Pemerintahan</option>
                                    <option value="Pembangunan" {{ old('kategori') == 'Pembangunan' ? 'selected' : '' }}>
                                        Pembangunan</option>
                                    <option value="Ekonomi" {{ old('kategori') == 'Ekonomi' ? 'selected' : '' }}>Ekonomi
                                    </option>
                                    <option value="Sosial" {{ old('kategori') == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                                    <option value="Kesehatan" {{ old('kategori') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan
                                    </option>
                                    <option value="Pendidikan" {{ old('kategori') == 'Pendidikan' ? 'selected' : '' }}>
                                        Pendidikan</option>
                                    <option value="Umum" {{ old('kategori') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-slate-700">Waktu Terbit (Opsional)</label>
                                <input type="datetime-local" name="published_at"
                                    class="form-control bg-slate-50 border-slate-200" value="{{ old('published_at') }}">
                                <div class="text-[10px] text-slate-400 mt-1 italic">Kosongkan untuk otomatis saat ini.</div>
                            </div>
                        </div>
                        <div class="card-footer bg-slate-50 border-0 p-3">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Berita
                            </button>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                            <h5 class="mb-0 fw-bold fs-6">Gambar Utama</h5>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <div id="image-preview"
                                    class="image-preview-container bg-slate-100 rounded-4 border-2 border-dashed border-slate-200 d-flex align-items-center justify-content-center overflow-hidden"
                                    style="aspect-ratio: 16/9; cursor: pointer;">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-slate-300"></i>
                                    <img id="preview-src" src="#" alt="Preview" class="d-none w-100 h-100 object-cover">
                                </div>
                            </div>
                            <input type="file" name="thumbnail" id="thumbnail-input" class="d-none"
                                accept="image/jpeg,image/png,image/jpg,image/webp">
                            <button type="button" onclick="document.getElementById('thumbnail-input').click()"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                Pilih Gambar
                            </button>
                            @error('thumbnail') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            <div class="text-[10px] text-slate-400 mt-2">Rekomendasi: 1280x720px (Max 2MB).</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.getElementById('thumbnail-input').onchange = evt => {
                const [file] = evt.target.files
                if (file) {
                    const preview = document.getElementById('preview-src');
                    const placeholder = document.querySelector('.image-preview-container i');
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                }
            }
        </script>
    @endpush
@endsection