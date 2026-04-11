@extends('layouts.kecamatan')

@section('title', 'Manajemen Berita & Informasi')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Berita & Informasi</h4>
                <p class="text-slate-500 small mb-0">Kelola publikasi berita untuk landing page publik.</p>
            </div>
            <div class="d-flex gap-2">
                @if($activeTab === 'news')
                    <a href="{{ route('kecamatan.berita.create') }}" class="btn btn-primary px-4 rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-plus me-2"></i> Buat Berita Baru
                    </a>
                @else
                    <a href="{{ route('kecamatan.berita.banners.create') }}" class="btn btn-teal px-4 text-white rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-image me-2"></i> Tambah Banner Iklan
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Tab Navigation -->
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'news' ? 'active shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}" 
                   href="{{ route('kecamatan.berita.index', ['tab' => 'news']) }}">
                   <i class="fas fa-newspaper me-2"></i> Daftar Berita
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 fw-bold {{ $activeTab === 'banners' ? 'active bg-teal shadow-sm text-white' : 'text-slate-500 hover:bg-slate-100' }}" 
                   href="{{ route('kecamatan.berita.index', ['tab' => 'banners']) }}">
                   <i class="fas fa-ad me-2"></i> Banner Iklan
                </a>
            </li>
        </ul>

        @if($activeTab === 'news')
            <!-- Source Filter Navigation -->
            <div class="mb-4 bg-white p-2 rounded-4 shadow-sm border border-slate-50 d-inline-flex gap-1">
                <a href="{{ route('kecamatan.berita.index', ['tab' => 'news', 'source' => 'all']) }}" 
                   class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ $sourceFilter === 'all' ? 'btn-primary shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
                   <i class="fas fa-list-ul me-2"></i> Semua Berita
                </a>
                <a href="{{ route('kecamatan.berita.index', ['tab' => 'news', 'source' => 'kecamatan']) }}" 
                   class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ $sourceFilter === 'kecamatan' ? 'btn-blue shadow-sm text-white' : 'text-slate-500 hover:bg-slate-50' }}"
                   style="{{ $sourceFilter === 'kecamatan' ? 'background: #2563eb;' : '' }}">
                   <i class="fas fa-landmark me-2"></i> Internal Kecamatan
                </a>
                <a href="{{ route('kecamatan.berita.index', ['tab' => 'news', 'source' => 'desa']) }}" 
                   class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ $sourceFilter === 'desa' ? 'btn-teal shadow-sm text-white' : 'text-slate-500 hover:bg-slate-50' }}"
                   style="{{ $sourceFilter === 'desa' ? 'background: #0d9488;' : '' }}">
                   <i class="fas fa-home-alt me-2"></i> Warta Desa Binaan
                </a>
            </div>
        @endif

        <div class="card border-0 shadow-premium rounded-4">
            <div class="card-body p-0">
                @if($activeTab === 'news')
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom border-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold" style="width: 50px;">No</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Berita</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Sumber</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Kategori</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold small">Status</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Tanggal</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            @forelse($berita as $item)
                                <tr class="border-bottom border-slate-50 {{ $item->desa_id ? 'bg-teal-50/10' : '' }}" 
                                    style="{{ $item->desa_id ? 'border-left: 4px solid #0d9488 !important;' : 'border-left: 4px solid #2563eb !important;' }}">
                                    <td class="px-4 py-3 text-slate-600 small">
                                        {{ ($berita->currentPage() - 1) * $berita->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @if($item->thumbnail)
                                                    <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                                        class="rounded-3 object-cover" width="60" height="40" alt="">
                                                @else
                                                    <div class="bg-slate-100 rounded-3 d-flex align-items-center justify-content-center"
                                                        width="60" height="40" style="width: 60px; height: 40px;">
                                                        <i class="fas fa-image text-slate-300"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-slate-800 line-clamp-1 truncate"
                                                    style="max-width: 250px;">{{ $item->judul }}</div>
                                                <div class="text-[10px] text-slate-400">View:
                                                    {{ number_format($item->view_count) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($item->desa_id)
                                            <div class="badge bg-teal-subtle text-teal-700 border border-teal-100 rounded-pill px-3 py-1.5 fw-bold" style="font-size: 10px;">
                                                <i class="fas fa-home-alt me-1"></i> DESA {{ strtoupper($item->desa->nama_desa) }}
                                            </div>
                                        @else
                                            <div class="badge bg-blue-subtle text-blue-700 border border-blue-100 rounded-pill px-3 py-1.5 fw-bold" style="font-size: 10px;">
                                                <i class="fas fa-landmark me-1"></i> KECAMATAN
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="badge bg-slate-100 text-slate-600 border border-slate-200 rounded-pill px-3 fw-medium">
                                            {{ $item->kategori }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form action="{{ route('kecamatan.berita.toggle-status', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="border-0 bg-transparent p-0">
                                                @if($item->status === 'published')
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 fw-medium">
                                                        <i class="fas fa-check-circle me-1"></i> Published
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 fw-medium">
                                                        <i class="fas fa-clock me-1"></i> Draft
                                                    </span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 small">
                                        <div class="fw-bold text-slate-700">{{ $item->published_at ? $item->published_at->isoFormat('D MMM YYYY') : '-' }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">{{ $item->author->nama_lengkap ?? 'System' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('public.berita.show', $item->slug) }}" target="_blank"
                                                class="btn btn-sm btn-light text-blue-500 shadow-sm border border-slate-200"
                                                data-bs-toggle="tooltip" title="Preview Publik">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="{{ route('kecamatan.berita.edit', $item->id) }}"
                                                class="btn btn-sm btn-light text-amber-500 shadow-sm border border-slate-200"
                                                data-bs-toggle="tooltip" title="Edit Berita">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('kecamatan.berita.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Arsipkan berita ini? Berita tidak akan tampil di publik namun masih tersimpan di database.')"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-light text-rose-500 shadow-sm border border-slate-200"
                                                    data-bs-toggle="tooltip" title="Arsipkan">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-center">
                                        <div class="opacity-20 mb-3">
                                            <i class="fas fa-newspaper fa-3x text-slate-300"></i>
                                        </div>
                                        <h6 class="fw-bold text-slate-400">Belum ada berita</h6>
                                        <p class="text-slate-400 small">Klik tombol 'Buat Berita Baru' untuk mulai menulis.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                @else
                    <!-- Banners Listing -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-slate-50 border-bottom border-slate-100">
                                <tr>
                                    <th class="px-4 py-3 text-slate-500 uppercase small fw-bold" style="width: 50px;">No</th>
                                    <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Banner</th>
                                    <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Tautan</th>
                                    <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Status</th>
                                    <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Prioritas</th>
                                    <th class="px-4 py-3 text-slate-500 uppercase small fw-bold text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-0">
                                @forelse($banners as $banner)
                                    <tr class="border-bottom border-slate-50">
                                        <td class="px-4 py-3 text-slate-600 small">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="{{ asset('storage/' . $banner->image_path) }}"
                                                        class="rounded-3 shadow-sm object-cover" width="120" height="40" style="object-fit: cover; width: 120px; height: 40px;" alt="">
                                                </div>
                                                <div class="fw-bold text-slate-800">{{ $banner->title }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-500 small">
                                            @if($banner->link_url)
                                                <a href="{{ $banner->link_url }}" target="_blank" class="text-blue-500 hover:underline">
                                                    {{ Str::limit($banner->link_url, 40) }}
                                                </a>
                                            @else
                                                <span class="text-slate-300 italic">Tidak ada tautan</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <form action="{{ route('kecamatan.berita.banners.toggle-status', $banner->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="border-0 bg-transparent p-0">
                                                    @if($banner->is_active)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 fw-medium">Aktif</span>
                                                    @else
                                                        <span class="badge bg-slate-100 text-slate-400 border border-slate-200 rounded-pill px-3 fw-medium">Non-Aktif</span>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-blue-subtle text-blue-600 rounded-pill px-3 fw-bold">{{ $banner->priority }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <a href="{{ route('kecamatan.berita.banners.edit', $banner->id) }}"
                                                    class="btn btn-sm btn-light text-amber-500 shadow-sm border border-slate-200">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('kecamatan.berita.banners.destroy', $banner->id) }}" method="POST"
                                                    onsubmit="return confirm('Hapus banner ini selamanya?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-rose-500 shadow-sm border border-slate-200">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-5 text-center">
                                            <div class="opacity-20 mb-3">
                                                <i class="fas fa-ad fa-3x text-slate-300"></i>
                                            </div>
                                            <h6 class="fw-bold text-slate-400">Belum ada banner iklan</h6>
                                            <p class="text-slate-400 small">Klik tombol 'Tambah Banner Iklan' untuk mulai unggah.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if($activeTab === 'news' && $berita->hasPages())
                <div class="card-footer bg-white border-top border-slate-50 px-4 py-3">
                    {{ $berita->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection