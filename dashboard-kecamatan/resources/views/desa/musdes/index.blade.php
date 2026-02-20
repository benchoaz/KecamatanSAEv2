@extends('layouts.desa')

@section('title', 'Daftar Musyawarah Desa')

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-slate-800">Dokumen Musyawarah Desa</h3>
            <p class="text-muted mb-0">Arsip perencanaan dan kegiatan musyawarah desa.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('desa.musdes.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-plus me-2"></i> Input Musdes Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-secondary small fw-bold">Judul Kegiatan</th>
                        <th class="py-3 text-uppercase text-secondary small fw-bold">Periode</th>
                        <th class="py-3 text-uppercase text-secondary small fw-bold text-center">Status</th>
                        <th class="py-3 text-uppercase text-secondary small fw-bold text-center">Tanggal Kirim</th>
                        <th class="pe-4 py-3 text-end text-uppercase text-secondary small fw-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musdesList as $item)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">{{ $item->judul }}</div>
                                <div class="text-muted small">
                                    <i class="far fa-folder me-1"></i> Module: {{ ucfirst($item->modul) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-slate-100 text-slate-600 border">{{ $item->periode }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = match ($item->status) {
                                        'draft' => 'bg-amber-50 text-amber-600',
                                        'submitted' => 'bg-sky-50 text-sky-600',
                                        'returned' => 'bg-rose-50 text-rose-600',
                                        'completed' => 'bg-emerald-50 text-emerald-600',
                                        default => 'bg-slate-50 text-slate-600',
                                    };
                                    $statusLabel = match ($item->status) {
                                        'draft' => 'Draft',
                                        'submitted' => 'Terkirim',
                                        'returned' => 'Perbaiki',
                                        'completed' => 'Selesai',
                                        default => 'Unknown',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill small fw-bold">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="text-center text-muted small">
                                {{ $item->submitted_at ? $item->submitted_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="pe-4 text-end">
                                @if($item->isEditable())
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('desa.musdes.edit', $item->id) }}"
                                            class="btn btn-sm btn-warning text-white rounded-pill px-3 shadow-sm" title="Edit Data">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                        <form action="{{ route('desa.musdes.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft laporan ini? Semua data dan file yang sudah diupload akan dihapus permanen.')"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm"
                                                title="Hapus Draft">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('desa.musdes.show', $item->id) }}"
                                        class="btn btn-sm btn-light rounded-pill px-3 border shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye text-primary"></i> Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="mb-3 text-slate-200">
                                    <i class="fas fa-folder-open fa-3x"></i>
                                </div>
                                <h6 class="fw-bold text-slate-600">Belum ada data Musdes.</h6>
                                <p class="text-muted small">Mulai dengan menekan tombol Input Musdes Baru di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($musdesList->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $musdesList->links() }}
            </div>
        @endif
    </div>

    <style>
        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .text-amber-600 {
            color: #d97706;
        }

        .bg-sky-50 {
            background-color: #f0f9ff;
        }

        .text-sky-600 {
            color: #0284c7;
        }

        .bg-rose-50 {
            background-color: #fff1f2;
        }

        .text-rose-600 {
            color: #e11d48;
        }

        .bg-emerald-50 {
            background-color: #ecfdf5;
        }

        .text-emerald-600 {
            color: #059669;
        }
    </style>
@endsection