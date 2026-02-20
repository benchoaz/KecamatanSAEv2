@extends('layouts.kecamatan')

@section('title', 'Master Data Desa')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Master Data Desa</h4>
                <p class="text-muted small">Fondasi referensi wilayah untuk seluruh modul aplikasi</p>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                data-bs-target="#addDesaModal" style="background-color: #4f46e5; border-color: #4f46e5; color: white;">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Desa Baru</span>
            </button>
        </div>

        <!-- Stats Section -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="opacity-75 d-block">Total Wilayah Desa</small>
                                <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                            </div>
                            <i class="fas fa-map-marked-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Desa Aktif</small>
                                <h3 class="fw-bold mb-0 text-success">{{ $stats['active'] }}</h3>
                            </div>
                            <div class="bg-soft-success p-3 rounded-circle text-success">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Desa Non-Aktif</small>
                                <h3 class="fw-bold mb-0 text-danger">{{ $stats['inactive'] }}</h3>
                            </div>
                            <div class="bg-soft-danger p-3 rounded-circle text-danger">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <form action="{{ route('kecamatan.master.desa.index') }}" method="GET" class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-light bg-light"
                                placeholder="Cari Kode atau Nama Desa..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select border-light bg-light" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <button type="submit" class="btn btn-teal px-4">Cari Data</button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('kecamatan.master.desa.index') }}" class="btn btn-light"><i
                                    class="fas fa-undo"></i></a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Kode Desa</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Nama Desa</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Kecamatan</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Website</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">Status</th>
                            <th class="pe-4 py-3 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($villages as $desa)
                            <tr>
                                <td class="ps-4 py-3 fw-bold text-primary">{{ $desa->kode_desa }}</td>
                                <td>{{ $desa->nama_desa }}</td>
                                <td>{{ $desa->kecamatan }}</td>
                                <td>
                                    @if($desa->website)
                                        <a href="{{ $desa->website }}" target="_blank"
                                            class="btn btn-xs btn-light border py-1 px-2 rounded-3" style="font-size: 10px;">
                                            <i class="fas fa-external-link-alt text-primary me-1"></i> Profil Desa
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($desa->status == 'aktif')
                                        <span class="badge bg-soft-success text-success rounded-pill px-3">Aktif</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle me-1"
                                        onclick="editDesa('{{ $desa->id }}', '{{ $desa->nama_desa }}', '{{ $desa->status }}', '{{ $desa->kode_desa }}', '{{ $desa->website }}')"
                                        title="Edit Data" style="background-color: #3b82f6; color: white; border: none;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('kecamatan.master.desa.destroy', $desa->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Nonaktifkan desa ini? Seluruh referensi data lama akan tetap tersimpan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-light rounded-circle text-danger"
                                            title="Nonaktifkan" style="background-color: #ef4444; color: white; border: none;">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">Tidak ada data desa ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($villages->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $villages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('modal')
    <!-- Add Modal -->
    <div class="modal fade" id="addDesaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold">Tambah Desa Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('kecamatan.master.desa.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode Desa (Kemendagri)</label>
                            <input type="text" name="kode_desa" class="form-control" placeholder="Contoh: 35.13.13.2001"
                                required>
                            <div class="form-text small">Kode desa dapat disesuaikan kembali jika terdapat kesalahan data.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Desa</label>
                            <input type="text" name="nama_desa" class="form-control" placeholder="Masukan nama desa"
                                required>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control"
                                    value="{{ appProfile()->region_name }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Kabupaten</label>
                                <input type="text" name="kabupaten" class="form-control" value="Probolinggo" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Website Desa (Opsional)</label>
                            <input type="text" name="website" class="form-control"
                                placeholder="Contoh: www.nama-desa.desa.id">
                        </div>
                        <input type="hidden" name="status" value="aktif">
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4"
                            style="background-color: #4f46e5; border-color: #4f46e5; color: white;">Simpan Desa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editDesaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-teal text-white border-0 py-3">
                    <h5 class="modal-title fw-bold">Ubah Data Desa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDesaForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode Desa (Kemendagri)</label>
                            <input type="text" name="kode_desa" id="edit_kode_desa" class="form-control" required>
                            <div class="form-text small">Gunakan kode resmi Kemendagri (contoh: 35.13.13.2001).
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Desa</label>
                            <input type="text" name="nama_desa" id="edit_nama_desa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Website Desa</label>
                            <input type="text" name="website" id="edit_website" class="form-control"
                                placeholder="Contoh: www.nama-desa.desa.id">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Status Referensi</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="aktif">Aktif (Digunakan dalam sistem)</option>
                                <option value="tidak_aktif">Tidak Aktif (Arsip)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-teal text-white px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function editDesa(id, nama, status, kode, website) {
            const form = document.getElementById('editDesaForm');
            form.action = `/kecamatan/master/desa/${id}`;

            // Update input values
            document.getElementById('edit_nama_desa').value = nama;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_kode_desa').value = kode;
            document.getElementById('edit_website').value = website || '';

            // Show modal using getOrCreateInstance to prevent multiple backdrops
            const modalEl = document.getElementById('editDesaModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Force Modal to be on top of backdrop */
        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .bg-soft-success {
            background: rgba(16, 185, 129, 0.1);
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.1);
        }

        .btn-teal {
            background: #14b8a6;
            color: white;
            border: none;
        }

        .btn-teal:hover {
            background: #0d9488;
            color: white;
        }

        .bg-teal {
            background: #14b8a6;
        }
    </style>
@endpush