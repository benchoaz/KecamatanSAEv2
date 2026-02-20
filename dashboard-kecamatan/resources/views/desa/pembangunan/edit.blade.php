@extends('layouts.desa')

@section('title', 'Edit Laporan Pembangunan - ' . $item->nama_kegiatan)

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold text-slate-800 mb-0">Edit Laporan</h2>
                    <span class="badge bg-amber-100 text-amber-600 rounded-pill px-3 py-2 fw-normal">Status:
                        {{ $item->status_laporan }}</span>
                </div>

                <form action="{{ route('desa.pembangunan.update', $item->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Reposting the same card structure from create.blade.php but with values -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">Identitas Kegiatan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" class="form-control rounded-3 py-2"
                                        value="{{ $item->nama_kegiatan }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-slate-700">Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control rounded-3 py-2"
                                        value="{{ $item->lokasi }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Tahun Anggaran</label>
                                    <input type="number" name="tahun_anggaran" class="form-control rounded-3 py-2"
                                        value="{{ $item->tahun_anggaran }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Bidang Anggaran Pendapatan dan Belanja Desa</label>
                                    <select name="bidang_apbdes" class="form-select rounded-3 py-2" required>
                                        <option value="Bidang Penyelenggaraan Pemerintahan Desa" {{ $item->bidang_apbdes == 'Bidang Penyelenggaraan Pemerintahan Desa' ? 'selected' : '' }}>Bidang Penyelenggaraan Pemerintahan Desa</option>
                                        <option value="Bidang Pelaksanaan Pembangunan Desa" {{ $item->bidang_apbdes == 'Bidang Pelaksanaan Pembangunan Desa' ? 'selected' : '' }}>Bidang Pelaksanaan Pembangunan Desa</option>
                                        <option value="Bidang Pembinaan Kemasyarakatan" {{ $item->bidang_apbdes == 'Bidang Pembinaan Kemasyarakatan' ? 'selected' : '' }}>Bidang Pembinaan Kemasyarakatan</option>
                                        <option value="Bidang Pemberdayaan Masyarakat" {{ $item->bidang_apbdes == 'Bidang Pemberdayaan Masyarakat' ? 'selected' : '' }}>Bidang Pemberdayaan Masyarakat</option>
                                        <option value="Bidang Penanggulangan Bencana, Darurat dan Mendesak Desa" {{ $item->bidang_apbdes == 'Bidang Penanggulangan Bencana, Darurat dan Mendesak Desa' ? 'selected' : '' }}>Bidang Penanggulangan Bencana, Darurat dan Mendesak Desa</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Sumber Dana</label>
                                    <input type="text" name="sumber_dana" class="form-control rounded-3 py-2"
                                        value="{{ $item->sumber_dana }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Progres -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Status Pengerjaan</label>
                                    <select name="status_kegiatan" class="form-select rounded-3 py-2">
                                        @foreach(['Belum Dimulai', 'Sedang Berjalan', 'Selesai', 'Tertunda'] as $st)
                                            <option value="{{ $st }}" {{ $item->status_kegiatan == $st ? 'selected' : '' }}>
                                                {{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Progres Fisik (%)</label>
                                    <select name="progres_fisik" class="form-select rounded-3 py-2">
                                        @foreach(['0-25%', '26-50%', '51-75%', '76-100%'] as $pr)
                                            <option value="{{ $pr }}" {{ $item->progres_fisik == $pr ? 'selected' : '' }}>
                                                {{ $pr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Anggaran -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Pagu Anggaran (Rp)</label>
                                    <input type="number" name="pagu_anggaran" class="form-control rounded-3 py-2"
                                        value="{{ $item->pagu_anggaran }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Realisasi (Rp)</label>
                                    <input type="number" name="realisasi_anggaran" class="form-control rounded-3 py-2"
                                        value="{{ $item->realisasi_anggaran }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('desa.pembangunan.index') }}"
                            class="btn btn-outline-slate rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-emerald text-white rounded-pill px-5">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection