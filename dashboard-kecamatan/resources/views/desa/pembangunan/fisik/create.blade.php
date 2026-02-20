@extends('layouts.desa')

@section('title', 'Tambah Laporan Pembangunan Fisik')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.fisik.index') }}"
                                class="text-decoration-none">Fisik</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold text-slate-800 mb-0">Tambah Laporan Fisik</h2>
                    <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-2 fw-normal">Status: Draft</span>
                </div>

                <form action="{{ route('desa.pembangunan.fisik.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- A. Identitas Kegiatan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">A. Identitas Kegiatan</h5>
                            <p class="text-slate-500 small">Informasi dasar mengenai pembangunan yang sedang/akan
                                dilaksanakan.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-slate-700">Jenis Kegiatan</label>
                                    <select name="jenis_kegiatan" class="form-select rounded-3 py-2" required>
                                        <option value="Fisik" selected>Pembangunan Fisik</option>
                                        <option value="Non Fisik">Kegiatan Non Fisik</option>
                                        <option value="Musdes">Musyawarah Desa (Musdes)</option>
                                        <option value="BLT">Penyaluran BLT</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Tahun Anggaran</label>
                                    <input type="number" name="tahun_anggaran" class="form-control rounded-3 py-2"
                                        value="{{ date('Y') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Apa nama pembangunan yang sedang
                                        dikerjakan?</label>
                                    <input type="text" name="nama_kegiatan" class="form-control rounded-3 py-2"
                                        placeholder="Contoh: Pembangunan Jalan Rabat Beton Dusun Krajan" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Di mana lokasi spesifik
                                        pembangunannya?</label>
                                    <input type="text" name="lokasi" class="form-control rounded-3 py-2"
                                        placeholder="Sebutkan dusun, RT/RW, atau area lokasi" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Bidang Anggaran Pendapatan dan Belanja
                                        Desa</label>
                                    <select name="bidang_apbdes" class="form-select rounded-3 py-2" required>
                                        <option value="" disabled selected>Pilih bidang...</option>
                                        <option value="Bidang Penyelenggaraan Pemerintahan Desa">Bidang Penyelenggaraan
                                            Pemerintahan Desa</option>
                                        <option value="Bidang Pelaksanaan Pembangunan Desa">Bidang Pelaksanaan Pembangunan
                                            Desa</option>
                                        <option value="Bidang Pembinaan Kemasyarakatan">Bidang Pembinaan Kemasyarakatan
                                        </option>
                                        <option value="Bidang Pemberdayaan Masyarakat">Bidang Pemberdayaan Masyarakat
                                        </option>
                                        <option value="Bidang Penanggulangan Bencana, Darurat dan Mendesak Desa">Bidang
                                            Penanggulangan Bencana, Darurat dan Mendesak Desa</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Sub Bidang</label>
                                    <input type="text" name="sub_bidang" class="form-control rounded-3 py-2"
                                        placeholder="Contoh: Pekerjaan Umum dan Penataan Ruang">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Sumber Dana</label>
                                    <select name="sumber_dana" class="form-select rounded-3 py-2" required>
                                        <option value="DDS">Dana Desa (DDS)</option>
                                        <option value="ADD">Alokasi Dana Desa (ADD)</option>
                                        <option value="PBH">Bagi Hasil Pajak & Retribusi (PBH)</option>
                                        <option value="DLL">Lain-lain (PAD, Bantuan Keuangan, dll)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B. Status Pelaksanaan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">B. Status Pelaksanaan</h5>
                            <p class="text-slate-500 small">Kondisi terkini dari kegiatan pembangunan.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Bagaimana status pengerjaannya saat
                                        ini?</label>
                                    <div class="d-flex flex-wrap gap-3 mt-1">
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_kegiatan"
                                                id="status1" value="Belum Dimulai" checked>
                                            <label class="form-check-label w-100" for="status1">
                                                <div class="fw-bold">Belum Dimulai</div>
                                                <div class="small text-slate-500">Persiapan lahan/administrasi</div>
                                            </label>
                                        </div>
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_kegiatan"
                                                id="status2" value="Sedang Berjalan">
                                            <label class="form-check-label w-100" for="status2">
                                                <div class="fw-bold">Sedang Berjalan</div>
                                                <div class="small text-slate-500">Pengerjaan fisik di lapangan</div>
                                            </label>
                                        </div>
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_kegiatan"
                                                id="status3" value="Selesai">
                                            <label class="form-check-label w-100" for="status3">
                                                <div class="fw-bold">Selesai</div>
                                                <div class="small text-slate-500">Pengerjaan telah 100%</div>
                                            </label>
                                        </div>
                                        <div class="form-check custom-radio-card p-3 rounded-4 border">
                                            <input class="form-check-input d-none" type="radio" name="status_kegiatan"
                                                id="status4" value="Tertunda">
                                            <label class="form-check-label w-100" for="status4">
                                                <div class="fw-bold text-danger">Tertunda</div>
                                                <div class="small text-slate-500">Ada kendala teknis/alam</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- C. Progres Fisik -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">C. Progres Fisik</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Dalam rentang berapa persentase
                                        pengerjaan saat ini?</label>
                                    <select name="progres_fisik" class="form-select rounded-3 py-2" required>
                                        <option value="0-25%">0 - 25% (Tahap Awal)</option>
                                        <option value="26-50%">26 - 50% (Sudah Terlihat)</option>
                                        <option value="51-75%">51 - 75% (Hampir Selesai)</option>
                                        <option value="76-100%">76 - 100% (Finishing/Selesai)</option>
                                    </select>
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
                                <div class="col-md-6">
                                    <div class="form-check p-3 rounded-4 border bg-white h-100">
                                        <input class="form-check-input" type="checkbox" name="komponen_belanja[]"
                                            value="Material" id="komp6">
                                        <label class="form-check-label ps-2" for="komp6">
                                            <div class="fw-bold">Material / Barang</div>
                                            <div class="small text-slate-500">Auto: Nota/Kwitansi Barang</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- D. Realisasi Anggaran -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">D. Realisasi Anggaran</h5>
                            <p class="text-slate-500 small">Estimasi penggunaan anggaran untuk monitoring internal (tanpa
                                dokumen bukti belanja).</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Pagu Anggaran Kegiatan (Rp)</label>
                                    <input type="number" name="pagu_anggaran" class="form-control rounded-3 py-2"
                                        placeholder="0" required>
                                    <div class="small text-slate-400 mt-1 italic">Nilai total anggaran sesuai RAB.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Realisasi Anggaran Saat Ini
                                        (Rp)</label>
                                    <input type="number" name="realisasi_anggaran" class="form-control rounded-3 py-2"
                                        placeholder="0" required>
                                    <div class="small text-slate-400 mt-1 italic">Total dana yang sudah terserap untuk
                                        kegiatan ini.</div>
                                </div>
                                <div class="col-md-12">
                                    <div class="p-3 rounded-4 bg-slate-50 border border-slate-100">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-slate-600">Persentase Serapan Anggaran</span>
                                            <span class="small fw-bold text-emerald-600" id="serapanText">0%</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 8px;">
                                            <div id="serapanBar" class="progress-bar bg-emerald-500" role="progressbar"
                                                style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E. Dokumen Perencanaan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">E. Dokumen Perencanaan</h5>
                            <p class="text-slate-500 small">Dokumen ini sebagai referensi perencanaan, bukan pemeriksaan
                                teknis.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Upload RAB (PDF)</label>
                                    <input type="file" name="rab_file" class="form-control rounded-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Upload Gambar Rencana/Desain
                                        (PDF/JPG)</label>
                                    <input type="file" name="gambar_rencana_file" class="form-control rounded-3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- F. Dokumentasi Lapangan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">F. Dokumentasi Lapangan</h5>
                            <p class="text-slate-500 small">Minimal lampirkan 2 foto progres kegiatan.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4 text-center">
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <i class="fas fa-image text-slate-300 mb-2"></i>
                                        <div class="small fw-bold mb-2">Foto Sebelum (0%)</div>
                                        <input type="file" name="foto_sebelum_file" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <i class="fas fa-spinner text-slate-300 mb-2"></i>
                                        <div class="small fw-bold mb-2">Foto Progres</div>
                                        <input type="file" name="foto_progres_file" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <i class="fas fa-check-double text-slate-300 mb-2"></i>
                                        <div class="small fw-bold mb-2">Foto Selesai (100%)</div>
                                        <input type="file" name="foto_selesai_file" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- G. Pernyataan Fakta -->
                    <div class="card bg-slate-50 border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="small fw-bold text-slate-800 mb-3">Pernyataan Fakta</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="pernyataan1" required>
                                <label class="form-check-label small text-slate-600" for="pernyataan1">
                                    Data diisi sesuai kondisi lapangan (Proses Faktual)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pernyataan2" required>
                                <label class="form-check-label small text-slate-600" for="pernyataan2">
                                    Dokumen fisik/berkas tersedia dan disimpan di desa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('desa.pembangunan.fisik.index') }}"
                            class="btn btn-outline-slate rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-emerald text-white rounded-pill px-5">Simpan Laporan</button>
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
            border-color: #10b981 !important;
            background-color: #ecfdf5;
        }

        .btn-outline-slate {
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-emerald {
            background-color: #10b981;
        }

        .btn-emerald:hover {
            background-color: #059669;
            color: white;
        }
    </style>
@endsection