@extends('layouts.kecamatan')

@section('title', 'Pengaturan WhatsApp Bot')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                <i class="fab fa-whatsapp text-success fs-3"></i>
            </div>
            <div>
                <h1 class="fw-bold fs-4 mb-0">Pengaturan WhatsApp Bot</h1>
                <p class="text-muted small mb-0">Update nomor WhatsApp bot yang ditampilkan di halaman depan.</p>
            </div>
            <div class="ms-auto">
                <a href="{{ route('kecamatan.settings.waha-n8n.provider') }}"
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plug me-1"></i> Konfigurasi Provider WA
                </a>
            </div>
        </div>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                });
            </script>
        @endif

        <!-- Bot Settings Form -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-4 px-4 border-bottom border-light">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-mobile-alt text-success"></i>
                    <h5 class="mb-0 fw-bold">Nomor WhatsApp Bot</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kecamatan.settings.waha-n8n.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-700 fw-semibold">
                                <i class="fab fa-whatsapp text-success me-1"></i>
                                Nomor WhatsApp Bot
                            </label>
                            <input type="text" name="bot_number"
                                value="{{ old('bot_number', $settings->bot_number ? '0' . substr(preg_replace('/^62/', '', $settings->bot_number), 0) : '') }}"
                                class="form-control bg-white border-slate-200 rounded-3 @error('bot_number') is-invalid @enderror"
                                placeholder="08xxxxxxxxxx">
                            @error('bot_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-slate-400">
                                Format: 08xxxxxxxxxx (akan dikonversi ke 628xxx)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-slate-700 fw-semibold d-block">Status Bot</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="bot_enabled" value="1" {{ $settings->bot_enabled ? 'checked' : '' }} style="width: 50px; height: 25px;">
                                </div>
                                <span class="text-slate-600">
                                    {{ $settings->bot_enabled ? 'Bot Aktif' : 'Bot Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Banner -->
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-eye text-primary"></i>
                    <h6 class="mb-0 fw-bold">Preview Banner</h6>
                </div>
            </div>
            <div class="card-body p-4">
                @if($settings->bot_number && $settings->bot_enabled)
                    <div class="alert alert-success d-flex align-items-center gap-3">
                        <i class="fab fa-whatsapp fa-2x"></i>
                        <div>
                            <strong>WhatsApp Bot Aktif</strong>
                            <div class="mb-0 text-dark">
                                Hubungi kami:
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->bot_number) }}"
                                    target="_blank" class="btn btn-success btn-sm ms-2">
                                    <i class="fab fa-whatsapp me-1"></i>
                                    Hubungi via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary d-flex align-items-center gap-3">
                        <i class="fab fa-whatsapp fa-2x text-muted"></i>
                        <div>
                            <strong>Bot Nonaktif</strong>
                            <div class="mb-0 text-muted">Nomor belum dikonfigurasi atau bot dimatikan.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Catatan -->
        <div class="mt-4 p-3 bg-info bg-opacity-10 rounded-3">
            <div class="d-flex gap-2">
                <i class="fas fa-info-circle text-info mt-1"></i>
                <div>
                    <strong>Catatan:</strong>
                    <ul class="mb-0 small text-muted">
                        <li>Nomor bot ini akan ditampilkan di halaman landing sebagai tombol "Hubungi via WhatsApp"</li>
                        <li>Pengaturan webhook dan koneksi WAHA/n8n dikonfigurasi langsung di dashboard masing-masing</li>
                        <li>Untuk update webhook, silakan akses dashboard WAHA secara langsung</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection