@extends('layouts.kecamatan')

@section('title', 'Bantu Daftarkan UMKM')

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
                <!-- Card Pendaftaran -->
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-gradient-to-r from-teal-600 to-teal-500 py-4 px-4 border-0">
                        <div class="d-flex align-items-center text-white">
                            <div class="bg-white/20 p-3 rounded-circle me-3">
                                <i class="fas fa-hands-helping fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">Fasilitasi Pendaftaran UMKM</h5>
                                <p class="mb-0 small opacity-90">Bantu warga yang kesulitan mendaftar secara mandiri.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 px-4 py-3 border-bottom border-amber-100">
                        <div class="d-flex gap-2">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                            <div class="small text-slate-700">
                                <strong>PENTING:</strong> Anda hanya mendaftarkan akun dasar.
                                Foto produk, harga, dan deskripsi promo <strong>WAJIB diisi sendiri oleh pemilik
                                    UMKM</strong> melalui link yang akan digenerate setelah ini.
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('kecamatan.umkm.store') }}" method="POST">
                        @csrf
                        
                        {{-- Hidden field untuk tracking --}}
                        @if(isset($prefill['from_inbox']) && $prefill['from_inbox'])
                            <input type="hidden" name="from_inbox_id" value="{{ $prefill['from_inbox'] }}">
                            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <i class="fas fa-magic text-info"></i>
                                    <small class="text-slate-700 fw-medium">Data otomatis terisi dari permintaan inbox. Silakan lengkapi informasi yang masih kosong.</small>
                                </div>
                            </div>
                        @endif

                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Nama Usaha / Toko <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama_usaha"
                                    class="form-control bg-slate-50 border-slate-200 @error('nama_usaha') is-invalid @enderror"
                                    placeholder="Contoh: Kripik Pisang Bu Ani" value="{{ old('nama_usaha') }}" required>
                                @error('nama_usaha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Nama Pemilik <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama_pemilik"
                                    class="form-control bg-slate-50 border-slate-200 @error('nama_pemilik') is-invalid @enderror"
                                    placeholder="Nama lengkap sesuai KTP" value="{{ old('nama_pemilik', $prefill['nama_pemilik'] ?? '') }}" required>
                                @error('nama_pemilik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-slate-700">Nomor WhatsApp Aktif <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-slate-100 border-slate-200 text-slate-500 fw-bold">+</span>
                                        <input type="text" name="no_wa"
                                            class="form-control bg-slate-50 border-slate-200 @error('no_wa') is-invalid @enderror"
                                            placeholder="628xxxxxxxxxx" value="{{ old('no_wa', $prefill['no_wa'] ?? '') }}" required>
                                    </div>
                                    <div class="form-text text-xs">Gunakan format 62 (contoh: 62812345678). Link akses
                                        dikirim ke sini.</div>
                                    @error('no_wa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-slate-700">Asal Desa <span
                                            class="text-danger">*</span></label>
                                    <select name="desa"
                                        class="form-select bg-slate-50 border-slate-200 @error('desa') is-invalid @enderror"
                                        required>
                                        <option value="">Pilih Desa...</option>
                                        @foreach($desas as $desa)
                                            <option value="{{ $desa->nama_desa }}" 
                                                {{ old('desa', $prefill['desa'] ?? '') == $desa->nama_desa ? 'selected' : '' }}>
                                                {{ $desa->nama_desa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('desa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Kategori Usaha <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_usaha"
                                    class="form-select bg-slate-50 border-slate-200 @error('jenis_usaha') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Kategori...</option>
                                    <option value="Makanan & Minuman" {{ old('jenis_usaha') == 'Makanan & Minuman' ? 'selected' : '' }}>Makanan & Minuman</option>
                                    <option value="Kerajinan Tangan" {{ old('jenis_usaha') == 'Kerajinan Tangan' ? 'selected' : '' }}>Kerajinan Tangan</option>
                                    <option value="Fashion & Konveksi" {{ old('jenis_usaha') == 'Fashion & Konveksi' ? 'selected' : '' }}>Fashion & Konveksi</option>
                                    <option value="Pertanian & Perkebunan" {{ old('jenis_usaha') == 'Pertanian & Perkebunan' ? 'selected' : '' }}>Pertanian & Perkebunan</option>
                                    <option value="Jasa" {{ old('jenis_usaha') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                    <option value="Lainnya" {{ old('jenis_usaha') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                    </option>
                                </select>
                                @error('jenis_usaha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-slate-700">Catatan Bantuan (Internal)</label>
                                <textarea name="notes" rows="2" class="form-control bg-slate-50 border-slate-200"
                                    placeholder="Opsional, misal: Warga lansia, butuh pendampingan khusus."></textarea>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-4 text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm w-100">
                                <i class="fas fa-paper-plane me-2"></i> Proses Pendaftaran & Dapatkan Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection