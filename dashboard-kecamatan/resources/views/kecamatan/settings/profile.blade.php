@extends('layouts.kecamatan')

@section('title', 'Pengaturan Profil Aplikasi')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="content-header mb-4">
            <div class="header-title">
                <h1 class="text-slate-900 fw-bold display-6">Pengaturan Pusat</h1>
                <p class="text-slate-500 fs-5 mb-0">Kelola identitas dan branding wilayah secara terpusat.</p>
                <div class="header-accent"></div>
            </div>
        </div>

        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    borderRadius: '1rem'
                });
            </script>
            <div class="alert alert-emerald border-0 shadow-sm rounded-4 p-4 mb-4 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box icon-box-emerald sm">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-emerald-900">Berhasil!</h6>
                        <p class="mb-0 text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($errors) && is_object($errors) && $errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 animate__animated animate__shakeX">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box icon-box-danger sm">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-danger">Terjadi Kesalahan!</h6>
                        <ul class="mb-0 text-danger small ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2 text-slate-900">
                            <i class="fas fa-cog text-primary"></i>
                            <h5 class="mb-0 fw-bold">Konfigurasi Identitas & Media</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.settings.profile.update') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-semibold">Nama Branding Utama</label>
                                    <input type="text" name="app_name" value="{{ old('app_name', $profile->app_name) }}"
                                        class="form-control form-control-lg bg-slate-50 border-slate-200 rounded-3"
                                        placeholder="Contoh: Dashboard SAE" required>
                                    <div class="form-text text-[11px] text-slate-400 mt-1">Muncul sebagai Judul Tab Browser
                                        dan Branding Sistem.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-semibold">Nama Resmi Wilayah</label>
                                    <input type="text" name="region_name"
                                        value="{{ old('region_name', $profile->region_name) }}"
                                        class="form-control form-control-lg bg-slate-50 border-slate-200 rounded-3"
                                        placeholder="Contoh: Nama Wilayah Anda" required>
                                    <div class="form-text text-[11px] text-slate-400 mt-1">Muncul sebagai identitas wilayah
                                        pada sidebar dan laporan.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-semibold">Tingkat Wilayah</label>
                                    <select name="region_level"
                                        class="form-select form-select-lg bg-slate-50 border-slate-200 rounded-3" required>
                                        <option value="desa" {{ $profile->region_level == 'desa' ? 'selected' : '' }}>Desa
                                        </option>
                                        <option value="kecamatan" {{ $profile->region_level == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                        <option value="kabupaten" {{ $profile->region_level == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-slate-700 fw-semibold">Tagline / Slogan</label>
                                    <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}"
                                        class="form-control form-control-lg bg-slate-50 border-slate-200 rounded-3">
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-2 text-center border-end border-slate-200">
                                                @if($profile->logo_path)
                                                    <img src="{{ asset('storage/' . $profile->logo_path) }}"
                                                        class="img-fluid mb-3 d-block mx-auto"
                                                        style="max-height: 120px; width: auto; max-width: 100%; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">
                                                @else
                                                    <div class="p-3 rounded-3 mb-3 d-inline-block">
                                                        <i class="fas fa-landmark text-slate-200 fa-3x"></i>
                                                    </div>
                                                @endif
                                                <span class="d-block text-[10px] fw-bold text-slate-400 uppercase">Logo
                                                    Sistem</span>
                                            </div>
                                            <div class="col-md-10 ps-md-4">
                                                <label class="form-label text-slate-700 fw-semibold">Ganti Logo
                                                    Wilayah</label>
                                                <input type="file" name="logo_path"
                                                    class="form-control bg-white border-slate-200 rounded-3 @error('logo_path') is-invalid @enderror">
                                                @error('logo_path')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                <p class="text-[11px] text-slate-400 mt-2 mb-0 italic">Format JPG/PNG,
                                                    maksimal 2MB.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Landing Page Visuals -->
                                <div class="col-md-12 mt-4">
                                    <div class="p-4 border border-teal-100 bg-teal-50 bg-opacity-30 rounded-4">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <i class="fas fa-images text-teal-600"></i>
                                            <h6 class="mb-0 fw-bold text-teal-900 border-bottom border-teal-200 pb-1">Media
                                                Visual Landing Page</h6>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 small fw-bold">Visual UMKM</label>
                                                @if($profile->image_umkm)
                                                    <div
                                                        class="mb-2 rounded-3 overflow-hidden shadow-sm position-relative ratio ratio-16x9">
                                                        <img src="{{ asset('storage/' . $profile->image_umkm) }}"
                                                            class="object-fit-cover w-100 h-100" alt="Visual UMKM">
                                                    </div>
                                                @endif
                                                <input type="file" name="image_umkm"
                                                    class="form-control form-control-sm border-slate-200">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 small fw-bold">Visual
                                                    Pariwisata</label>
                                                @if($profile->image_pariwisata)
                                                    <div
                                                        class="mb-2 rounded-3 overflow-hidden shadow-sm position-relative ratio ratio-16x9">
                                                        <img src="{{ asset('storage/' . $profile->image_pariwisata) }}"
                                                            class="object-fit-cover w-100 h-100" alt="Visual Pariwisata">
                                                    </div>
                                                @endif
                                                <input type="file" name="image_pariwisata"
                                                    class="form-control form-control-sm border-slate-200">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 small fw-bold">Visual Festival
                                                    Budaya</label>
                                                @if($profile->image_festival)
                                                    <div
                                                        class="mb-2 rounded-3 overflow-hidden shadow-sm position-relative ratio ratio-16x9">
                                                        <img src="{{ asset('storage/' . $profile->image_festival) }}"
                                                            class="object-fit-cover w-100 h-100" alt="Visual Festival">
                                                    </div>
                                                @endif
                                                <input type="file" name="image_festival"
                                                    class="form-control form-control-sm border-slate-200">
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-teal-700 mt-3 mb-0">
                                            <i class="fas fa-info-circle me-1"></i> Visual ini ditampilkan pada bagian
                                            "Potensi Wilayah". Rekomendasi 5MB/file.
                                        </p>
                                    </div>
                                </div>

                                <!-- Pengaturan Visibilitas Menu Landing Page -->
                                <div class="col-md-12 mt-4">
                                    <div class="p-4 border border-slate-200 bg-slate-50 rounded-4">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <i class="fas fa-eye text-slate-600"></i>
                                            <h6 class="mb-0 fw-bold text-slate-900 border-bottom border-slate-200 pb-1">
                                                Kontrol
                                                Visibilitas Menu Landing Page</h6>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div
                                                    class="p-3 bg-white border border-slate-200 rounded-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div
                                                            class="avatar-sm bg-orange-100 text-orange-600 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-bullhorn"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-slate-900">Menu Pengaduan</h6>
                                                            <small class="text-slate-500 text-[10px]">Aktifkan form lapor
                                                                warga.</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="is_menu_pengaduan_active" {{ $profile->is_menu_pengaduan_active ? 'checked' : '' }}
                                                            style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="p-3 bg-white border border-slate-200 rounded-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div
                                                            class="avatar-sm bg-emerald-100 text-emerald-600 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-store"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-slate-900">Menu UMKM & Loker</h6>
                                                            <small class="text-slate-500 text-[10px]">Aktifkan etalase
                                                                potensi
                                                                wilayah.</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="is_menu_umkm_active" {{ $profile->is_menu_umkm_active ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="p-3 bg-white border border-slate-200 rounded-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div
                                                            class="avatar-sm bg-blue-100 text-blue-600 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-file-alt"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-slate-900">Menu Pelayanan</h6>
                                                            <small class="text-slate-500 text-[10px]">Aktifkan halaman
                                                                layanan dan persyaratan.</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="is_menu_pelayanan_active" {{ $profile->is_menu_pelayanan_active ?? true ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="p-3 bg-white border border-slate-200 rounded-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div
                                                            class="avatar-sm bg-purple-100 text-purple-600 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-slate-900">Menu Statistik</h6>
                                                            <small class="text-slate-500 text-[10px]">Aktifkan halaman
                                                                statistik kependudukan.</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="is_menu_statistik_active" {{ $profile->is_menu_statistik_active ?? true ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="p-3 bg-white border border-slate-200 rounded-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div
                                                            class="avatar-sm bg-rose-100 text-rose-600 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-newspaper"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-slate-900">Menu Berita</h6>
                                                            <small class="text-slate-500 text-[10px]">Aktifkan berita dan publikasi wilayah.</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="is_menu_berita_active" {{ $profile->is_menu_berita_active ?? true ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- AI Assistant Toggle -->
                                            <div class="col-md-12">
                                                <div
                                                    class="p-3 bg-primary bg-opacity-5 border border-primary border-opacity-10 rounded-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div
                                                            class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-robot"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-primary">Asisten AI Cerdas</h6>
                                                            <small class="text-slate-500 text-[10px]">Admin bisa menggunakan
                                                                AI untuk memori agar lebih sempurna.</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_ai_active"
                                                            value="1" {{ $profile->is_ai_active ? 'checked' : '' }}
                                                            style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Konfigurasi Koordinat Peta (NEW) -->
                                <div class="col-md-12 mt-4">
                                    <div class="p-4 border border-blue-100 bg-blue-50 bg-opacity-30 rounded-4">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                                            <h6 class="mb-0 fw-bold text-blue-900 border-bottom border-blue-200 pb-1">
                                                Konfigurasi Koordinat Peta (Pusat Wilayah)</h6>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 small fw-bold">Latitude (Garis
                                                    Lintang)</label>
                                                <input type="number" step="any" name="map_latitude"
                                                    value="{{ old('map_latitude', $profile->map_latitude ?? -7.78) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="Contoh: -7.78000000">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 small fw-bold">Longitude (Garis
                                                    Bujur)</label>
                                                <input type="number" step="any" name="map_longitude"
                                                    value="{{ old('map_longitude', $profile->map_longitude ?? 113.47) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="Contoh: 113.47000000">
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-blue-700 mt-3 mb-0">
                                            <i class="fas fa-info-circle me-1"></i> Koordinat ini digunakan untuk menentukan
                                            titik pusat peta saat pertama kali dibuka di halaman depan.
                                        </p>
                                    </div>
                                </div>

                                <!-- Konfigurasi Hero Section (Beranda) - GABUNGAN -->
                                <div class="col-md-12 mt-5">
                                    <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                                        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-desktop text-tertiary"></i>
                                                    <h6 class="mb-0 fw-bold text-slate-800">Konfigurasi Hero Section
                                                        (Beranda)</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">

                                            <!-- Tokoh Utama / Pimpinan -->
                                            <div class="mb-5">
                                                <div class="d-flex align-items-center justify-content-between mb-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fas fa-user-tie text-primary"></i>
                                                        <span
                                                            class="text-xs font-black text-slate-400 uppercase tracking-widest">Tokoh
                                                            Utama / Pimpinan</span>
                                                    </div>
                                                    <div class="form-check form-switch d-flex align-items-center gap-2">
                                                        <input class="form-check-input" type="checkbox" id="heroActive"
                                                            name="hero_image_active" {{ isset($profile) && $profile->hero_image_active ? 'checked' : '' }}
                                                            style="width: 2.5em; height: 1.25em;">
                                                        <label class="form-check-label fw-bold small text-slate-600"
                                                            for="heroActive">Tampilkan</label>
                                                    </div>
                                                </div>

                                                <div class="row g-4 align-items-center">
                                                    <div class="col-md-4">
                                                        <div
                                                            class="bg-white p-2 rounded-4 shadow-sm border border-slate-100 text-center">
                                                            @if(isset($profile) && $profile->hero_image_path)
                                                                <img src="{{ asset('storage/' . $profile->hero_image_path) }}"
                                                                    class="img-fluid rounded-4 w-100 object-fit-contain bg-slate-50"
                                                                    style="max-height: 200px;" alt="Leader Preview">
                                                            @else
                                                                <div class="bg-slate-50 rounded-4 d-flex flex-column align-items-center justify-content-center"
                                                                    style="height: 180px;">
                                                                    <i class="fas fa-user-slash fa-2x text-slate-200 mb-2"></i>
                                                                    <span
                                                                        class="text-[10px] fw-bold text-slate-400 uppercase">Input
                                                                        Foto</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label text-slate-600 small fw-bold">Upload
                                                                Foto Hero</label>
                                                            <input type="file" name="hero_image_path"
                                                                class="form-control bg-slate-50 border-slate-200 rounded-3 text-sm"
                                                                accept="image/jpeg,image/jpg,image/png,image/webp">
                                                            <div class="form-text text-[10px] text-slate-400 mt-1 italic">
                                                                Format: JPG, JPEG, PNG, WebP. Maksimal 2MB.
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="form-label text-slate-600 small fw-bold">Nama Pimpinan</label>
                                                            <input type="text" name="leader_name"
                                                                value="{{ old('leader_name', $profile->leader_name) }}"
                                                                class="form-control bg-slate-50 border-slate-200 rounded-3 text-sm mb-3"
                                                                placeholder="Contoh: Bpk. H. Ahmad Fauzi">

                                                            <label class="form-label text-slate-600 small fw-bold">Jabatan / Gelar</label>
                                                            <input type="text" name="leader_title"
                                                                value="{{ old('leader_title', $profile->leader_title) }}"
                                                                class="form-control bg-slate-50 border-slate-200 rounded-3 text-sm mb-3"
                                                                placeholder="Contoh: Camat {{ $profile->region_name ?? 'Wilayah' }}">

                                                            <label class="form-label text-slate-600 small fw-bold">Keterangan Foto (Alt Text)</label>
                                                            <input type="text" name="hero_image_alt"
                                                                value="{{ old('hero_image_alt', $profile->hero_image_alt) }}"
                                                                class="form-control bg-slate-50 border-slate-200 rounded-3 text-sm"
                                                                placeholder="Contoh: Bpk. Camat {{ $profile->region_name ?? 'Wilayah' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="border-top border-light my-5"></div>

                                            <!-- Background Pemandangan -->
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-4">
                                                    <i class="fas fa-image text-emerald-500"></i>
                                                    <span
                                                        class="text-xs font-black text-slate-400 uppercase tracking-widest">Background
                                                        Pemandangan</span>
                                                </div>

                                                <div class="row g-4">
                                                    <div class="col-md-4">
                                                        <div
                                                            class="position-relative rounded-4 overflow-hidden border border-slate-200 shadow-sm">
                                                            @if(isset($profile) && $profile->hero_bg_path)
                                                                <img src="{{ asset('storage/' . $profile->hero_bg_path) }}"
                                                                    class="w-100 h-100 object-fit-cover"
                                                                    style="min-height: 140px;" alt="BG Preview">
                                                            @else
                                                                <div class="bg-slate-100 w-100 d-flex flex-column align-items-center justify-content-center"
                                                                    style="height: 140px;">
                                                                    <i class="fas fa-mountain text-slate-300 fa-2x mb-2"></i>
                                                                </div>
                                                            @endif
                                                            <div
                                                                class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-70 py-1.5 text-center">
                                                                <span
                                                                    class="text-[10px] font-black text-white uppercase tracking-widest">Preview</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="mb-4">
                                                            <label class="form-label text-slate-600 small fw-bold">Upload
                                                                Pemandangan Desa</label>
                                                            <input type="file" name="hero_bg_path"
                                                                class="form-control bg-slate-50 border-slate-200 rounded-3 text-sm">
                                                        </div>

                                                        <div class="row g-4">
                                                            <div class="col-6">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span
                                                                        class="text-[11px] font-bold text-slate-600 uppercase tracking-tighter">Transparansi</span>
                                                                    <span class="text-xs font-black text-primary"
                                                                        id="opacityValue">{{ $profile->hero_bg_opacity ?? 10 }}%</span>
                                                                </div>
                                                                <input type="range" name="hero_bg_opacity"
                                                                    class="form-range" min="0" max="100"
                                                                    value="{{ $profile->hero_bg_opacity ?? 10 }}"
                                                                    oninput="document.getElementById('opacityValue').innerText = this.value + '%'">
                                                            </div>
                                                            <div class="col-6">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span
                                                                        class="text-[11px] font-bold text-slate-600 uppercase tracking-tighter">Efek
                                                                        Blur</span>
                                                                    <span class="text-xs font-black text-primary"
                                                                        id="blurValue">{{ $profile->hero_bg_blur ?? 0 }}px</span>
                                                                </div>
                                                                <input type="range" name="hero_bg_blur" class="form-range"
                                                                    min="0" max="20"
                                                                    value="{{ $profile->hero_bg_blur ?? 0 }}"
                                                                    oninput="document.getElementById('blurValue').innerText = this.value + 'px'">
                                                            </div>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400 mt-3 mb-0">Pengaturan ini
                                                            memastikan teks tetap terbaca jelas di atas gambar.</p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Kontak & Jejaring Sosial (NEW) -->
                                <div class="col-md-12 mt-4">
                                    <div class="p-4 border border-blue-100 bg-blue-50 bg-opacity-30 rounded-4">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <i class="fas fa-address-book text-blue-600"></i>
                                            <h6 class="mb-0 fw-bold text-blue-900 border-bottom border-blue-200 pb-1">Kontak
                                                & Jejaring Sosial</h6>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-12">
                                                <label class="form-label text-slate-700 fw-semibold">Alamat Kantor
                                                    Resmi</label>
                                                <textarea name="address"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    rows="2"
                                                    placeholder="Contoh: Jl. Raya Utama No. 1, {{ $profile->region_name ?? 'Wilayah' }}, Kab. Probolinggo">{{ old('address', $profile->address) }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 fw-semibold">Nomor Telepon
                                                    Kantor/Layanan</label>
                                                <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="(0335) 123456">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 fw-semibold">WhatsApp Pengaduan
                                                    (Lapor!)</label>
                                                <input type="text" name="whatsapp_complaint"
                                                    value="{{ old('whatsapp_complaint', $profile->whatsapp_complaint) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="08123456789">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 fw-semibold text-primary">Nomor
                                                    WhatsApp Bot</label>
                                                <input type="text" name="whatsapp_bot_number"
                                                    value="{{ old('whatsapp_bot_number', $profile->whatsapp_bot_number) }}"
                                                    class="form-control bg-white border-primary border-opacity-25 rounded-3 text-sm"
                                                    placeholder="08xxxxxxxxxx">
                                                <div class="form-text text-[10px] text-primary text-opacity-75 mt-1 small">
                                                    Gunakan nomor yang terhubung ke bot WAHA.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 fw-semibold">Buka: Senin -
                                                    Kamis</label>
                                                <input type="text" name="office_hours_mon_thu"
                                                    value="{{ old('office_hours_mon_thu', $profile->office_hours_mon_thu) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="08:00 - 15:30 WIB">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-700 fw-semibold">Buka: Jumat</label>
                                                <input type="text" name="office_hours_fri"
                                                    value="{{ old('office_hours_fri', $profile->office_hours_fri) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="08:00 - 11:30 WIB">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-slate-400 fw-semibold">Sabtu - Minggu</label>
                                                <input type="text"
                                                    class="form-control bg-slate-100 border-slate-200 rounded-3 text-sm text-slate-400"
                                                    value="Libur" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 fw-semibold">Link Instagram</label>
                                                <input type="url" name="instagram_url"
                                                    value="{{ old('instagram_url', $profile->instagram_url) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="https://instagram.com/{{ Str::slug($profile->region_name ?? 'wilayah') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 fw-semibold">Link Facebook</label>
                                                <input type="url" name="facebook_url"
                                                    value="{{ old('facebook_url', $profile->facebook_url) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="https://facebook.com/{{ Str::slug($profile->region_name ?? 'wilayah') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 fw-semibold">Link YouTube</label>
                                                <input type="url" name="youtube_url"
                                                    value="{{ old('youtube_url', $profile->youtube_url) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="https://youtube.com/@{{ Str::slug($profile->region_name ?? 'wilayah') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-slate-700 fw-semibold">Link X
                                                    (Twitter)</label>
                                                <input type="url" name="x_url" value="{{ old('x_url', $profile->x_url) }}"
                                                    class="form-control bg-white border-slate-200 rounded-3 text-sm"
                                                    placeholder="https://x.com/{{ Str::slug($profile->region_name ?? 'wilayah') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-5 pt-3 border-top border-light">
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3 fw-bold shadow-sm"
                                    style="background-color: #4f46e5; border-color: #4f46e5; color: white;">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mt-4 mt-xl-0">
                <div class="alert bg-primary bg-opacity-10 border-0 p-4 rounded-4 shadow-sm mb-4">
                    <h6 class="fw-bold text-primary mb-3">Instruksi Owner Sistem</h6>
                    <p class="text-primary text-opacity-75 small mb-0">
                        Pastikan data Branding Wilayah sudah sesuai dengan SK. Perubahan akan segera berdampak pada seluruh
                        etalase publik (Landing Page & Portal).
                    </p>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-900 mb-3">Audit Log Terakhir</h6>
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-history text-slate-400 mt-1"></i>
                            <div>
                                <p class="small text-slate-600 mb-1">Terakhir diperbarui oleh:</p>
                                <p class="fw-bold text-slate-900 mb-0">
                                    {{ $profile->editor ? $profile->editor->nama_lengkap : 'Sistem' }}
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $profile->updated_at ? $profile->updated_at->format('d M Y, H:i') : '-' }} WIB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection