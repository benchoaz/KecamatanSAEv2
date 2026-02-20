@extends('layouts.kecamatan')

@php
    $isEdit = isset($umkm);
    $title = $isEdit ? 'Ubah Data UMKM' : 'Tambah UMKM Baru';
@endphp

@section('title', $title)

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.umkm.index') }}"
                class="btn btn-link text-slate-500 text-decoration-none p-0 small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                        <h5 class="mb-0 fw-bold fs-6">{{ $title }}</h5>
                    </div>
                    <form action="{{ $isEdit ? route('kecamatan.umkm.update', $umkm->id) : route('kecamatan.umkm.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Nama UMKM</label>
                                    <input type="text" name="name"
                                        class="form-control bg-slate-50 border-slate-200 @error('name') is-invalid @enderror"
                                        placeholder="Contoh: Keripik Singkong Barokah"
                                        value="{{ old('name', $umkm->name ?? '') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Produk Utama</label>
                                    <input type="text" name="product"
                                        class="form-control bg-slate-50 border-slate-200 @error('product') is-invalid @enderror"
                                        placeholder="Contoh: Makanan Ringan"
                                        value="{{ old('product', $umkm->product ?? '') }}" required>
                                    @error('product') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Harga Produk (Rp)</label>
                                    <input type="number" name="price" 
                                        class="form-control bg-slate-50 border-slate-200 @error('price') is-invalid @enderror"
                                        placeholder="Contoh: 25000" 
                                        value="{{ old('price', $umkm->price ?? '') }}">
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Harga Coret / Asli (Opsional)</label>
                                    <input type="number" name="original_price" 
                                        class="form-control bg-slate-50 border-slate-200 @error('original_price') is-invalid @enderror"
                                        placeholder="Contoh: 30000" 
                                        value="{{ old('original_price', $umkm->original_price ?? '') }}">
                                    @error('original_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">WhatsApp (Format: 62xx)</label>
                                    <input type="text" name="contact_wa"
                                        class="form-control bg-slate-50 border-slate-200 @error('contact_wa') is-invalid @enderror"
                                        placeholder="Contoh: 628123456789"
                                        value="{{ old('contact_wa', $umkm->contact_wa ?? '') }}" required>
                                    @error('contact_wa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Status Publikasi</label>
                                    <select name="is_active" class="form-select bg-slate-50 border-slate-200">
                                        <option value="1" {{ old('is_active', $umkm->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('is_active', $umkm->is_active ?? 1) == 0 ? 'selected' : '' }}>Non-aktif</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Produk Unggulan?</label>
                                    <select name="is_featured" class="form-select bg-slate-50 border-slate-200">
                                        <option value="0" {{ old('is_featured', $umkm->is_featured ?? 0) == 0 ? 'selected' : '' }}>Biasa</option>
                                        <option value="1" {{ old('is_featured', $umkm->is_featured ?? 0) == 1 ? 'selected' : '' }}>Unggulan (Muncul di Depan)</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Deskripsi Produk</label>
                                    <textarea name="description" rows="3" 
                                        class="form-control bg-slate-50 border-slate-200 @error('description') is-invalid @enderror"
                                        placeholder="Ceritakan keunggulan produk ini...">{{ old('description', $umkm->description ?? '') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 mb-0">
                                    <label class="form-label small fw-bold text-slate-700">Foto Produk/Toko</label>
                                    <div class="d-flex align-items-center gap-4">
                                        <div id="image-preview"
                                            class="bg-slate-100 rounded-4 border-2 border-dashed border-slate-200 d-flex align-items-center justify-content-center overflow-hidden"
                                            style="width: 150px; height: 100px; cursor: pointer;"
                                            onclick="document.getElementById('image_path').click()">
                                            @if($isEdit && $umkm->image_path)
                                                <img id="preview-src" src="{{ asset('storage/' . $umkm->image_path) }}"
                                                    class="w-100 h-100 object-cover">
                                            @else
                                                <i class="fas fa-camera fa-2x text-slate-300"></i>
                                                <img id="preview-src" src="#" alt="Preview"
                                                    class="d-none w-100 h-100 object-cover">
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" name="image_path" id="image_path"
                                                class="form-control border-slate-200" accept="image/*"
                                                onchange="previewImage(this)">
                                            <p class="text-[10px] text-slate-400 mt-2 mb-0">Format: JPG, PNG, WEBP. Maksimal
                                                2MB.</p>
                                        </div>
                                    </div>
                                    @error('image_path') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-slate-50 border-0 p-4 text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Perbarui Data' : 'Simpan Data' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(input) {
                const preview = document.getElementById('preview-src');
                const placeholder = document.querySelector('#image-preview i');

                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                        if (placeholder) placeholder.classList.add('d-none');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection