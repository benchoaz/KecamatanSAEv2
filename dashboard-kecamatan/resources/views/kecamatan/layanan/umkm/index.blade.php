@extends('layouts.kecamatan')

@section('title', 'Manajemen UMKM Rakyat (Fasilitator)')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Manajemen UMKM Rakyat</h4>
                <p class="text-slate-500 small mb-0">Fasilitasi pendaftaran UMKM warga tanpa mengelola produk.</p>
            </div>
            <a href="{{ route('kecamatan.umkm.create') }}" class="btn btn-primary px-4 rounded-3 fw-bold shadow-sm">
                <i class="fas fa-hand-holding-heart me-2"></i> Bantu Daftarkan UMKM
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-premium rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom border-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">UMKM / Pemilik</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Desa & Kategori</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Kontak</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold">Status Akun</th>
                                <th class="px-4 py-3 text-slate-500 uppercase small fw-bold text-end">Aksi Fasilitator</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            @forelse($umkm as $item)
                                <tr class="border-bottom border-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @if($item->foto_usaha)
                                                    <img src="{{ asset('storage/' . $item->foto_usaha) }}"
                                                        class="rounded-3 object-cover shadow-sm" width="40" height="40">
                                                @else
                                                    <div class="bg-indigo-50 text-indigo-500 rounded-3 d-flex align-items-center justify-content-center fw-bold"
                                                        style="width: 40px; height: 40px;">
                                                        {{ substr($item->nama_usaha, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-slate-800 text-sm">{{ $item->nama_usaha }}</div>
                                                <div class="text-slate-500 text-xs">{{ $item->nama_pemilik }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="badge bg-slate-100 text-slate-600 border border-slate-200 rounded-pill px-2 py-1 small fw-bold mb-1">
                                            {{ $item->desa }}
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $item->jenis_usaha }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="https://wa.me/{{ $item->no_wa }}" target="_blank"
                                            class="text-success text-decoration-none text-xs fw-bold">
                                            <i class="fab fa-whatsapp me-1"></i> {{ $item->no_wa }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($item->ownership_status == 'pending_transfer')
                                            <span
                                                class="badge bg-amber-50 text-amber-600 border border-amber-100 rounded-pill px-2 py-1 fw-bold text-[10px] uppercase">
                                                <i class="fas fa-clock me-1"></i> Butuh Serah Terima
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-{{ $item->status_badge }}-50 text-{{ $item->status_badge == 'success' ? 'emerald' : ($item->status_badge == 'warning' ? 'amber' : 'slate') }}-600 border border-{{ $item->status_badge == 'success' ? 'emerald' : ($item->status_badge == 'warning' ? 'amber' : 'slate') }}-100 rounded-pill px-2 py-1 fw-bold text-[10px] uppercase">
                                                @if($item->status == \App\Models\Umkm::STATUS_AKTIF)
                                                    <i class="fas fa-check-circle me-1"></i>
                                                @endif
                                                {{ $item->status_label }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end" style="position: relative; z-index: 1;">
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-light border-0 shadow-sm rounded-circle dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v text-slate-400"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                                <li>
                                                    <h6 class="dropdown-header text-xs text-uppercase fw-bold text-slate-400">
                                                        Menu Fasilitator</h6>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item rounded-3 mb-1 text-sm font-medium d-flex align-items-center"
                                                        href="{{ route('kecamatan.umkm.handover', $item->id) }}">
                                                        <i class="fas fa-key text-amber-500 me-2" style="width: 20px"></i> Reset
                                                        Akses / Link
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item rounded-3 mb-1 text-sm font-medium d-flex align-items-center text-slate-600"
                                                        href="{{ route('kecamatan.umkm.edit', $item->id) }}">
                                                        <i class="fas fa-edit text-slate-400 me-2" style="width: 20px"></i>
                                                        Koreksi Admin
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('kecamatan.umkm.destroy', $item->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Nonaktifkan UMKM ini? Data tidak akan hilang, hanya disembunyikan.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item rounded-3 text-sm font-medium d-flex align-items-center text-rose-600">
                                                            <i class="fas fa-ban text-rose-400 me-2" style="width: 20px"></i>
                                                            Nonaktifkan
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center">
                                        <div class="opacity-20 mb-3">
                                            <i class="fas fa-store-slash fa-3x text-slate-300"></i>
                                        </div>
                                        <h6 class="fw-bold text-slate-400">Belum ada UMKM Terdaftar</h6>
                                        <p class="text-slate-400 small">Gunakan tombol "Bantu Daftarkan" untuk membantu warga.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($umkm->hasPages())
                <div class="card-footer bg-white border-top border-slate-50 px-4 py-3">
                    {{ $umkm->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize all dropdowns with proper event handling
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl, {
                        popperConfig: {
                            strategy: 'fixed'
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection