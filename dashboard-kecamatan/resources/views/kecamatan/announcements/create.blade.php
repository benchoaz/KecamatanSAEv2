@extends('layouts.kecamatan')

@section('title', 'Buat Pengumuman Baru')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.announcements.index') }}"
                class="btn btn-link text-slate-500 text-decoration-none p-0 small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden border border-slate-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                        <h5 class="mb-0 fw-bold fs-6">Form Pengumuman Baru</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.announcements.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-700">Judul Pengumuman</label>
                                    <input type="text" name="title" class="form-control bg-slate-50 border-slate-200"
                                        placeholder="Contoh: Imbauan Musdes APBDes 2026" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-700">Isi Pengumuman</label>
                                    <textarea name="content" class="form-control bg-slate-50 border-slate-200 h-32"
                                        placeholder="Tuliskan isi pengumuman secara singkat dan jelas..." required
                                        maxlength="500"></textarea>
                                    <div class="text-[10px] text-slate-400 mt-1 italic">Maksimal 500 karakter. Gunakan
                                        bahasa formal pemerintahan.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-700">Target Audiens</label>
                                    <select name="target_type" id="target_type"
                                        class="form-select bg-slate-50 border-slate-200" required>
                                        <option value="public">Publik (Landing Page)</option>
                                        <option value="all_desa">Semua Desa (Dashboard Desa)</option>
                                        <option value="specific_desa">Desa Tertentu</option>
                                        <option value="internal">Internal Kecamatan Saja</option>
                                    </select>
                                </div>

                                <div class="col-md-6" id="specific_desa_container" style="display: none;">
                                    <label class="form-label small fw-bold text-slate-700">Pilih Desa</label>
                                    <select name="target_desa_ids[]" class="form-select bg-slate-50 border-slate-200"
                                        multiple style="height: 100px;">
                                        @foreach($desa as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_desa }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-[10px] text-slate-400 mt-1">Tahan Ctrl untuk memilih lebih dari satu.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-700">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control bg-slate-50 border-slate-200"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-700">Tanggal Berakhir</label>
                                    <input type="date" name="end_date" class="form-control bg-slate-50 border-slate-200"
                                        value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-slate-700">Mode Tampilan</label>
                                    <select name="display_mode" class="form-select bg-slate-50 border-slate-200" required>
                                        <option value="ticker">Tulisan Berjalan (Ticker)</option>
                                        <option value="banner">Banner Statis</option>
                                        <option value="notification">Notifikasi Popup</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-slate-700">Prioritas</label>
                                    <select name="priority" class="form-select bg-slate-50 border-slate-200" required>
                                        <option value="normal">Normal</option>
                                        <option value="important">Penting / Urgent</option>
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end mb-1">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            checked>
                                        <label class="form-check-label small fw-bold text-slate-700" for="is_active">Status
                                            Aktif</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-slate-100">

                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset"
                                    class="btn btn-link text-slate-400 text-decoration-none small fw-bold">Reset
                                    Form</button>
                                <button type="submit" class="btn btn-primary px-5 rounded-3 fw-bold shadow-sm">Simpan
                                    Pengumuman</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('target_type').addEventListener('change', function () {
                const specificDesaContainer = document.getElementById('specific_desa_container');
                if (this.value === 'specific_desa') {
                    specificDesaContainer.style.display = 'block';
                } else {
                    specificDesaContainer.style.display = 'none';
                }
            });

            // Auto trigger on load if error or edit
            window.addEventListener('load', function () {
                const targetType = document.getElementById('target_type');
                if (targetType.value === 'specific_desa') {
                    document.getElementById('specific_desa_container').style.display = 'block';
                }
            });
        </script>
    @endpush
@endsection