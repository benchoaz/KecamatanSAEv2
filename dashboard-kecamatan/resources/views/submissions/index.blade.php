@extends('layouts.desa')

@section('title', 'Riwayat & Status Laporan')

@section('content')
    <div class="content-header mb-4">
        <div class="header-title">
            <h1>Riwayat & Status Laporan</h1>
            <p class="text-muted">Pantau status verifikasi dan progres laporan desa Anda.</p>
        </div>
    </div>

    <div class="card bg-white border-gray-200 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ID / Tanggal</th>
                            <th>Bidang / Aspek</th>
                            <th>Periode</th>
                            <th>Status Verifikasi</th>
                            <th>Terakhir Diupdate</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $sub)
                            <tr class="border-gray-100">
                                <td class="ps-4 py-4">
                                    <div class="fw-bold text-gray-800 small">#{{ strtoupper(substr($sub->uuid, 0, 8)) }}</div>
                                    <div class="text-muted extra-small">{{ $sub->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-teal-600">{{ $sub->menu->nama_menu }}</div>
                                    <div class="text-muted small">{{ $sub->aspek->nama_aspek }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-gray-100 text-gray-600 border">{{ strtoupper($sub->periode) }}</span>
                                    <div class="text-muted small mt-1">{{ $sub->tahun }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'draft' => ['class' => 'bg-gray-100 text-gray-600 border', 'icon' => 'fa-file-pen', 'label' => 'Draft'],
                                            'submitted' => ['class' => 'bg-amber-100 text-amber-700 border border-amber-200', 'icon' => 'fa-paper-plane', 'label' => 'Diajukan'],
                                            'returned' => ['class' => 'bg-blue-100 text-blue-700 border border-blue-200', 'icon' => 'fa-rotate-left', 'label' => 'Perbaikan'],
                                            'reviewed' => ['class' => 'bg-teal-100 text-teal-700 border border-teal-200', 'icon' => 'fa-magnifying-glass', 'label' => 'Diverifikasi'],
                                            'approved' => ['class' => 'bg-emerald-100 text-emerald-700 border border-emerald-200', 'icon' => 'fa-check-double', 'label' => 'Disetujui'],
                                            'rejected' => ['class' => 'bg-rose-100 text-rose-700 border border-rose-200', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                                        ][$sub->status] ?? ['class' => 'bg-gray-100 text-gray-600', 'icon' => 'fa-question', 'label' => $sub->status];
                                    @endphp
                                    <span class="badge {{ $statusConfig['class'] }} d-inline-flex align-items-center px-3 py-2">
                                        <i class="fas {{ $statusConfig['icon'] }} me-2"></i>
                                        {{ $statusConfig['label'] }}
                                    </span>

                                    @if($sub->status === 'returned')
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-outline-warning btn-extra-sm"
                                                data-bs-toggle="popover" title="Catatan Perbaikan"
                                                data-bs-content="{{ $sub->catatan_review ?? 'Tidak ada catatan spesifik.' }}">
                                                <i class="fas fa-comment-dots me-1"></i> Lihat Catatan
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-gray-600 small">{{ $sub->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('desa.submissions.edit', $sub->id) }}"
                                            class="btn btn-sm {{ $sub->isEditable() ? 'btn-teal' : 'btn-outline-secondary' }}"
                                            title="{{ $sub->isEditable() ? 'Edit Laporan' : 'Lihat Detail' }}">
                                            <i class="fas {{ $sub->isEditable() ? 'fa-edit' : 'fa-eye' }}"></i>
                                            <span class="ms-1">{{ $sub->isEditable() ? 'Edit' : 'Detail' }}</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3 opacity-25"></i>
                                        <p>Belum ada riwayat laporan yang dikirimkan.</p>
                                        <a href="{{ route('desa.submissions.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-plus me-1"></i> Buat Laporan Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
            <div class="card-footer border-secondary bg-transparent py-3">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .extra-small {
            font-size: 0.7rem;
        }

        .btn-extra-sm {
            padding: 0.15rem 0.5rem;
            font-size: 0.75rem;
        }

        .btn-teal {
            background-color: #14b8a6;
            color: white;
        }

        .btn-teal:hover {
            background-color: #0d9488;
            color: white;
        }

        .bg-gray-100 {
            background-color: #f1f5f9;
        }

        .text-gray-600 {
            color: #475569;
        }

        .bg-amber-100 {
            background-color: #fef3c7;
        }

        .text-amber-700 {
            color: #b45309;
        }

        .bg-blue-100 {
            background-color: #dbeafe;
        }

        .text-blue-700 {
            color: #1d4ed8;
        }

        .bg-teal-100 {
            background-color: #ccfbf1;
        }

        .text-teal-700 {
            color: #0f766e;
        }

        .bg-emerald-100 {
            background-color: #d1fae5;
        }

        .text-emerald-700 {
            color: #047857;
        }

        .bg-rose-100 {
            background-color: #ffe4e6;
        }

        .text-rose-700 {
            color: #be123c;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl)
            })
        });
    </script>
@endpush