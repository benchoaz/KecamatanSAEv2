@extends('layouts.kecamatan')

@section('title', 'Koreksi Data Jasa (Admin)')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" class="text-decoration-none text-slate-500 small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Manajemen
            </a>
            <h4 class="fw-bold text-slate-800 mt-2">Koreksi Data Jasa</h4>
            <p class="text-slate-500 small">Perubahan di sini bersifat administratif untuk membantu warga.</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-slate-50 p-4">
                        <h6 class="fw-bold mb-0">Identitas Layanan & Penyedia</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.jasa.update', $jasa->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Nama Penyedia / Personil</label>
                                    <input type="text" name="display_name" value="{{ old('display_name', $jasa->display_name) }}" class="form-control border-slate-200 rounded-3" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Nama Layanan / Judul Kerja</label>
                                    <input type="text" name="job_title" value="{{ old('job_title', $jasa->job_title) }}" class="form-control border-slate-200 rounded-3" placeholder="Contoh: Tukang Ledeng Panggilan" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Kategori Layanan</label>
                                    <select name="job_category" class="form-select border-slate-200 rounded-3" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ old('job_category', $jasa->job_category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">No. WhatsApp (Format: 628...)</label>
                                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $jasa->contact_phone) }}" class="form-control border-slate-200 rounded-3" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Wilayah Utama Layanan (Desa)</label>
                                    <select name="service_area" class="form-select border-slate-200 rounded-3" required>
                                        @foreach($desas as $desa)
                                            <option value="{{ $desa->nama_desa }}" {{ old('service_area', $jasa->service_area) == $desa->nama_desa ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Status Publikasi</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" {{ $jasa->status == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold text-emerald-600" for="statusActive">Aktif / Publik</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" {{ $jasa->status == 'inactive' ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold text-slate-400" for="statusInactive">Nonaktif</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 fw-bold small">Status Verifikasi Resmi</label>
                                    <div class="form-check form-switch pt-1">
                                        <input class="form-check-input" type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified', $jasa->is_verified) ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold text-slate-700" for="is_verified">
                                            Vetted by District <i class="fas fa-check-circle text-blue-500 ms-1"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-slate-100">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" class="btn btn-slate-100 px-4 fw-bold">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-premium rounded-4 bg-indigo-600 text-white">
                    <div class="card-body p-4 text-center">
                        <div class="w-16 h-16 bg-white/20 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                            <i class="fas fa-magic text-2xl"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Penyelarasan Digital</h6>
                        <p class="small opacity-80 mb-0">Citizen-centric management. Pastikan data yang dikoreksi sudah benar agar tidak membingungkan warga yang mencari jasa di portal publik.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
