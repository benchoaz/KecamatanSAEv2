@extends('layouts.kecamatan')

@section('title', 'Manajemen Geospasial Wilayah')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="content-header mb-4">
            <div class="header-title">
                <h1 class="text-slate-900 fw-bold display-6">Geospasial Wilayah</h1>
                <p class="text-slate-500 fs-5 mb-0">Kelola data pemetaan (GeoJSON) untuk visualisasi peta wilayah secara
                    dinamis.</p>
                <div class="header-accent"></div>
            </div>
        </div>

        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif

        <div class="row">
            <!-- Sidebar Info / Upload -->
            <div class="col-xl-4 mb-4">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-4 px-4 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2 text-slate-900">
                            <i class="fas fa-upload text-primary"></i>
                            <h5 class="mb-0 fw-bold">Update Layer Peta</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.settings.geospasial.upload') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label text-slate-700 fw-semibold">Tipe Layer</label>
                                <select name="type" class="form-select bg-slate-50 border-slate-200 rounded-3" required>
                                    <option value="kecamatan">Batas Wilayah (Master Kecamatan)</option>
                                    <option value="desa">Batas Desa (Sub-wilayah)</option>
                                    <option value="poi">Titik Penting (POI)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-slate-700 fw-semibold">File GeoJSON (.geojson)</label>
                                <input type="file" name="geojson_file"
                                    class="form-control bg-slate-50 border-slate-200 rounded-3"
                                    accept=".geojson,application/json" required>
                                <div class="form-text text-[11px] text-slate-400 mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Jika Anda mengupload file "Master Kecamatan",
                                    sistem akan otomatis memfilter peta berdasarkan nama wilayah di pengaturan profil.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm"
                                style="background-color: #4f46e5; border: 0;">
                                <i class="fas fa-save me-2"></i> Update Layer
                            </button>
                        </form>
                    </div>
                </div>

                <div class="alert bg-primary bg-opacity-10 border-0 p-4 rounded-4 shadow-sm">
                    <h6 class="fw-bold text-primary mb-3">Panduan Teknis</h6>
                    <ul class="text-primary text-opacity-75 small space-y-2 ps-3 mb-0">
                        <li>Gunakan format file <strong>.geojson</strong> standar WGS84.</li>
                        <li>Pastikan properti nama wilayah menggunakan salah satu kunci berikut: <code>NM_KEC</code>,
                            <code>nm_kecamatan</code>, <code>name</code>, atau <code>NAMOBJ</code>.</li>
                        <li>Peta pada Landing Page akan otomatis melakukan <i>auto-zoom</i> ke wilayah yang sesuai dengan
                            nama wilayah Anda.</li>
                    </ul>
                </div>
            </div>

            <!-- List Files -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2 text-slate-900">
                            <i class="fas fa-folder-open text-primary"></i>
                            <h5 class="mb-0 fw-bold">Berkas GeoJSON Aktif</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="ps-4 text-slate-500 fw-semibold small text-uppercase py-3">Nama Berkas
                                        </th>
                                        <th class="text-slate-500 fw-semibold small text-uppercase py-3">Ukuran</th>
                                        <th class="text-slate-500 fw-semibold small text-uppercase py-3">Terakhir Diubah
                                        </th>
                                        <th class="pe-4 text-end text-slate-500 fw-semibold small text-uppercase py-3">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $file)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="icon-box bg-slate-100 text-slate-500 sm rounded-3">
                                                        <i class="fas fa-map-marked-alt"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-slate-700">{{ $file['name'] }}</div>
                                                        <div class="text-[10px] text-slate-400">application/geo+json</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-slate-600 small">{{ $file['size'] }}</td>
                                            <td class="text-slate-600 small">{{ $file['last_modified'] }}</td>
                                            <td class="pe-4 text-end">
                                                <span
                                                    class="badge bg-emerald-100 text-emerald-700 rounded-pill px-3 py-2 fw-bold small">
                                                    Aktif Digunakan
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="opacity-30 mb-3">
                                                    <i class="fas fa-folder-open display-4"></i>
                                                </div>
                                                <p class="text-slate-400 mb-0">Belum ada berkas GeoJSON di folder data.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .text-slate-900 {
            color: #0f172a;
        }

        .text-slate-700 {
            color: #334155;
        }

        .text-slate-600 {
            color: #475569;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .text-slate-400 {
            color: #94a3b8;
        }

        .border-slate-200 {
            border-color: #e2e8f0;
        }

        .shadow-premium {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        }

        .icon-box.sm {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .bg-emerald-100 {
            background-color: #ecfdf5;
        }

        .text-emerald-700 {
            color: #047857;
        }
    </style>
@endpush