@extends('layouts.kecamatan')

@section('title', 'Manajemen Fitur')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('kecamatan.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manajemen Fitur</li>
                    </ol>
                </nav>
                <h2 class="fw-bold"><i class="fas fa-toggle-on text-primary me-2"></i> Manajemen Fitur Aplikasi</h2>
                <p class="text-muted">Aktifkan atau nonaktifkan modul aplikasi secara global untuk seluruh desa.</p>
            </div>
        </div>

        <div class="row">
            @foreach($menus as $menu)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @php
                                    $iconColor = match ($menu->kode_menu) {
                                        'pemerintahan' => 'text-primary bg-primary-subtle',
                                        'ekbang' => 'text-brand-600 bg-brand-50',
                                        'kesra' => 'text-danger bg-danger-subtle',
                                        'trantibum' => 'text-warning-900 bg-warning-subtle',
                                        'analisa' => 'text-info bg-info-subtle',
                                        default => 'text-primary bg-light'
                                    };
                                @endphp
                                <div class="avatar-sm {{ $iconColor }} rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 48px; height: 48px; font-size: 24px;">
                                    <i class="{{ $menu->icon }}"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-bold">{{ $menu->nama_menu }}</h5>
                                    <small class="text-uppercase tracking-wider text-muted" style="font-size: 10px;">ID:
                                        {{ $menu->kode_menu }}</small>
                                </div>
                            </div>
                            <p class="card-text text-muted small mb-4">{{ $menu->deskripsi }}</p>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <span
                                    class="badge {{ $menu->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3">
                                    {{ $menu->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>

                                <div class="form-check form-switch">
                                    <input class="form-check-input feature-toggle" type="checkbox" role="switch"
                                        data-kode="{{ $menu->kode_menu }}" {{ $menu->is_active ? 'checked' : '' }}
                                        style="width: 3em; height: 1.5em; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle fa-2x me-3 opacity-50"></i>
            <div>
                <strong>Catatan Penting:</strong>
                Menonaktifkan fitur akan menyembunyikan menu dari operator desa dan memblokir akses ke halaman terkait.
                Data yang sudah ada tetap aman dan tidak akan dihapus.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.feature-toggle').forEach(toggle => {
            toggle.addEventListener('change', function () {
                const kodeMenu = this.dataset.kode;
                const isActive = this.checked ? 1 : 0;

                // Show loading state if needed
                this.disabled = true;

                fetch('{{ route('kecamatan.settings.profile.toggle-feature') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        kode_menu: kodeMenu,
                        is_active: isActive
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Refresh status badge visually
                            const badge = this.closest('.card-body').querySelector('.badge');
                            if (isActive) {
                                badge.classList.remove('bg-danger-subtle', 'text-danger');
                                badge.classList.add('bg-success-subtle', 'text-success');
                                badge.textContent = 'Aktif';
                            } else {
                                badge.classList.remove('bg-success-subtle', 'text-success');
                                badge.classList.add('bg-danger-subtle', 'text-danger');
                                badge.textContent = 'Non-Aktif';
                            }
                        } else {
                            alert('Gagal mengupdate status fitur.');
                            this.checked = !this.checked; // Revert
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem.');
                        this.checked = !this.checked; // Revert
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
            });
        });
    </script>
@endpush