@extends('layouts.kecamatan')

@section('title', 'Master Standar Satuan Harga (SSH)')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-slate-800 mb-1">Master SSH</h2>
                <p class="text-slate-500 small">Kelola standar satuan harga barang dan jasa sebagai acuan desa.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalAddSsh">
                    <i class="fas fa-plus me-2"></i> Tambah Item SSH
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 text-slate-600 small fw-bold">
                            <tr>
                                <th class="ps-4 py-3">NAMA ITEM</th>
                                <th>SATUAN</th>
                                <th>HARGA WAJAR (MIN - MAX)</th>
                                <th>KOMPONEN BELANJA</th>
                                <th>TAHUN / WILAYAH</th>
                                <th class="text-center pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700">
                            @forelse($ssh as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $item->nama_item }}</div>
                                    </td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="small text-slate-400">Min: Rp
                                                {{ number_format($item->harga_wajar_min, 0, ',', '.') }}</span>
                                            <span class="fw-bold text-emerald-600">Max: Rp
                                                {{ number_format($item->harga_wajar_max, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-50 text-blue-700 fw-medium">
                                            {{ $item->komponenBelanja->nama_komponen }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">{{ $item->tahun }}</div>
                                        <div class="small text-slate-400">{{ $item->wilayah ?? 'Seluruh Wilayah' }}</div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditSsh{{ $item->id }}">
                                                        <i class="fas fa-edit me-2 text-primary"></i> Edit Data
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('kecamatan.pembangunan.referensi.ssh.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus item ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                                            <i class="fas fa-trash-alt me-2"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Edit SSH -->
                                <div class="modal fade" id="modalEditSsh{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form action="{{ route('kecamatan.pembangunan.referensi.ssh.update', $item->id) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-0 p-4 pb-0">
                                                    <h5 class="fw-bold text-slate-800">Edit Item SSH</h5>
                                                    <button type="button" class="btn-close" data-bs-close="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-slate-600">Nama Item</label>
                                                        <input type="text" name="nama_item" class="form-control rounded-3"
                                                            value="{{ $item->nama_item }}" required>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label
                                                                class="form-label small fw-bold text-slate-600">Satuan</label>
                                                            <input type="text" name="satuan" class="form-control rounded-3"
                                                                value="{{ $item->satuan }}" placeholder="ex: m2, Pkt, Unit"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-slate-600">Tahun</label>
                                                            <input type="number" name="tahun" class="form-control rounded-3"
                                                                value="{{ $item->tahun }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-slate-600">Harga Min
                                                                (Rp)</label>
                                                            <input type="number" name="harga_wajar_min"
                                                                class="form-control rounded-3"
                                                                value="{{ $item->harga_wajar_min }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-slate-600">Harga Max
                                                                (Rp)</label>
                                                            <input type="number" name="harga_wajar_max"
                                                                class="form-control rounded-3"
                                                                value="{{ $item->harga_wajar_max }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-slate-600">Komponen
                                                            Belanja</label>
                                                        <select name="komponen_belanja_id" class="form-select rounded-3"
                                                            required>
                                                            @foreach($komponens as $k)
                                                                <option value="{{ $k->id }}" {{ $item->komponen_belanja_id == $k->id ? 'selected' : '' }}>
                                                                    {{ $k->nama_komponen }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label small fw-bold text-slate-600">Wilayah
                                                            (Opsional)</label>
                                                        <input type="text" name="wilayah" class="form-control rounded-3"
                                                            value="{{ $item->wilayah }}"
                                                            placeholder="Nama Desa atau Wilayah Spesifik">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit"
                                                        class="btn btn-brand-600 text-white rounded-pill px-4">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-slate-300 mb-3"><i class="fas fa-tags fa-4x"></i></div>
                                        <p class="text-slate-500">Belum ada data SSH yang diinput.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($ssh->hasPages())
                <div class="card-footer bg-white border-0 p-4">
                    {{ $ssh->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Add SSH -->
    <div class="modal fade" id="modalAddSsh" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('kecamatan.pembangunan.referensi.ssh.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold text-slate-800">Tambah Item SSH</h5>
                        <button type="button" class="btn-close" data-bs-close="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-600">Nama Item</label>
                            <input type="text" name="nama_item" class="form-control rounded-3"
                                placeholder="Nama barang atau jasa..." required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">Satuan</label>
                                <input type="text" name="satuan" class="form-control rounded-3"
                                    placeholder="m2, HOK, Unit..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">Tahun</label>
                                <input type="number" name="tahun" class="form-control rounded-3" value="{{ date('Y') }}"
                                    required>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">Harga Min (Rp)</label>
                                <input type="number" name="harga_wajar_min" class="form-control rounded-3" placeholder="0"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">Harga Max (Rp)</label>
                                <input type="number" name="harga_wajar_max" class="form-control rounded-3" placeholder="0"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-600">Komponen Belanja</label>
                            <select name="komponen_belanja_id" class="form-select rounded-3" required>
                                <option value="">Pilih Komponen...</option>
                                @foreach($komponens as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_komponen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-slate-600">Wilayah (Opsional)</label>
                            <input type="text" name="wilayah" class="form-control rounded-3"
                                placeholder="Nama Desa atau Wilayah Spesifik">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-brand-600 text-white rounded-pill px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .text-slate-800 {
            color: #1e293b;
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

        .btn-brand-600 {
            background-color: #2563eb;
        }

        .btn-brand-600:hover {
            background-color: #1d4ed8;
            color: white;
        }

        .bg-emerald-50 {
            background-color: #ecfdf5;
        }

        .text-emerald-600 {
            color: #059669;
        }
    </style>
@endsection