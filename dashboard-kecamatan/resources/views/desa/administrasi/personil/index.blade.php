@extends('layouts.desa')

@section('title', 'Data ' . ($kategori == 'perangkat' ? 'Perangkat Desa' : 'Anggota BPD'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">
                    Data {{ $kategori == 'perangkat' ? 'Perangkat Desa' : 'Anggota BPD' }}
                </h3>
                <p class="text-slate-500 mb-0">Kelola dan arsipkan data personil desa.</p>
            </div>
            <a href="{{ route('desa.administrasi.personil.create', ['kategori' => $kategori]) }}"
                class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Baru
            </a>
        </div>

        <!-- Alert Status Info -->
        <div class="alert bg-white border-start border-4 border-info shadow-sm rounded-3 mb-4 p-3">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-info-circle text-info fa-lg"></i>
                <div class="small">
                    <strong>Status Data:</strong>
                    <span class="badge bg-secondary me-1">Draft</span> Belum dikirim (Bisa Edit) &bull;
                    <span class="badge bg-primary me-1">Verifikasi</span> Sedang diperiksa Kecamatan (Read-only) &bull;
                    <span class="badge bg-success me-1">Terverifikasi</span> Data valid & diterima (Read-only) &bull;
                    <span class="badge bg-danger">Revisi</span> Dikembalikan dengan catatan (Bisa Edit)
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-slate-600 small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Nama Lengkap</th>
                            <th class="py-3">Jabatan</th>
                            <th class="py-3">SK Pengangkatan</th>
                            @if($kategori == 'bpd')
                                <th class="py-3">Masa Jabatan</th>
                            @endif
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personils as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary fw-bold border"
                                            style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($p->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-800">{{ $p->nama }}</div>
                                            <small class="text-slate-500">NIK: {{ $p->nik }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $p->jabatan }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-slate-700">{{ $p->nomor_sk }}</span>
                                        @if($p->file_sk)
                                            <a href="{{ route('desa.administrasi.file.personil', $p->id) }}" target="_blank"
                                                class="small text-primary text-decoration-none">
                                                <i class="fas fa-paperclip me-1"></i> Lihat File
                                            </a>
                                        @else
                                            <span class="text-danger small"><i class="fas fa-exclamation-circle"></i> Belum
                                                upload</span>
                                        @endif
                                    </div>
                                </td>
                                @if($kategori == 'bpd')
                                    <td>
                                        <div class="small text-slate-600">
                                            {{ $p->masa_jabatan_mulai ? $p->masa_jabatan_mulai->format('d M Y') : '-' }} <br>
                                            s/d <span
                                                class="text-secondary">{{ $p->masa_jabatan_selesai ? $p->masa_jabatan_selesai->format('d M Y') : 'Sekarang' }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td class="text-center">
                                    <span class="badge {{ $p->status_badge }} rounded-pill px-3 py-2">
                                        {{ $p->status_label }}
                                    </span>
                                    @if($p->status == 'dikembalikan' && $p->catatan)
                                        <div class="mt-1">
                                            <small class="text-danger" data-bs-toggle="tooltip" title="{{ $p->catatan }}">
                                                <i class="fas fa-comment-alt me-1"></i> Cek Revisi
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($p->isEditable())
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('desa.administrasi.personil.submit', $p->id) }}" method="POST"
                                                onsubmit="return confirm('Kirim data ke Kecamatan? Data tidak bisa diubah setelah dikirim.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                    title="Kirim ke Kecamatan">
                                                    <i class="fas fa-paper-plane me-1"></i> Kirim
                                                </button>
                                            </form>
                                            <a href="{{ route('desa.administrasi.personil.edit', $p->id) }}"
                                                class="btn btn-sm btn-outline-warning rounded-pill px-3" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-light text-secondary rounded-pill px-3" disabled>
                                            <i class="fas fa-lock me-1"></i> Terkunci
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $kategori == 'bpd' ? 6 : 5 }}" class="text-center py-5">
                                    <div class="text-slate-400 mb-3">
                                        <i class="fas fa-folder-open fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold text-slate-600">Belum ada data</h6>
                                    <p class="text-slate-400 small">Mulai tambahkan data personil desa Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $personils->appends(['kategori' => $kategori])->links() }}
            </div>
        </div>
    </div>
@endsection