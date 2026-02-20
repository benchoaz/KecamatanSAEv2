@extends('layouts.kecamatan')

@section('title', isset($loker) ? 'Edit Loker' : 'Tambah Loker')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12 col-lg-8 mx-auto">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 border-bottom">
                        <div class="header-title">
                            <h4 class="card-title fw-bold">
                                {{ isset($loker) ? 'Edit Lowongan Kerja' : 'Tambah Lowongan Kerja' }}</h4>
                            <p class="text-muted small mb-0">Halaman bantuan input atau koreksi data loker warga.</p>
                        </div>
                        <a href="{{ route('kecamatan.loker.index') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <form
                            action="{{ isset($loker) ? route('kecamatan.loker.update', $loker->id) : route('kecamatan.loker.store') }}"
                            method="POST">
                            @csrf
                            @if(isset($loker))
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                <!-- Informasi Utama -->
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3 text-primary border-start border-3 border-primary ps-2">
                                        Informasi Pekerjaan</h6>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Judul Pekerjaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control rounded-3"
                                        value="{{ old('title', $loker->title ?? ($prefill['title'] ?? '')) }}" required
                                        placeholder="Contoh: Butuh Tukang Bangunan Harian">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategori <span
                                            class="text-danger">*</span></label>
                                    <select name="job_category" class="form-select rounded-3" required>
                                        <option value="">Pilih Kategori...</option>
                                        @php
                                            $categories = ['Buruh tani', 'Tukang bangunan', 'Tukang pijet', 'ART', 'Ojek / becak', 'Lainnya'];
                                        @endphp
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ (old('job_category', $loker->job_category ?? '') == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">WhatsApp/HP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="contact_wa" class="form-control rounded-3"
                                        value="{{ old('contact_wa', $loker->contact_wa ?? ($prefill['wa'] ?? '')) }}"
                                        required placeholder="Contoh: 0812...">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Desa</label>
                                    <select name="desa_id" class="form-select rounded-3">
                                        <option value="">Pilih Desa...</option>
                                        @foreach($desas as $desa)
                                            <option value="{{ $desa->id }}" {{ (old('desa_id', $loker->desa_id ?? ($prefill['desa_id'] ?? '')) == $desa->id) ? 'selected' : '' }}>
                                                {{ $desa->nama_desa }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu Kerja</label>
                                    <select name="work_time" class="form-select rounded-3">
                                        <option value="Harian" {{ (old('work_time', $loker->work_time ?? '') == 'Harian') ? 'selected' : '' }}>Harian</option>
                                        <option value="Mingguan" {{ (old('work_time', $loker->work_time ?? '') == 'Mingguan') ? 'selected' : '' }}>Mingguan</option>
                                        <option value="Borongan" {{ (old('work_time', $loker->work_time ?? '') == 'Borongan') ? 'selected' : '' }}>Borongan</option>
                                        <option value="Lainnya" {{ (old('work_time', $loker->work_time ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <textarea name="description" class="form-control rounded-3"
                                        rows="3">{{ old('description', $loker->description ?? '') }}</textarea>
                                </div>

                                <!-- Pengaturan Admin -->
                                <div class="col-12 pt-3">
                                    <h6 class="fw-bold mb-3 text-primary border-start border-3 border-primary ps-2">
                                        Pengaturan Dashboard</h6>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-danger">Status Publikasi <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-select rounded-3 border-danger-subtle" required>
                                        <option value="menunggu_verifikasi" {{ (old('status', $loker->status ?? '') == 'menunggu_verifikasi') ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                        <option value="aktif" {{ (old('status', $loker->status ?? '') == 'aktif') ? 'selected' : '' }}>Aktif (Tampil Publik)</option>
                                        <option value="nonaktif" {{ (old('status', $loker->status ?? '') == 'nonaktif') ? 'selected' : '' }}>Nonaktif (Sembunyikan)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-flex flex-column gap-2 justify-content-center">
                                    <div class="form-check form-switch p-0 ms-4">
                                        <input class="form-check-input" type="checkbox" name="is_available_today"
                                            id="todaySwitch" value="1" {{ old('is_available_today', $loker->is_available_today ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-2" for="todaySwitch">Butuh Mendesak
                                            (Hari Ini)</label>
                                    </div>
                                    <div class="form-check form-switch p-0 ms-4">
                                        <input class="form-check-input" type="checkbox" name="is_sensitive"
                                            id="sensitiveSwitch" value="1" {{ old('is_sensitive', $loker->is_sensitive ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-2" for="sensitiveSwitch">Mark as
                                            Sensitive (Privat)</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Catatan Internal (Khusus Admin)</label>
                                    <textarea name="internal_notes" class="form-control rounded-3 bg-light" rows="2"
                                        placeholder="Catatan verifikasi atau alasan penolakan/nonaktif...">{{ old('internal_notes', $loker->internal_notes ?? '') }}</textarea>
                                    <small class="text-muted italic">Catatan ini tidak akan tampil di halaman publik
                                        warga.</small>
                                </div>

                                <div class="col-12 pt-4 border-top mt-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('kecamatan.loker.index') }}"
                                            class="btn btn-light rounded-pill px-4">Batal</a>
                                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                            <i class="fas fa-save me-1"></i>
                                            {{ isset($loker) ? 'Simpan Perubahan' : 'Terbitkan Loker' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection