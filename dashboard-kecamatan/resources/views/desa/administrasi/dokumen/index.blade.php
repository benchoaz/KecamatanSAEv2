@extends('layouts.desa')

@section('title', 'Arsip Dokumen Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <a href="{{ route('desa.administrasi.index') }}" class="text-decoration-none text-slate-500 fw-medium">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Menu Utama
                </a>
                <h2 class="fw-bold text-slate-800 mt-2">
                    {{ $tipe == 'perdes' ? 'Peraturan Desa (Perdes)' : ($tipe == 'laporan' ? 'Laporan Tahunan' : 'Arsip Dokumen') }}
                </h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('desa.administrasi.dokumen.create', ['tipe' => $tipe == 'perdes' ? 'Perdes' : 'LKPJ']) }}" 
                   class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-2"></i> Tambah Dokumen Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 text-slate-600 fw-bold small uppercase">
                            <tr>
                                <th class="px-4 py-3">Tipe / Tahun</th>
                                <th class="py-3">Nomor Dokumen</th>
                                <th class="py-3">Perihal / Keterangan</th>
                                <th class="py-3">Status Verifikasi</th>
                                <th class="py-3 text-end px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumens as $dok)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold text-slate-800">{{ $dok->tipe_dokumen }}</div>
                                        <div class="text-slate-500 small">Tahun {{ $dok->tahun }}</div>
                                    </td>
                                    <td>
                                        <span class="text-slate-700">{{ $dok->nomor_dokumen }}</span>
                                    </td>
                                    <td style="max-width: 300px;">
                                        <div class="text-truncate" title="{{ $dok->perihal }}">
                                            {{ $dok->perihal }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($dok->status) {
                                                'draft' => 'bg-slate-100 text-slate-600',
                                                'dikirim' => 'bg-blue-100 text-blue-600',
                                                'dikembalikan' => 'bg-danger-100 text-danger-600',
                                                'diterima' => 'bg-success-100 text-success-600',
                                                default => 'bg-slate-100 text-slate-600'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 small fw-bold">
                                            {{ $dok->status_label }}
                                        </span>
                                        @if($dok->catatan)
                                            <div class="text-danger small mt-1"><i class="fas fa-comment-dots me-1"></i> Ada revisi</div>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('desa.administrasi.file.dokumen', $dok->id) }}" target="_blank" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Lihat File">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            </a>
                                            
                                            @if($dok->isEditable())
                                                <a href="{{ route('desa.administrasi.dokumen.edit', $dok->id) }}" class="btn btn-sm btn-white border rounded-circle shadow-sm" title="Edit">
                                                    <i class="fas fa-edit text-primary"></i>
                                                </a>
                                                
                                                <form action="{{ route('desa.administrasi.dokumen.submit', $dok->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-circle shadow-sm" title="Kirim ke Kecamatan" onclick="return confirm('Kirim dokumen ini untuk diverifikasi?')">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('desa.administrasi.dokumen.destroy', $dok->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Hapus" onclick="return confirm('Hapus dokumen ini?')">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-slate-400">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p class="mb-0">Belum ada dokumen yang diarsip.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($dokumens->hasPages())
                <div class="card-footer bg-white py-3 px-4">
                    {{ $dokumens->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
