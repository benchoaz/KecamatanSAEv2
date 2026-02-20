@extends(auth()->user()->desa_id ? 'layouts.desa' : 'layouts.kecamatan')

@section('title', $title ?? 'Administrasi Perangkat Desa')

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
                <h2 class="fw-bold text-primary-900 mb-1">{{ $title ?? 'Administrasi Perangkat Desa' }}</h2>
                <p class="text-tertiary mb-0">
                    @if($desa_id)
                        <i class="fas fa-circle-info me-1"></i> Manajemen data riwayat, legalitas, dan kontak personil desa.
                    @else
                        <i class="fas fa-map-location-dot me-1"></i> Pilih desa dibawah ini untuk melakukan monitoring
                        administratif.
                    @endif
                </p>
            </div>
            @if($desa_id)
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-premium" data-bs-toggle="modal"
                    data-bs-target="#addPersonilModal">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Data Personil
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
                            @if(isset($kategori) && $kategori == 'bpd')
                                <th class="text-center py-3">ANGGOTA BPD</th>
                            @else
                                <th class="text-center py-3">KEPALA DESA</th>
                                <th class="text-center py-3">PERANGKAT</th>
                            @endif
                            <th class="text-center py-3">TOTAL</th>
                            <th class="text-end pe-4 py-3">KENDALI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($desas as $index => $desa)
                            <tr>
                                <td class="ps-4 text-secondary small fw-medium">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-brand-50 text-brand-600 rounded-3 d-flex align-items-center justify-content-center"
                                            style="width: 42px; height: 42px;">
                                            <i class="fas fa-map-location-dot"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-primary-900">Desa {{ $desa->nama_desa }}</div>
                                            <small class="text-tertiary">{{ appProfile()->region_level }}
                                                {{ appProfile()->region_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                @if(isset($kategori) && $kategori == 'bpd')
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-brand-50 text-brand-600 px-3">{{ $desa->bpd_count }}
                                            Orang</span>
                                    </td>
                                    <td class="text-center fw-bold text-primary-900">
                                        {{ $desa->bpd_count }}
                                    </td>
                                @else
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-emerald-50 text-emerald-600 px-3 fw-bold">{{ $desa->kades_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-brand-50 text-brand-600 px-3 fw-bold">{{ $desa->perangkat_count }}</span>
                                    </td>
                                    <td class="text-center fw-bold text-primary-900">
                                        {{ $desa->kades_count + $desa->perangkat_count }}
                                    </td>
                                @endif
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
                            <th class="ps-4 py-3">FOTO & IDENTITAS</th>
                            <th class="py-3">JABATAN</th>
                            <th class="py-3">MASA JABATAN</th>
                            <th class="py-3">LEGALITAS (SK)</th>
                            <th class="py-3">KONTAK</th>
                            <th class="py-3">STATUS</th>
                            <th class="text-end pe-4 py-3">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($personils as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="personil-photo">
                                            @if($p->foto)
                                                <img src="{{ route('kecamatan.file.personil-foto', $p->id) }}" alt="Foto {{ $p->nama }}"
                                                    class="rounded-circle object-fit-cover"
                                                    style="width: 48px; height: 48px; border: 2px solid var(--brand-100);">
                                            @else
                                                <div class="rounded-circle bg-brand-50 text-brand-600 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                    style="width: 48px; height: 48px;">
                                                    {{ strtoupper(substr($p->nama, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-primary-900">{{ $p->nama }}</div>
                                            <small class="text-tertiary">NIK: {{ $p->nik }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-brand-50 text-brand-600 border-0 px-3 fw-semibold">
                                        {{ $p->jabatan }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-primary-700 fw-medium">
                                        {{ $p->masa_jabatan_mulai ? $p->masa_jabatan_mulai->format('d/m/Y') : '-' }}
                                        <span class="text-tertiary mx-1">s/d</span>
                                        <span
                                            class="{{ $p->masa_jabatan_selesai && $p->masa_jabatan_selesai->isPast() ? 'text-danger fw-bold' : '' }}">
                                            {{ $p->masa_jabatan_selesai ? $p->masa_jabatan_selesai->format('d/m/Y') : 'Sekarang' }}
                                        </span>
                                    </div>
                                    @if($p->kategori == 'perangkat')
                                        <div class="x-small text-tertiary mt-1">
                                            @if(str_contains(strtolower($p->jabatan), 'kepala desa'))
                                                <i class="fas fa-clock-rotate-left me-1"></i> Sesuai Aturan 8 Thn
                                            @else
                                                <i class="fas fa-user-clock me-1"></i> Pensiun Usia 60 Thn
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small class="text-tertiary">No. SK:</small>
                                        <span class="small fw-semibold text-primary-900">{{ $p->nomor_sk ?? '-' }}</span>
                                        @if($p->file_sk)
                                            <a href="{{ route('kecamatan.file.personil', $p->id) }}" target="_blank"
                                                class="text-brand-600 small mt-1 text-decoration-none fw-bold">
                                                <i class="fas fa-file-pdf me-1"></i> Buka Dokumen
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($p->no_hp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->no_hp) }}" target="_blank"
                                            class="btn btn-xs btn-success rounded-pill px-3 shadow-sm">
                                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                        </a>
                                    @else
                                        <span class="text-tertiary small">Tidak ada nomor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $p->status_badge }} rounded-pill px-3">{{ $p->status_label }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="#" class="btn btn-icon btn-light rounded-circle shadow-sm text-primary-600"
                                            title="Edit Profil">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>

                                        @if($p->status != 'diterima')
                                            <form action="{{ route('kecamatan.pemerintahan.detail.personil.verify', $p->id) }}"
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
                                                data-bs-toggle="modal" data-bs-target="#revisionModal{{ $p->id }}" title="Minta Revisi">
                                                <i class="fas fa-reply"></i>
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-icon btn-light rounded-circle shadow-sm text-danger"
                                            title="Nonaktifkan">
                                            <i class="fas fa-power-off"></i>
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
                                            <i class="fas fa-users-slash fa-2x"></i>
                                        </div>
                                        <h5 class="text-primary-900 fw-bold">Belum Ada Data</h5>
                                        <p class="text-tertiary">Silakan tambahkan data personil baru untuk desa ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Simple Placeholder Modal for Demo -->
    <div class="modal fade" id="addPersonilModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary-900 text-white rounded-top-4 py-3 px-4">
                    <h5 class="modal-title fw-bold">Tambah Data Personil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ $store_route }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="kategori" value="{{ $kategori ?? 'perangkat' }}">
                    <input type="hidden" name="desa_id" value="{{ $desa_id }}">

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <!-- Foto Section -->
                            <div class="col-12 text-center mb-2">
                                <div class="position-relative d-inline-block">
                                    <div class="rounded-circle border-dashed border-2 border-primary-200 d-flex align-items-center justify-content-center bg-primary-50"
                                        style="width: 120px; height: 120px;">
                                        <i class="fas fa-camera fa-2x text-primary-300"></i>
                                    </div>
                                    <label for="fotoInput"
                                        class="position-absolute bottom-0 end-0 bg-brand-600 text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px; cursor: pointer;">
                                        <i class="fas fa-plus"></i>
                                        <input type="file" id="fotoInput" name="foto" class="d-none" accept="image/*">
                                    </label>
                                </div>
                                <div class="mt-2 small text-tertiary">Pas Foto (JPG/PNG, Max 1MB)</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control rounded-3 border-gray-200"
                                    placeholder="Contoh: Budi Santoso, S.T" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">NIK (16 Digit)</label>
                                <input type="text" name="nik" class="form-control rounded-3 border-gray-200" maxlength="16"
                                    placeholder="Masukkan 16 digit NIK" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control rounded-3 border-gray-200"
                                    placeholder="Contoh: Probolinggo">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3 border-gray-200"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Nomor Telepon / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-success"><i
                                            class="fab fa-whatsapp"></i></span>
                                    <input type="text" name="no_hp" class="form-control rounded-3 border-start-0 ps-0"
                                        placeholder="Contoh: 08123456789">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Jabatan</label>
                                <select name="jabatan" class="form-select rounded-3 border-gray-200" required>
                                    @if(($kategori ?? 'perangkat') == 'perangkat')
                                        <option value="Kepala Desa">Kepala Desa</option>
                                        <option value="Sekretaris Desa">Sekretaris Desa</option>
                                        <option value="Kaur Keuangan">Kaur Keuangan</option>
                                        <option value="Kaur Perencanaan">Kaur Perencanaan</option>
                                        <option value="Kaur Umum">Kaur Umum</option>
                                        <option value="Kasi Pemerintahan">Kasi Pemerintahan</option>
                                        <option value="Kasi Kesejahteraan">Kasi Kesejahteraan</option>
                                        <option value="Kasi Pelayanan">Kasi Pelayanan</option>
                                        <option value="Kepala Dusun">Kepala Dusun</option>
                                    @else
                                        <option value="Ketua BPD">Ketua BPD</option>
                                        <option value="Wakil Ketua BPD">Wakil Ketua BPD</option>
                                        <option value="Sekretaris BPD">Sekretaris BPD</option>
                                        <option value="Anggota BPD">Anggota BPD</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Mulai Menjabat</label>
                                <input type="date" name="masa_jabatan_mulai" class="form-control rounded-3 border-gray-200">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Nomor SK</label>
                                <input type="text" name="nomor_sk" class="form-control rounded-3 border-gray-200"
                                    placeholder="Contoh: 188/02/426.411.02/2024">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary-900">Tanggal SK</label>
                                <input type="date" name="tanggal_sk" class="form-control rounded-3 border-gray-200">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-primary-900">Dokumen SK (PDF)</label>
                                <div class="input-group">
                                    <input type="file" name="file_sk" class="form-control rounded-3"
                                        accept="application/pdf">
                                    <span class="input-group-text bg-light text-muted small">Max 2MB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal"
                            data-bs-target="#addPersonilModal">Gagalkan</button>
                        <button type="submit" class="btn btn-brand-600 text-white rounded-pill px-5 shadow-sm">Simpan
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals for Revision moved outside table -->
    @section('modal')
        @if(isset($personils))
            @foreach($personils as $p)
                <div class="modal fade" id="revisionModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header bg-warning-subtle text-warning-emphasis fw-bold">
                                Catatan Revisi ({{ $p->nama }})
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('kecamatan.pemerintahan.detail.personil.verify', $p->id) }}" method="POST">
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

        .fw-500 {
            font-weight: 500;
        }
    </style>
@endpush