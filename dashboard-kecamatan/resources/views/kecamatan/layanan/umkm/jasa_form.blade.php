@extends('layouts.kecamatan')

@section('title', 'Bantu Daftarkan Jasa Warga (Fasilitator)')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" class="text-decoration-none text-slate-500 small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Manajemen
            </a>
            <h4 class="fw-bold text-slate-800 mt-2">Bantu Daftarkan Layanan Jasa</h4>
            <p class="text-slate-500 small">Formulir pendaftaran untuk warga yang memiliki keahlian/jasa namun kesulitan mendaftar mandiri.</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                    <div class="card-header bg-indigo-600 text-white p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-3 d-flex align-items-center justify-content-center text-xl shadow-sm">
                                <i class="fas fa-user-gear"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Input Data Jasa / Keahlian</h6>
                                <p class="text-indigo-100 text-[10px] mb-0 uppercase tracking-widest font-black">Fasilitasi Petugas</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.jasa.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="from_inbox" value="{{ $prefill['from_inbox'] }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Nama Keahlian / Judul Jasa</label>
                                    <input type="text" name="job_title" class="form-control border-slate-200 rounded-3" placeholder="Contoh: Tukang Servis Pompa" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Nama Personil / Penyedia</label>
                                    <input type="text" name="display_name" value="{{ $prefill['display_name'] }}" class="form-control border-slate-200 rounded-3" placeholder="Nama Warga" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Kategori Layanan</label>
                                    <select name="job_category" class="form-select border-slate-200 rounded-3" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Tipe Penawaran</label>
                                    <select name="job_type" class="form-select border-slate-200 rounded-3" required>
                                        <option value="jasa">Jasa / Servis</option>
                                        <option value="transportasi">Transportasi / Driver</option>
                                        <option value="keliling">Pedagang Keliling</option>
                                        <option value="harian">Pekerja Harian</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">No. WhatsApp Aktif (Format: 628...)</label>
                                    <input type="text" name="contact_phone" value="{{ $prefill['contact_phone'] }}" class="form-control border-slate-200 rounded-3" placeholder="628..." required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Alamat / Wilayah Operasi (Desa)</label>
                                    <select name="service_area" class="form-select border-slate-200 rounded-3" required>
                                        <option value="">-- Pilih Desa --</option>
                                        @foreach($desas as $desa)
                                            <option value="{{ $desa->nama_desa }}" {{ $prefill['service_area'] == $desa->nama_desa ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-check form-switch pt-1">
                                        <input class="form-check-input" type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold text-slate-700" for="is_verified">
                                            Status Verifikasi: Tandai Terverifikasi Resmi <i class="fas fa-check-circle text-blue-500 ms-1"></i>
                                        </label>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1 mb-0">Menandakan bahwa penyedia jasa ini telah divalidasi identitasnya oleh petugas kecamatan.</p>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-4 mt-4 border border-slate-100">
                                <p class="text-[11px] text-slate-500 mb-0 d-flex gap-2">
                                    <i class="fas fa-info-circle mt-0.5"></i>
                                    Setelah pendaftaran, link pengelolaan akan digenerate secara otomatis. Petugas wajib melakukan serah terima digital ke warga agar data bisa dilengkapi secara mandiri.
                                </p>
                            </div>

                            <hr class="my-4 border-slate-100">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" class="btn btn-slate-100 px-4 fw-bold">Batal</a>
                                <button type="submit" class="btn btn-indigo text-white px-4 fw-bold shadow-sm">Daftarkan Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                    <img src="https://images.unsplash.com/photo-1590650153855-d9e808231d41?w=400&auto=format&fit=crop&q=60" class="card-img-top" alt="Service">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-slate-800">Kenapa Membantu Warga?</h6>
                        <p class="text-slate-500 small mb-0 leading-relaxed text-justify">Banyak penyedia jasa lokal (tukang, ojek, dll) memiliki keahlian luar biasa namun kurang literasi digital. Dengan membantu mendaftarkan mereka, Anda berkontribusi langsung pada peningkatan ekonomi keluarga di kecamatan kita.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
