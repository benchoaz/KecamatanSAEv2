@extends('layouts.desa')

@section('title', 'Data Lembaga Desa')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Lembaga Desa</h4>
                <p class="text-slate-500 mb-0 fs-6">Daftar lembaga kemasyarakatan desa (LPM, PKK, Karang Taruna, dll).</p>
            </div>
            <a href="{{ route('desa.administrasi.lembaga.create') }}"
                class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-plus me-2"></i> Tambah Data
            </a>
        </div>

        <!-- Filter / Stats (Optional, kept simple for now) -->

        <!-- Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold ls-1" style="width: 5%">No
                                </th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold ls-1" style="width: 25%">
                                    Nama Lembaga</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold ls-1" style="width: 20%">
                                    Ketua</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold ls-1" style="width: 20%">
                                    Legalitas</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold ls-1" style="width: 15%">
                                    Status</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold ls-1 text-end"
                                    style="width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lembagas as $index => $lembaga)
                                <tr>
                                    <td class="px-4 py-3 text-center text-slate-500">{{ $lembagas->firstItem() + $index }}</td>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-slate-800">{{ $lembaga->nama_lembaga }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-slate-700">{{ $lembaga->ketua }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small">
                                            <div class="text-slate-700 fw-medium">SK No:
                                                {{ Str::limit($lembaga->sk_pendirian, 15) }}</div>
                                            @if($lembaga->file_sk)
                                                <a href="{{ route('desa.administrasi.file.lembaga', $lembaga->id) }}" target="_blank"
                                                    class="text-primary text-decoration-none small">
                                                    <i class="fas fa-file-pdf me-1"></i> Lihat File
                                                </a>
                                            @else
                                                <span class="text-muted small italic">File belum diupload</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-desa.form.status-badge :status="$lembaga->status" />
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if($lembaga->isEditable())
                                                <a href="{{ route('desa.administrasi.lembaga.edit', $lembaga->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    Detail & Edit
                                                </a>
                                            @else
                                                <a href="{{ route('desa.administrasi.lembaga.edit', $lembaga->id) }}"
                                                    class="btn btn-sm btn-light text-secondary rounded-pill px-3">
                                                    <i class="fas fa-eye me-1"></i> Lihat
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-sitemap fa-3x text-secondary opacity-50"></i>
                                            </div>
                                            <h6 class="fw-bold text-slate-700 mb-1">Belum ada data Lembaga</h6>
                                            <p class="text-slate-500 small mb-3">Mulai dengan menambahkan data lembaga desa.</p>
                                            <a href="{{ route('desa.administrasi.lembaga.create') }}"
                                                class="btn btn-primary btn-sm rounded-pill px-4">
                                                Tambah Data
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-top">
                    {{ $lembagas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection