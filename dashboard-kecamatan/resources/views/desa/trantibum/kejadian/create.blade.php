@extends('layouts.desa')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Buat Laporan Kejadian/Bencana</h4>
                            <p class="text-muted mb-0">Formulir pelaporan kejadian trantibum & bencana alam (Format
                                Standar).</p>
                        </div>
                        <a href="{{ route('desa.trantibum.kejadian.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('desa.trantibum.kejadian.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="alert alert-soft-primary mb-4" role="alert">
                                <i class="fas fa-info-circle me-2"></i> Laporan ini akan langsung diteruskan ke
                                <strong>Camat, Bupati, dan Wakil Bupati</strong>. Mohon isi dengan data yang valid dan dapat
                                dipertanggungjawabkan.
                            </div>

                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label required fw-bold">Kategori Kejadian</label>
                                        <select name="kategori" class="form-select" required>
                                            <option value="">Pilih Kategori...</option>
                                            <option value="Bencana Alam">Bencana Alam (Banjir, Longsor, dll)</option>
                                            <option value="Kriminalitas">Kriminalitas (Pencurian, Perampokan, dll)</option>
                                            <option value="Ketertiban Umum">Ketertiban Umum (Unjuk Rasa, Tawuran, dll)
                                            </option>
                                            <option value="Kebakaran">Kebakaran</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label required fw-bold">Detail Kejadian</label>
                                        <input type="text" name="jenis_kejadian" class="form-control"
                                            placeholder="Contoh: Pencurian Sapi / Banjir Bandang" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label required fw-bold">Waktu Kejadian</label>
                                        <input type="datetime-local" name="waktu_kejadian" class="form-control" required>
                                    </div>

                                    <div class="card bg-soft-light mb-3">
                                        <div class="card-body py-3">
                                            <h6 class="fw-bold mb-3"><i
                                                    class="fas fa-map-marker-alt me-2 text-danger"></i>Lokasi Terdampak</h6>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Deskripsi Lokasi</label>
                                                <textarea name="lokasi_deskripsi" class="form-control" rows="2"
                                                    placeholder="Contoh: Desa Sindetlami, Dusun Krajan RT 01 RW 02"></textarea>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="form-label">Koordinat (Opsional)</label>
                                                <input type="text" name="lokasi_koordinat" class="form-control"
                                                    placeholder="Contoh: -7.788034, 113.492597">
                                                <small class="text-muted">Format: Latitude, Longitude</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label required fw-bold">Kronologi Kejadian</label>
                                        <textarea name="kronologi" class="form-control" rows="5" required
                                            placeholder="Ceritakan kronologi kejadian..."></textarea>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Dampak Kerusakan</label>
                                        <textarea name="dampak_kerusakan" class="form-control" rows="4"
                                            placeholder="Detail kerusakan rumah, fasilitas umum, korban jiwa, dll..."></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Kondisi Mutakhir</label>
                                        <textarea name="kondisi_mutakhir" class="form-control" rows="2"
                                            placeholder="Kondisi saat ini di lapangan..."></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Upaya Penanganan</label>
                                        <textarea name="upaya_penanganan" class="form-control" rows="2"
                                            placeholder="Langkah yang sudah dilakukan desa/warga..."></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Pihak / Unsur yang Terlibat</label>
                                        <textarea name="pihak_terlibat" class="form-control" rows="2"
                                            placeholder="Contoh: Pemdes, Babinsa, Bhabinkamtibmas, Warga..."></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Foto Dokumentasi</label>
                                        <input type="file" name="foto_kejadian" class="form-control" accept="image/*">
                                        <small class="text-muted">Upload bukti foto kejadian (Max: 2MB)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection