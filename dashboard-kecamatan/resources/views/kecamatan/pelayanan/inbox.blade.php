@extends('layouts.kecamatan')

@php
    $pageTitle = 'Inbox ';
    $pageDesc = '';
    $accentColor = 'slate';
    
    if ($category === 'ekonomi') {
        $pageTitle .= 'Usaha & Jasa';
        $pageDesc = 'Verifikasi pendaftaran UMKM dan Jasa baru dari warga.';
        $accentColor = 'teal';
    } elseif ($category === 'umkm') {
        $pageTitle .= 'UMKM';
        $pageDesc = 'Verifikasi pendaftaran UMKM baru dari warga.';
        $accentColor = 'blue';
    } elseif ($category === 'loker') {
        $pageTitle .= 'Loker';
        $pageDesc = 'Verifikasi informasi lowongan kerja baru dari warga.';
        $accentColor = 'emerald';
    } else {
        $pageTitle .= 'Pelayanan Berkas';
        $pageDesc = 'Daftar permohonan layanan administrasi dan berkas dari masyarakat.';
        $accentColor = 'rose';
    }
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
            <div>
                <h1 class="text-slate-900 fw-bold fs-3 mb-1">{{ $pageTitle }}</h1>
                <p class="text-slate-400 small mb-0">{{ $pageDesc }}</p>
            </div>
            <div class="d-flex gap-2">
                @if($category == 'umkm' || $category == 'ekonomi')
                    <a href="{{ route('economy.create') }}" class="btn btn-brand-600 rounded-3 px-3 small fw-bold text-white shadow-sm hover-up">
                        <i class="fas fa-plus me-2"></i> Bantu Daftar Ekonomi
                    </a>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-slate-100 mb-4 bg-white p-3">
            <form action="{{ route('kecamatan.pelayanan.inbox') }}" method="GET" class="row g-2 align-items-center m-0">
                <input type="hidden" name="category" value="{{ $category }}">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-slate-200 bg-slate-50 text-sm" placeholder="Cari Nama, PIN, WA, NIK..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select border-slate-200 bg-slate-50 text-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                        <option value="Sedang Diproses" {{ request('status') == 'Sedang Diproses' ? 'selected' : '' }}>🔄 Sedang Diproses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                        <option value="Ditolak / Tidak Valid" {{ request('status') == 'Ditolak / Tidak Valid' ? 'selected' : '' }}>❌ Ditolak / Tidak Valid</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 text-sm fw-bold">Terapkan Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('kecamatan.pelayanan.inbox', ['category' => $category]) }}" class="btn btn-light rounded-3 text-sm px-3 border border-slate-200" title="Hapus Filter"><i class="fas fa-times text-slate-400"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-slate-100">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50/50 border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Informasi / Judul</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Sumber</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Wilayah</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Kontak (WA)</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Status</th>
                                <th class="pe-4 py-3 text-end text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($complaints as $item)
                                <tr class="transition-all hover:bg-slate-50/50">
                                    <td class="ps-4 py-3">
                                        <div class="fw-semibold text-slate-700 small">{{ $item->created_at->format('d/m/y') }}</div>
                                        <div class="text-[10px] text-slate-400 text-nowrap">{{ $item->created_at->format('H:i') }} WIB</div>
                                    </td>
                                     <td class="py-3" style="max-width: 300px;">
                                        <div class="fw-bold text-slate-800 small truncate">
                                            @if($category == 'umkm' || $category == 'loker')
                                                {{ Str::limit($item->uraian, 60) }}
                                            @else
                                                {{ $item->nama_pemohon ?? 'Pemohon / Warga' }}
                                                @if(str_contains($item->uraian, '[ANONIM]'))
                                                    <i class="fas fa-user-secret text-slate-400 ms-1" title="Pelapor meminta anonimitas"></i>
                                                @endif
                                                @if(str_contains($item->uraian, '[RAHASIA]'))
                                                    <span class="badge bg-rose-100 text-rose-600 border border-rose-200 text-[8px] ms-1 px-1 py-0"><i class="fas fa-lock me-1"></i>RAHASIA</span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 d-flex align-items-center gap-1 flex-wrap">
                                            @if($category == 'pelayanan')
                                                {{ Str::limit($item->uraian, 40) }}
                                            @elseif($category == 'ekonomi' || $category == 'umkm')
                                                @if($item->category == 'umkm')
                                                    <span class="badge bg-blue-50 text-blue-600 border border-blue-100 text-[8px] px-1 py-0">UMKM</span>
                                                @else
                                                    <span class="badge bg-teal-50 text-teal-600 border border-teal-100 text-[8px] px-1 py-0">JASA</span>
                                                @endif
                                                Pendaftaran Terpadu
                                            @else
                                                <span class="badge bg-emerald-50 text-emerald-600 border border-emerald-100 text-[8px] px-1 py-0">LOKER</span>
                                                Info Kerja Warga
                                            @endif
                                            @if($item->attachments_count > 0)
                                                <span class="badge bg-slate-100 text-slate-500 border border-slate-200 text-[8px] px-1 py-0"><i class="fas fa-paperclip me-1"></i>{{ $item->attachments_count }}</span>
                                            @endif
                                        </div>
                                     </td>
                                    <td class="py-3">
                                        @php 
                                            $sourceIcon = 'fa-globe';
                                            $sourceColor = 'slate';
                                            if($item->source == 'whatsapp') { $sourceIcon = 'fa-whatsapp'; $sourceColor = 'emerald'; }
                                            elseif($item->source == 'chatbox') { $sourceIcon = 'fa-comment-dots'; $sourceColor = 'blue'; }
                                        @endphp
                                        <span class="text-[10px] fw-bold px-2 py-1 rounded-pill bg-{{ $sourceColor }}-50 text-{{ $sourceColor }}-500 border border-{{ $sourceColor }}-100 uppercase">
                                            <i class="fab {{ $sourceIcon }} me-1"></i> {{ $item->source ?? 'Web Form' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-slate-600 small">{{ $item->desa ? $item->desa->nama_desa : ($item->nama_desa_manual ?? 'Umum') }}</span>
                                    </td>
                                    <td class="py-3">
                                        @php $waNum = preg_replace('/[^0-9]/', '', $item->whatsapp); @endphp
                                        <a href="https://wa.me/{{ str_starts_with($waNum, '0') ? '62'.substr($waNum, 1) : $waNum }}" target="_blank"
                                            class="text-emerald-600 small fw-medium text-decoration-none hover:underline">
                                            <i class="fab fa-whatsapp me-1"></i> {{ $item->whatsapp }}
                                        </a>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-{{ $item->status_color }}-500 text-[10px] fw-bold border border-{{ $item->status_color }}-100 px-2 py-1 rounded uppercase">
                                            @if($item->status == \App\Models\PublicService::STATUS_MENUNGGU)
                                                <i class="fas fa-clock me-1"></i> ANTRIAN
                                            @elseif($item->status == \App\Models\PublicService::STATUS_SELESAI)
                                                <i class="fas fa-check-double me-1"></i> {{ $item->status_label }}
                                            @else
                                                {{ $item->status_label }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <a href="{{ route('kecamatan.pelayanan.show', $item->id) }}"
                                            class="btn btn-sm btn-white border border-slate-200 rounded-3 px-3 fw-bold text-slate-600">
                                            @if($item->status == 'Menunggu Verifikasi') Tanggapi @else Detail @endif
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-4 text-slate-300">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="small mb-0">Belum ada item masuk di kategori ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($complaints->hasPages())
                <div class="card-footer bg-white border-top border-slate-100 py-3 px-4">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection