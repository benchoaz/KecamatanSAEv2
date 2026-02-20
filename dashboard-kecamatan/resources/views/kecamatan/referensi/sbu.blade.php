@extends('layouts.kecamatan')

@section('title', 'Master Standar Biaya Umum (SBU)')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-slate-800 mb-1">Master SBU</h2>
                <p class="text-slate-500 small">Kelola standar biaya umum (Honorarium, Perjalanan Dinas, dll).</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalAddSbu">
                    <i class="fas fa-plus me-2"></i> Tambah Item SBU
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 text-slate-600 small fw-bold">
                            <tr>
                                <th class="ps-4 py-3">NAMA BIAYA</th>
                                <th>SATUAN</th>
                                <th>BATAS MAKSIMUM</th>
                                <th>KOMPONEN BELANJA</th>
                                <th class="text-center">TAHUN</th>
                                <th class="text-center pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700">
                            @forelse($sbu as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $item->nama_biaya }}</div>
                                    </td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        <div class="fw-bold text-red-600">Rp {{ number_format($item->batas_maks, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-purple-50 text-purple-700 fw-medium">
                                            {{ $item->komponenBelanja->nama_komponen }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $item->tahun }}</td>
                                    <td class="text-center pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditSbu{{ $item->id }}">
                                                        <i class="fas fa-edit me-2 text-primary"></i> Edit Data
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('kecamatan.pembangunan.referensi.sbu.destroy', $item->id) }}"
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

                                <!-- Modal Edit SBU -->
                                <div class="modal fade" id="modalEditSbu{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form action="{{ route('kecamatan.pembangunan.referensi.sbu.update', $item->id) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-0 p-4 pb-0">
                                                    <h5 class="fw-bold text-slate-800">Edit Item SBU</h5>
                                                    <button type="button" class="btn-close" data-bs-close="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-slate-600">Nama
                                                            Biaya</label>
                                                        <input type="text" name="nama_biaya" class="form-control rounded-3"
                                                            value="{{ $item->nama_biaya }}" required>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label
                                                                class="form-label small fw-bold text-slate-600">Satuan</label>
                                                            <input type="text" name="satuan" class="form-control rounded-3"
                                                                value="{{ $item->satuan }}"
                                                                placeholder="ex: Orang/Jam, OT, Trip" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-slate-600">Tahun</label>
                                                            <input type="number" name="tahun" class="form-control rounded-3"
                                                                value="{{ $item->tahun }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-slate-600">Batas Maksimum
                                                            (Rp)</label>
                                                        <input type="number" name="batas_maks" class="form-control rounded-3"
                                                            value="{{ $item->batas_maks }}" required>
                                                    </div>
                                                    <div class="mb-0">
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
                                        <div class="text-slate-300 mb-3"><i class="fas fa-file-invoice-dollar fa-4x"></i></div>
                                        <p class="text-slate-500">Belum ada data SBU yang diinput.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($sbu->hasPages())
                <div class="card-footer bg-white border-0 p-4">
                    {{ $sbu->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Add SBU -->
    <div class="modal fade" id="modalAddSbu" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('kecamatan.pembangunan.referensi.sbu.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold text-slate-800">Tambah Item SBU</h5>
                        <button type="button" class="btn-close" data-bs-close="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-600">Nama Biaya</label>
                            <input type="text" name="nama_biaya" class="form-control rounded-3"
                                placeholder="Nama honorarium atau jasa..." required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">Satuan</label>
                                <input type="text" name="satuan" class="form-control rounded-3"
                                    placeholder="OT, Orang/Hari, Jam..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">Tahun</label>
                                <input type="number" name="tahun" class="form-control rounded-3" value="{{ date('Y') }}"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-600">Batas Maksimum (Rp)</label>
                            <input type="number" name="batas_maks" class="form-control rounded-3" placeholder="0" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-slate-600">Komponen Belanja</label>
                            <select name="komponen_belanja_id" class="form-select rounded-3" required>
                                <option value="">Pilih Komponen...</option>
                                @foreach($komponens as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_komponen }}</option>
                                @endforeach
                            </select>
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

        .bg-purple-50 {
            background-color: #f5f3ff;
        }

        .text-purple-700 {
            color: #6d28d9;
        }
    </style>
@endsection