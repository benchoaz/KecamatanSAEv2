@extends('layouts.kecamatan')

@section('title', 'Manajemen Layanan Publik (Fasilitator)')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Manajemen Layanan Publik</h4>
                <p class="text-slate-500 small mb-0">Fasilitasi pendaftaran UMKM & Jasa warga tanpa mengelola produk.</p>
            </div>
            <div class="d-flex gap-2">
                @if($activeTab == 'umkm')
                    <a href="{{ route('kecamatan.umkm.create') }}" class="btn btn-primary px-4 rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-hand-holding-heart me-2"></i> Bantu Daftar UMKM
                    </a>
                @else
                    <a href="{{ route('kecamatan.jasa.create') }}" class="btn btn-indigo text-white px-4 rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-tools me-2"></i> Bantu Daftar Jasa
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Tabs --}}
        <div class="card border-0 shadow-premium rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 p-0">
                <div class="nav nav-tabs nav-fill border-0">
                    <a href="{{ route('kecamatan.umkm.index', ['tab' => 'umkm']) }}" 
                       class="nav-link border-0 py-3 fw-bold text-sm {{ $activeTab == 'umkm' ? 'active bg-white text-primary border-bottom border-primary border-3' : 'bg-slate-50 text-slate-400' }}">
                        <i class="fas fa-store me-2"></i> UMKM Rakyat
                    </a>
                    <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" 
                       class="nav-link border-0 py-3 fw-bold text-sm {{ $activeTab == 'jasa' ? 'active bg-white text-primary border-bottom border-primary border-3' : 'bg-slate-50 text-slate-400' }}">
                        <i class="fas fa-tools me-2"></i> Jasa & Tenaga Kerja
                    </a>
                </div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="card border-0 shadow-premium rounded-4 mb-4">
            <div class="card-body p-3">
                <form action="{{ route('kecamatan.umkm.index') }}" method="GET" class="row g-2">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-slate-200 pr-0">
                                <i class="fas fa-search text-slate-400"></i>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-slate-200 border-start-0 ps-1" 
                                   placeholder="Cari berdasarkan nama {{ $activeTab == 'umkm' ? 'usaha atau pemilik' : 'jasa atau penyedia' }}...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-slate-800 text-white w-100 fw-bold">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-premium rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom border-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">{{ $activeTab == 'umkm' ? 'UMKM / Pemilik' : 'Jasa / Penyedia' }}</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Fokus & Wilayah</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Kontak</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Status</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold text-end">Aksi Fasilitator</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            @if($activeTab == 'umkm')
                                @forelse($umkm as $item)
                                    <tr class="border-bottom border-slate-50">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($item->foto_usaha)
                                                        <img src="{{ asset('storage/' . $item->foto_usaha) }}" class="rounded-3 object-cover shadow-sm" width="40" height="40">
                                                    @else
                                                        <div class="bg-indigo-50 text-indigo-500 rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                            {{ substr($item->nama_usaha, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-slate-800 text-sm">
                                                        {{ $item->nama_usaha }}
                                                        @if($item->is_verified)
                                                            <i class="fas fa-check-circle text-primary text-[10px]" title="Terverifikasi"></i>
                                                        @endif
                                                    </div>
                                                    <div class="text-slate-500 text-xs">{{ $item->nama_pemilik }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="badge bg-slate-100 text-slate-600 border border-slate-200 rounded-pill px-2 py-1 small fw-bold mb-1">
                                                {{ $item->desa }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $item->jenis_usaha }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="https://wa.me/{{ $item->no_wa }}" target="_blank" class="text-success text-decoration-none text-xs fw-bold">
                                                <i class="fab fa-whatsapp me-1"></i> {{ $item->no_wa }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($item->ownership_status == 'pending_transfer')
                                                <span class="badge bg-amber-50 text-amber-600 border border-amber-100 rounded-pill px-2 py-1 fw-bold text-[10px] uppercase">
                                                    <i class="fas fa-clock me-1"></i> Butuh Serah Terima
                                                </span>
                                            @else
                                                <span class="badge bg-{{ $item->status_badge }}-50 text-{{ $item->status_badge == 'success' ? 'emerald' : ($item->status_badge == 'warning' ? 'amber' : 'slate') }}-600 border border-{{ $item->status_badge == 'success' ? 'emerald' : ($item->status_badge == 'warning' ? 'amber' : 'slate') }}-100 rounded-pill px-2 py-1 fw-bold text-[10px] uppercase">
                                                    {{ $item->status_label }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border-0 shadow-sm rounded-circle dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v text-slate-400"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                                    <li>
                                                        <form action="{{ route('kecamatan.umkm.toggle-verify', $item->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item rounded-3 mb-1 text-sm font-medium {{ $item->is_verified ? 'text-slate-600' : 'text-primary' }}">
                                                                <i class="fas {{ $item->is_verified ? 'fa-certificate' : 'fa-check-circle' }} me-2"></i> 
                                                                {{ $item->is_verified ? 'Batal Verifikasi' : 'Verifikasi (Centang Biru)' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 text-sm font-medium" href="{{ route('kecamatan.umkm.handover', $item->id) }}"><i class="fas fa-key text-amber-500 me-2"></i> Reset Akses / Link</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 text-sm font-medium" href="{{ route('kecamatan.umkm.edit', $item->id) }}"><i class="fas fa-edit text-slate-400 me-2"></i> Koreksi Admin</a></li>
                                                    <li>
                                                        <form action="{{ route('kecamatan.umkm.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan UMKM ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item rounded-3 text-sm font-medium text-rose-600"><i class="fas fa-ban text-rose-400 me-2"></i> Nonaktifkan</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-5 text-center text-slate-400">Belum ada UMKM terdaftar.</td></tr>
                                @endforelse
                            @else
                                @forelse($jasa as $item)
                                    <tr class="border-bottom border-slate-50">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="bg-indigo-50 text-indigo-500 rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user-cog"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-slate-800 text-sm">
                                                        {{ $item->job_title }}
                                                        @if($item->is_verified)
                                                            <i class="fas fa-check-circle text-primary text-[10px]" title="Terverifikasi"></i>
                                                        @endif
                                                    </div>
                                                    <div class="text-slate-500 text-xs">{{ $item->display_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="badge bg-slate-100 text-slate-600 border border-slate-200 rounded-pill px-2 py-1 small fw-bold mb-1">
                                                {{ $item->service_area }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $item->job_category }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->contact_phone) }}" target="_blank" class="text-success text-decoration-none text-xs fw-bold">
                                                <i class="fab fa-whatsapp me-1"></i> {{ $item->contact_phone }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColor = $item->status == 'active' ? 'emerald' : 'slate';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 border border-{{ $statusColor }}-100 rounded-pill px-2 py-1 fw-bold text-[10px] uppercase">
                                                {{ $item->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border-0 shadow-sm rounded-circle dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v text-slate-400"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                                    <li>
                                                        <form action="{{ route('kecamatan.jasa.toggle-verify', $item->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item rounded-3 mb-1 text-sm font-medium {{ $item->is_verified ? 'text-slate-600' : 'text-primary' }}">
                                                                <i class="fas {{ $item->is_verified ? 'fa-certificate' : 'fa-check-circle' }} me-2"></i> 
                                                                {{ $item->is_verified ? 'Batal Verifikasi' : 'Verifikasi (Centang Biru)' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 text-sm font-medium" href="{{ route('kecamatan.jasa.handover', $item->id) }}"><i class="fas fa-key text-amber-500 me-2"></i> Reset Akses / Link</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item rounded-3 mb-1 text-sm font-medium" href="{{ route('kecamatan.jasa.edit', $item->id) }}"><i class="fas fa-edit text-slate-400 me-2"></i> Koreksi Admin</a></li>
                                                    <li>
                                                        <form action="{{ route('kecamatan.jasa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus Jasa ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item rounded-3 text-sm font-medium text-rose-600"><i class="fas fa-trash text-rose-400 me-2"></i> Hapus Jasa</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-5 text-center text-slate-400">Belum ada Jasa terdaftar.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @php $currentPaginate = ($activeTab == 'umkm') ? $umkm : $jasa; @endphp
            @if($currentPaginate && $currentPaginate->hasPages())
                <div class="card-footer bg-white border-top border-slate-50 px-4 py-3">
                    {{ $currentPaginate->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection