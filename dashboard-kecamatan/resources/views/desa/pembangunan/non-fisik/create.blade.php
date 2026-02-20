@extends('layouts.desa')

@section('title', 'Laporan Kegiatan Non Fisik')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.dashboard') }}"
                                class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.non-fisik.index') }}"
                                class="text-decoration-none">Kegiatan Non Fisik</a></li>
                        <li class="breadcrumb-item active">Laporan Baru</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold text-slate-800 mb-0">Laporan Kegiatan Non Fisik</h2>
                    <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-2 fw-normal">Status: Draft</span>
                </div>

                <form action="{{ route('desa.pembangunan.non-fisik.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- A. Identitas Kegiatan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">A. Identitas Kegiatan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Jenis Kegiatan</label>
                                    <select name="jenis_kegiatan" class="form-select rounded-3 py-2" required>
                                        <option value="Non Fisik" selected>Kegiatan Non Fisik</option>
                                        <option value="Musdes">Musyawarah Desa (Musdes)</option>
                                        <option value="Pelatihan">Pelatihan / Bimtek</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" class="form-control rounded-3 py-2"
                                        placeholder="Contoh: Pelatihan Digital Marketing UMKM" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Tahun Anggaran</label>
                                    <input type="number" name="tahun_anggaran" class="form-control rounded-3 py-2"
                                        value="{{ date('Y') }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-slate-700">Lokasi Pelaksanaan</label>
                                    <input type="text" name="lokasi" class="form-control rounded-3 py-2"
                                        placeholder="Sebutkan tempat kegiatan" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Sumber Dana</label>
                                    <select name="sumber_dana" class="form-select rounded-3 py-2" required>
                                        <option value="DDS">Dana Desa (DDS)</option>
                                        <option value="ADD">Alokasi Dana Desa (ADD)</option>
                                        <option value="PBH">Bagi Hasil Pajak & Retribusi (PBH)</option>
                                        <option value="DLL">Lain-lain</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B. Anggaran & Realisasi -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">B. Anggaran & Realisasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Pagu Anggaran (Rp)</label>
                                    <input type="number" name="pagu_anggaran" class="form-control rounded-3 py-2"
                                        placeholder="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Realisasi Anggaran (Rp)</label>
                                    <input type="number" name="realisasi_anggaran" class="form-control rounded-3 py-2"
                                        placeholder="0" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bantuan Administrasi Toggle Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-emerald-50 border border-emerald-200">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-emerald-100 text-emerald-600 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px;">
                                    <i class="fas fa-magic"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-emerald-900 mb-1">Butuh bantuan administrasi otomatis
                                        (SPJ/Kwitansi)?</h6>
                                    <p class="text-emerald-700 small mb-0">Aktifkan untuk memilih komponen belanja agar
                                        sistem menyiapkan draf dokumen bantuan.</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-emerald text-white rounded-pill px-4 fw-bold"
                                onclick="document.getElementById('section-komponen').classList.remove('d-none'); this.closest('.card').classList.add('d-none');">
                                Aktifkan Asisten
                            </button>
                        </div>
                    </div>

                    <!-- New Section: Komponen Belanja (Sistem yang mikirkan SPJ) -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 d-none" id="section-komponen">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="fw-bold text-slate-800 mb-0">X. Komponen Belanja</h5>
                                    <span class="badge bg-emerald-100 text-emerald-600 rounded-pill px-2 py-1 small">Asisten
                                        Administrasi Aktif</span>
                                </div>
                                <button type="button" class="btn-close small"
                                    onclick="document.getElementById('section-komponen').classList.add('d-none'); document.querySelector('.bg-emerald-50').classList.remove('d-none');"></button>
                            </div>
                            <p class="text-slate-500 small">Pilih komponen yang ada dalam anggaran. Sistem akan menyiapkan
                                checklist dokumen bantuan secara otomatis.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Honor" id="komp1">
                                        <label class="form-check-label ps-2" for="komp1">
                                            <div class="fw-bold">Honor Narasumber/Upah</div>
                                            <div class="small text-slate-500">Auto: Tanda Terima Honor</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Uang Saku" id="komp2">
                                        <label class="form-check-label ps-2" for="komp2">
                                            <div class="fw-bold">Uang Saku Peserta</div>
                                            <div class="small text-slate-500">Auto: Tanda Terima Saku</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Mamin" id="komp3">
                                        <label class="form-check-label ps-2" for="komp3">
                                            <div class="fw-bold">Konsumsi / Mamin</div>
                                            <div class="small text-slate-500">Auto: Kwitansi Mamin</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="ATK" id="komp4">
                                        <label class="form-check-label ps-2" for="komp4">
                                            <div class="fw-bold">Alat Tulis Kantor (ATK)</div>
                                            <div class="small text-slate-500">Auto: Kwitansi ATK</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Banner" id="komp5">
                                        <label class="form-check-label ps-2" for="komp5">
                                            <div class="fw-bold">Spanduk / Banner</div>
                                            <div class="small text-slate-500">Auto: Kwitansi Percetakan</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">C. Pelaksanaan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Tanggal Kegiatan</label>
                                    <input type="date" name="tanggal_kegiatan" class="form-control rounded-3 py-2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Jumlah Peserta (Orang)</label>
                                    <input type="number" name="jumlah_peserta" class="form-control rounded-3 py-2"
                                        placeholder="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Status Kegiatan</label>
                                    <select name="status_kegiatan" class="form-select rounded-3 py-2" required>
                                        <option value="Terlaksana">Terlaksana</option>
                                        <option value="Tertunda">Tertunda</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- D. Dokumentasi -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">D. Dokumentasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Upload Daftar Hadir (PDF)</label>
                                    <input type="file" name="rab_file" class="form-control rounded-3">
                                    <div class="small text-slate-400 mt-1 italic">*Repurpose field: rab_file for attendance
                                        list</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Upload Foto Kegiatan</label>
                                    <input type="file" name="foto_progres_file" class="form-control rounded-3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E. Pernyataan Fakta -->
                    <div class="card bg-slate-50 border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="small fw-bold text-slate-800 mb-3">Pernyataan Fakta</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="pernyataan1" required>
                                <label class="form-check-label small text-slate-600" for="pernyataan1">
                                    Data kegiatan diisi sesuai pelaksanaan riil (Faktual)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pernyataan2" required>
                                <label class="form-check-label small text-slate-600" for="pernyataan2">
                                    Daftar hadir dan dokumentasi asli disimpan di desa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('desa.pembangunan.non-fisik.index') }}"
                            class="btn btn-outline-slate rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-emerald text-white rounded-pill px-5">Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection