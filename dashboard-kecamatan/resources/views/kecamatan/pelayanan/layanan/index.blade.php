@extends('layouts.kecamatan')

@section('title', 'Manajemen Daftar Layanan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="fw-bold text-slate-800 mb-1">Daftar Layanan (Self Service)</h4>
                <p class="text-slate-500 small mb-0">Kelola daftar layanan yang tampil di halaman depan aplikasi.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('kecamatan.pelayanan.layanan.create') }}"
                    class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-2"></i> Tambah Layanan Baru
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-slate-400 small fw-bold text-uppercase tracking-wider"
                                    style="width: 80px">Urutan</th>
                                <th class="py-3 text-slate-400 small fw-bold text-uppercase tracking-wider">Ikon & Nama
                                    Layanan</th>
                                <th class="py-3 text-slate-400 small fw-bold text-uppercase tracking-wider">Persyaratan</th>
                                <th class="py-3 text-slate-400 small fw-bold text-uppercase tracking-wider">Estimasi</th>
                                <th class="py-3 text-slate-400 small fw-bold text-uppercase tracking-wider">Status</th>
                                <th class="pe-4 py-3 text-end text-slate-400 small fw-bold text-uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($layanan as $item)
                                <tr class="border-bottom border-slate-50">
                                    <td class="ps-4">
                                        <span class="badge bg-slate-100 text-slate-600 rounded-pill">{{ $item->urutan }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div
                                                class="w-10 h-10 {{ $item->warna_bg }} {{ $item->warna_text }} rounded-3 d-flex align-items-center justify-content-center shadow-sm">
                                                <i class="fas {{ $item->ikon }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-slate-800">{{ $item->nama_layanan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-slate-600 small text-wrap" style="max-width: 300px">
                                            {{ Str::limit($item->deskripsi_syarat, 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-blue-50 text-blue-600 border border-blue-100 px-2 py-1.5 rounded-pill small fw-bold">
                                            <i class="far fa-clock me-1"></i> {{ $item->estimasi_waktu }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->is_active)
                                            <span
                                                class="badge bg-emerald-50 text-emerald-600 px-2 py-1.5 rounded-pill small fw-bold border border-emerald-100">Aktif</span>
                                        @else
                                            <span
                                                class="badge bg-slate-50 text-slate-400 px-2 py-1.5 rounded-pill small fw-bold border border-slate-200">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('kecamatan.pelayanan.layanan.edit', $item->id) }}"
                                                class="btn btn-light btn-sm rounded-pill text-teal-600 me-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('kecamatan.pelayanan.layanan.destroy', $item->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm rounded-pill text-rose-500"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center">
                                        <div class="text-slate-400">
                                            <i class="fas fa-layer-group fa-3x mb-3 opacity-20"></i>
                                            <p class="mb-0 small">Belum ada daftar layanan yang dibuat.</p>
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
@endsection