@extends('layouts.desa')

@section('title', 'Laporan Pelaksanaan BLT Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa.blt.index') }}" class="text-decoration-none">BLT
                                Desa</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold text-slate-800 mb-0">Pelaksanaan BLT Desa</h2>
                    <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-2 fw-normal">Status: Draft</span>
                </div>

                <form action="{{ route('desa.blt.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- A. Informasi Umum -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">A. Informasi Umum</h5>
                            <p class="text-slate-500 small">Data dasar penetapan penerima manfaat BLT.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Tahun Anggaran</label>
                                    <input type="number" name="tahun_anggaran" class="form-control rounded-3 py-2"
                                        value="{{ date('Y') }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-slate-700">Jumlah KPM yang ditetapkan
                                        (Total)</label>
                                    <input type="number" name="jumlah_kpm" class="form-control rounded-3 py-2"
                                        placeholder="Contoh: 50" required>
                                    <div class="small text-slate-400 mt-1">Sesuai Musdes Khusus / Penetapan Desa</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Dasar Penetapan BLT (Perdes / SK
                                        Kades)</label>
                                    <input type="text" name="dasar_penetapan" class="form-control rounded-3 py-2"
                                        placeholder="Nomor dan Tanggal SK/Perdes">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B. Realisasi Penyaluran -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">B. Realisasi Penyaluran</h5>
                            <p class="text-slate-500 small">Progres penyaluran dana ke warga penerima manfaat.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Jumlah KPM yang sudah menerima</label>
                                    <input type="number" name="kpm_terealisasi" class="form-control rounded-3 py-2"
                                        placeholder="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Total dana BLT tersalurkan (Rp)</label>
                                    <input type="number" name="total_dana_tersalurkan" class="form-control rounded-3 py-2"
                                        placeholder="0" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Periode penyaluran terakhir</label>
                                    <input type="text" name="periode_terakhir" class="form-control rounded-3 py-2"
                                        placeholder="Contoh: Triwulan I / Januari - Maret">
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
                            <p class="text-slate-500 small">Pilih komponen tambahan. Sistem akan menyiapkan checklist
                                dokumen bantuan secara otomatis.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Honor" id="komp1">
                                        <label class="form-check-label ps-2" for="komp1">
                                            <div class="fw-bold">Honor Petugas Penyalur</div>
                                            <div class="small text-slate-500">Auto: Tanda Terima Honor</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Mamin" id="komp3">
                                        <label class="form-check-label ps-2" for="komp3">
                                            <div class="fw-bold">Konsumsi Penyaluran</div>
                                            <div class="small text-slate-500">Auto: Kwitansi Mamin</div>
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

                    <!-- C. Status Penyaluran -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">C. Status Penyaluran</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Bagaimana kondisi penyaluran saat
                                        ini?</label>
                                    <div class="d-flex flex-wrap gap-3 mt-1">
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_penyaluran"
                                                id="blt1" value="Tepat Waktu" checked>
                                            <label class="form-check-label w-100" for="blt1">
                                                <div class="fw-bold text-emerald-600">Tepat Waktu</div>
                                                <div class="small text-slate-500">Sesuai jadwal</div>
                                            </label>
                                        </div>
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_penyaluran"
                                                id="blt2" value="Bertahap">
                                            <label class="form-check-label w-100" for="blt2">
                                                <div class="fw-bold text-sky-600">Bertahap</div>
                                                <div class="small text-slate-500">Dalam proses</div>
                                            </label>
                                        </div>
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_penyaluran"
                                                id="blt3" value="Tertunda">
                                            <label class="form-check-label w-100" for="blt3">
                                                <div class="fw-bold text-amber-600">Tertunda</div>
                                                <div class="small text-slate-500">Ada kendala</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-3" id="field_alasan" style="display:none;">
                                        <label class="form-label small fw-bold">Alasan Tertunda (Singkat)</label>
                                        <textarea name="alasan_tertunda" class="form-control rounded-3" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- D. Dokumentasi -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">D. Dokumentasi</h5>
                            <p class="text-slate-500 small">Bukti administratif dan visual kegiatan penyaluran.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Berita Acara (PDF)</label>
                                    <input type="file" name="dokumen_ba" class="form-control rounded-3">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Daftar KPM (PDF/XLS)</label>
                                    <input type="file" name="daftar_kpm_file" class="form-control rounded-3">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Foto Penyaluran</label>
                                    <input type="file" name="foto_penyaluran" class="form-control rounded-3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E. Catatan Desa -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">E. Catatan Desa (Opsional)</h5>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label fw-bold text-slate-700">Apakah terdapat kondisi lapangan atau kendala
                                sosial tertentu?</label>
                            <textarea name="catatan_desa" class="form-control rounded-3" rows="3"
                                placeholder="Sebutkan kendala jika ada..."></textarea>
                        </div>
                    </div>

                    <!-- F. Pernyataan Fakta -->
                    <div class="card bg-slate-50 border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="small fw-bold text-slate-800 mb-3">Pernyataan Fakta</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="pernyataan1" required>
                                <label class="form-check-label small text-slate-600" for="pernyataan1">
                                    Data penyaluran diisi sesuai kondisi realisasi (Faktual)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pernyataan2" required>
                                <label class="form-check-label small text-slate-600" for="pernyataan2">
                                    Dokumen lampiran tersedia dan disimpan di desa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('desa.blt.index') }}" class="btn btn-outline-slate rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-sky text-white rounded-pill px-5">Simpan Draft</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .custom-radio-card {
            min-width: 150px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .custom-radio-card:has(input:checked) {
            border-color: #0ea5e9 !important;
            background-color: #f0f9ff;
        }

        .btn-outline-slate {
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-sky {
            background-color: #0ea5e9;
        }

        .btn-sky:hover {
            background-color: #0284c7;
            color: white;
        }
    </style>
@endsection