@extends('layouts.kecamatan')

@section('title', 'Kelola Pengumuman')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
            <div>
                <h1 class="text-slate-900 fw-bold fs-3 mb-1">Pengumuman Resmi</h1>
                <p class="text-slate-400 small mb-0">Kelola informasi publik dan instruksi internal kecamatan.</p>
            </div>
            <a href="{{ route('kecamatan.announcements.create') }}"
                class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
                <i class="fas fa-plus me-2"></i> Buat Pengumuman
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-emerald border-0 shadow-sm rounded-4 p-3 mb-4 animate__animated animate__fadeIn">
                <p class="mb-0 text-emerald-700 small fw-medium"><i class="fas fa-check-circle me-1"></i>
                    {{ session('success') }}</p>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-slate-100">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50/50 border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Judul &
                                    Konten</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Target</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Periode</th>
                                <th class="py-3 text-center text-slate-400 text-[11px] fw-bold uppercase tracking-wider">
                                    Mode</th>
                                <th class="py-3 text-center text-slate-400 text-[11px] fw-bold uppercase tracking-wider">
                                    Status</th>
                                <th class="pe-4 py-3 text-end text-slate-400 text-[11px] fw-bold uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $item)
                                <tr class="transition-all hover:bg-slate-50/50">
                                    <td class="ps-4 py-3" style="max-width: 300px;">
                                        <div class="fw-bold text-slate-800 mb-0 small">{{ $item->title }}</div>
                                        <p class="text-slate-400 text-[11px] mb-0 text-truncate">{{ $item->content }}</p>
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="badge bg-slate-100 text-slate-600 rounded-pill px-2 py-1 text-[10px] fw-bold">
                                            {{ strtoupper($item->target_type) }}
                                        </span>
                                        @if($item->target_type == 'specific_desa' && $item->target_desa_ids)
                                            <div class="text-[9px] text-slate-400 mt-1 italic">
                                                {{ count($item->target_desa_ids) }} Desa Terpilih
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="text-slate-600 text-[10px]">
                                            <i class="far fa-calendar-alt me-1"></i> {{ $item->start_date->format('d/m/y') }} -
                                            {{ $item->end_date->format('d/m/y') }}
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="text-slate-500 text-[10px] fw-medium">
                                            @if($item->display_mode == 'ticker') <i class="fas fa-stream me-1"></i> Ticker
                                            @elseif($item->display_mode == 'banner') <i class="fas fa-image me-1"></i> Banner
                                            @else <i class="fas fa-bell me-1"></i> Notif @endif
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($item->is_active && $item->end_date >= now()->toDateString())
                                            <span class="text-emerald-500 text-[10px] fw-bold"><i
                                                    class="fas fa-circle me-1 fs-[6px]"></i> AKTIF</span>
                                        @else
                                            <span class="text-slate-300 text-[10px] fw-bold"><i
                                                    class="fas fa-circle me-1 fs-[6px]"></i> ARSIP</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('kecamatan.announcements.edit', $item->id) }}"
                                                class="btn btn-sm btn-light border p-1 px-2 text-[11px] fw-bold">Edit</a>
                                            <form action="{{ route('kecamatan.announcements.destroy', $item->id) }}"
                                                method="POST" onsubmit="return confirm('Arsipkan pengumuman ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-white border border-rose-100 text-rose-400 p-1 px-2 text-[11px] fw-bold">Arsip</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4 text-slate-300">
                                            <i class="fas fa-bullhorn fa-2x mb-2"></i>
                                            <p class="small mb-0">Belum ada pengumuman.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($announcements->hasPages())
                <div
                    class="card-footer bg-white border-top border-slate-100 py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection