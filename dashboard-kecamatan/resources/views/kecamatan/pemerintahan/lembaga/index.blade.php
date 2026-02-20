@extends('layouts.kecamatan')

@section('title', 'Lembaga & Organisasi Kemasyarakatan Desa')

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
                <h2 class="fw-bold text-primary-900 mb-1">Registrasi Lembaga Desa</h2>
                <p class="text-tertiary mb-0">
                    @if($desa_id)
                        <i class="fas fa-sitemap me-1"></i> Daftar Legalitas PKK, Karang Taruna, LPM, Posyandu, dan Lainnya.
                    @else
                        <i class="fas fa-map-location-dot me-1"></i> Pilih Desa untuk Melihat Detail Administrasi Lembaga.
                    @endif
                </p>
            </div>
            @if($desa_id)
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-premium" data-bs-toggle="modal"
                    data-bs-target="#addLembagaModal">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Lembaga
                </button>
            @endif
        </div>
    </div>

    @if(!$desa_id)
        <div class="card border-0 shadow-premium rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary-900 text-white small fw-bold">
                        <tr>
                            <th class="ps-4 py-3" style="width: 70px;">NO</th>
                            <th class="py-3">NAMA DESA</th>
                            <th class="py-3">LEMBAGA TERDAFTAR</th>
                            <th class="text-center py-3">JUMLAH</th>
                            <th class="text-end pe-4 py-3">KENDALI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($desas as $index => $desa)
                            <tr>
                                <td class="ps-4 text-secondary small fw-medium">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="fw-bold text-primary-900"> Desa {{ $desa->nama_desa }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($desa->lembaga->take(3) as $l)
                                            <span class="badge rounded-pill bg-brand-50 text-brand-600 px-2 py-1"
                                                style="font-size: 0.7rem;">{{ $l->nama_lembaga }}</span>
                                        @empty
                                            <span class="text-tertiary small italic text-muted">Belum ada data</span>
                                        @endforelse
                                        @if($desa->lembaga->count() > 3)
                                            <span class="text-tertiary small">+{{ $desa->lembaga->count() - 3 }} lainnya</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-primary-900">
                                    {{ $desa->lembaga->count() }}
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                                        class="btn btn-sm btn-brand-600 text-white rounded-pill px-4 shadow-sm">
                                        Detail <i class="fas fa-chevron-right ms-2 small"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-premium rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary-900 text-white small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">NAMA LEMBAGA / TIPE</th>
                            <th class="py-3">KETUA</th>
                            <th class="py-3">TAHUN FORMASI</th>
                            <th class="py-3">LEGALITAS (SK)</th>
                            <th class="py-3">STATUS</th>
                            <th class="text-end pe-4 py-3">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($lembagas as $l)
                            <tr>
                                <td class="ps-4 text-primary-900">
                                    <div class="fw-bold">{{ $l->nama_lembaga }}</div>
                                    <span class="badge rounded-pill bg-brand-50 text-brand-600 text-uppercase"
                                        style="font-size: 0.65rem;">
                                        {{ $l->tipe_lembaga }}
                                    </span>
                                </td>
                                <td class="text-secondary fw-medium">{{ $l->ketua ?? '-' }}</td>
                                <td class="text-secondary">{{ $l->tahun_pembentukan ?? '-' }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="small fw-semibold text-primary-900">{{ $l->nomor_sk ?? 'Belum ada SK' }}</span>
                                        @if($l->file_sk)
                                            <a href="{{ route('kecamatan.file.lembaga', $l->id) }}"
                                                class="text-brand-600 small mt-1 text-decoration-none fw-bold" target="_blank">
                                                <i class="fas fa-file-pdf me-1"></i> Buka SK
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $l->status_badge ?? 'bg-secondary' }} rounded-pill px-3">{{ $l->status_label ?? 'Draft' }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="#" class="btn btn-icon btn-light rounded-circle shadow-sm text-primary-600"
                                            title="Edit Data">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>

                                        @if($l->status != 'diterima')
                                            <form action="{{ route('kecamatan.pemerintahan.detail.lembaga.verify', $l->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="diterima">
                                                <button type="submit"
                                                    class="btn btn-icon btn-success rounded-circle shadow-sm text-white"
                                                    title="Verifikasi / Terima">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-icon btn-warning rounded-circle shadow-sm text-white"
                                                data-bs-toggle="modal" data-bs-target="#revisionModal{{ $l->id }}" title="Minta Revisi">
                                                <i class="fas fa-reply"></i>
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-icon btn-light rounded-circle shadow-sm text-danger"
                                            title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>


                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="bg-primary-50 text-primary-200 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-sitemap fa-3x"></i>
                                        </div>
                                        <h5 class="text-primary-900 fw-bold">Data lembaga desa belum diinput.</h5>
                                        <p class="text-tertiary">Silakan tambahkan data lembaga baru untuk desa ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal Tambah Lembaga -->
    <div class="modal fade" id="addLembagaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Lembaga Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('kecamatan.pemerintahan.detail.lembaga.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Tipe Lembaga</label>
                                <select name="tipe_lembaga" class="form-select" required>
                                    <option value="PKK">PKK (Pemberdayaan Kesejahteraan Keluarga)</option>
                                    <option value="Karang Taruna">Karang Taruna</option>
                                    <option value="LPM">LPM (Lembaga Pemberdayaan Masyarakat)</option>
                                    <option value="Posyandu">Posyandu</option>
                                    <option value="RT/RW">Forum RT/RW</option>
                                    <option value="Lainnya">Lembaga Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nama Lembaga (Sesuai SK)</label>
                                <input type="text" name="nama_lembaga" class="form-control"
                                    placeholder="Contoh: PKK Desa Sukamaju" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nama Ketua</label>
                                <input type="text" name="ketua" class="form-control" placeholder="Nama Lengkap">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No SK</label>
                                <input type="text" name="nomor_sk" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun Formasi</label>
                                <input type="number" name="tahun_pembentukan" class="form-control" placeholder="YYYY">
                            </div>
                            <div class="col-12">
                                <label class="form-label">File SK (PDF)</label>
                                <input type="file" name="file_sk" class="form-control" accept="application/pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Lembaga</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals for Revision moved outside table -->
    @section('modal')
        @if(isset($lembagas))
            @foreach($lembagas as $l)
                <div class="modal fade" id="revisionModal{{ $l->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header bg-warning-subtle text-warning-emphasis fw-bold">
                                Catatan Revisi ({{ $l->nama_lembaga }})
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('kecamatan.pemerintahan.detail.lembaga.verify', $l->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="dikembalikan">
                                <div class="modal-body text-start">
                                    <label class="form-label fw-bold small">Alasan Pengembalian</label>
                                    <textarea name="catatan" class="form-control" rows="3" required
                                        placeholder="Jelaskan data yang perlu diperbaiki..."></textarea>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning rounded-pill px-4">Kirim Revisi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endsection

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .bg-primary-soft {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(16, 185, 129, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-xs {
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
        }
    </style>
@endpush