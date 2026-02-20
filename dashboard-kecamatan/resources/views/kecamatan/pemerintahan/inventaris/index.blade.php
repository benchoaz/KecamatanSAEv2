@extends('layouts.kecamatan')

@section('title', 'Inventaris & Aset Desa')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ auth()->user()->desa_id ? route('desa.pemerintahan.index') : route('kecamatan.pemerintahan.index') }}"
                class="btn btn-xs btn-light rounded-pill px-3 text-secondary text-decoration-none border shadow-sm">
                <i class="fas fa-arrow-left-long me-2"></i> Kembali ke Menu Utama
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-bold text-primary-900 mb-1">Inventaris & Aset Desa</h2>
                <p class="text-tertiary mb-0">
                    @if($desa_id)
                        <i class="fas fa-boxes-stacked me-1"></i> Pendataan Barang Milik Desa & Tanah Kas Desa.
                    @else
                        <i class="fas fa-map-location-dot me-1"></i> Pilih Desa untuk Melihat Detail Administrasi Aset.
                    @endif
                </p>
            </div>
            @if($desa_id)
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-premium" data-bs-toggle="modal"
                    data-bs-target="#addInventarisModal">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Aset
                </button>
            @endif
        </div>
    </div>

    @if(!$desa_id)
        <div class="card bg-white border-gray-200 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Nama Desa</th>
                            <th class="text-center">Total Inventaris & Aset</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desas as $index => $desa)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-slate-700"> Desa {{ $desa->nama_desa }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-soft text-primary px-3 py-2"
                                        style="font-size: 0.85rem;">{{ $desa->inventaris_count }} Aset</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card bg-white border-gray-200 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-gray-800 fw-bold">Daftar Inventaris</h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary active">Semua</button>
                            <button class="btn btn-outline-secondary">Barang</button>
                            <button class="btn btn-outline-secondary">Tanah</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4">Nama Aset / Tipe</th>
                                    <th>Tahun / Sumber</th>
                                    <th>Detail / Lokasi</th>
                                    <th>Kondisi</th>
                                    <th>Legalitas / Sengketa</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventaris as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $item->nama_item }}</div>
                                            <span
                                                class="badge {{ $item->tipe_aset == 'tanah' ? 'bg-info-soft text-info' : 'bg-primary-soft text-primary' }} text-uppercase"
                                                style="font-size: 0.65rem;">
                                                {{ $item->tipe_aset }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $item->tahun_perolehan ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->sumber_dana ?? 'Lainnya' }}</small>
                                        </td>
                                        <td>
                                            @if($item->tipe_aset == 'tanah')
                                                <div class="small">Luas: {{ $item->luas ?? '-' }} m²</div>
                                                <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $item->lokasi ?? '-' }}</div>
                                            @else
                                                <div class="small text-muted">SN/Kode: {{ $item->kode_item ?? '-' }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $kondisiClass = [
                                                    'Baik' => 'bg-success',
                                                    'Rusak Ringan' => 'bg-warning',
                                                    'Rusak Berat' => 'bg-danger',
                                                ][$item->kondisi] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $kondisiClass }}">{{ $item->kondisi ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($item->tipe_aset == 'tanah')
                                                <div class="small fw-500">{{ $item->nomor_legalitas ?? 'Tanpa Sertifikat' }}</div>
                                                @php
                                                    $sengketaClass = [
                                                        'aman' => 'text-success',
                                                        'sengketa' => 'text-danger',
                                                        'klaim' => 'text-warning',
                                                    ][$item->status_sengketa] ?? 'text-muted';
                                                @endphp
                                                <div class="small {{ $sengketaClass }} text-uppercase fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    <i class="fas fa-shield-halved me-1"></i> Status: {{ $item->status_sengketa }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-icon btn-sm"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-icon btn-sm text-danger"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-boxes-stacked fa-3x mb-3 text-muted"></i>
                                                <p>Belum ada data aset terdaftar.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Modal Tambah Aset -->
    <div class="modal fade" id="addInventarisModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('kecamatan.pemerintahan.detail.inventaris.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="desa_id" value="{{ $desa_id }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipe Aset</label>
                                <select name="tipe_aset" class="form-select" id="tipeAsetSelect" required>
                                    <option value="barang">Barang (Laptop, Kursi, dkk)</option>
                                    <option value="tanah">Tanah / Properti</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Aset / Item</label>
                                <input type="text" name="nama_item" class="form-control"
                                    placeholder="Contoh: Tanah Kas Desa" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun Perolehan</label>
                                <input type="number" name="tahun_perolehan" class="form-control" placeholder="YYYY"
                                    min="1900" max="{{ date('Y') }}">
                            </div>
                            <div class="col-md-6" id="kondisiField">
                                <label class="form-label">Kondisi</label>
                                <select name="kondisi" class="form-select">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-none" id="luasField">
                                <label class="form-label">Luas (m²)</label>
                                <input type="text" name="luas" class="form-control" placeholder="1000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Legalitas / Sengketa</label>
                                <select name="status_sengketa" class="form-select" required>
                                    <option value="aman">Aman / Clean</option>
                                    <option value="sengketa">Dalam Sengketa Hukum</option>
                                    <option value="klaim">Ada Klaim Pihak Lain</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Nomor Legalitas (Sertifikat / Letter C / No Kontrak)</label>
                                <input type="text" name="nomor_legalitas" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Aset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.getElementById('tipeAsetSelect').addEventListener('change', function () {
            const type = this.value;
            const luasField = document.getElementById('luasField');
            const kondisiField = document.getElementById('kondisiField');

            if (type === 'tanah') {
                luasField.classList.remove('d-none');
                kondisiField.classList.add('d-none');
            } else {
                luasField.classList.add('d-none');
                kondisiField.classList.remove('d-none');
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .bg-primary-soft {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .bg-info-soft {
            background-color: rgba(6, 182, 212, 0.1);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    </style>
@endpush