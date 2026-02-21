@extends('layouts.kecamatan')

@section('title', 'Monitoring WhatsApp Bot')

@push('styles')
<style>
    .connection-card {
        transition: all 0.3s ease;
    }
    .connection-card:hover {
        transform: translateY(-2px);
    }
    .connection-card.connected {
        border-left: 4px solid #10b981;
    }
    .connection-card.disconnected {
        border-left: 4px solid #ef4444;
    }
    .connection-card.checking {
        border-left: 4px solid #f59e0b;
    }
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .status-indicator.online {
        background-color: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
    }
    .status-indicator.offline {
        background-color: #ef4444;
    }
    .status-indicator.checking {
        background-color: #f59e0b;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .qr-container {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
    }
    .qr-code {
        max-width: 256px;
        margin: 0 auto;
    }
    .toggle-switch {
        position: relative;
        width: 50px;
        height: 26px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.4s;
        border-radius: 26px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: #10b981;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    .info-badge {
        font-size: 0.75rem;
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 8px;
        border-radius: 6px;
        font-family: monospace;
        word-break: break-all;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="content-header mb-4">
        <div class="header-title">
            <h1 class="text-slate-900 fw-bold display-6">
                <i class="fab fa-whatsapp text-success me-2"></i>
                Monitoring WhatsApp Bot
            </h1>
            <p class="text-slate-500 fs-5 mb-0">
                Status koneksi dan pengaturan nomor bot WhatsApp.
            </p>
            <div class="header-accent"></div>
        </div>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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

    <!-- Connection Status Cards -->
    <div class="row mb-4">
        <!-- WAHA Card -->
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 connection-card {{ $settings->is_waha_connected ? 'connected' : 'disconnected' }}" id="waha-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg bg-green-100 text-green-600 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                                <i class="fas fa-comments fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-slate-900">WAHA</h5>
                                <small class="text-slate-500">WhatsApp HTTP API</small>
                            </div>
                        </div>
                        <div id="waha-status" class="d-flex align-items-center">
                            <span class="status-indicator {{ $settings->is_waha_connected ? 'online' : 'offline' }}"></span>
                            <span class="fw-semibold {{ $settings->is_waha_connected ? 'text-success' : 'text-danger' }}">
                                {{ $settings->is_waha_connected ? 'Terhubung' : 'Terputus' }}
                            </span>
                        </div>
                    </div>
                    <!-- Read-only info -->
                    <div class="mb-3">
                        <small class="text-slate-400 d-block mb-1">Endpoint</small>
                        <span class="info-badge">{{ $settings->waha_api_url ?? '-' }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm flex-fill" onclick="checkWahaConnection()">
                            <i class="fas fa-sync-alt me-1"></i> Cek Koneksi
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="startWahaSession()">
                            <i class="fas fa-play me-1"></i> Start Session
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- n8n Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 connection-card {{ $settings->is_n8n_connected ? 'connected' : 'disconnected' }}" id="n8n-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg bg-orange-100 text-orange-600 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                                <i class="fas fa-project-diagram fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-slate-900">n8n</h5>
                                <small class="text-slate-500">Workflow Automation</small>
                            </div>
                        </div>
                        <div id="n8n-status" class="d-flex align-items-center">
                            <span class="status-indicator {{ $settings->is_n8n_connected ? 'online' : 'offline' }}"></span>
                            <span class="fw-semibold {{ $settings->is_n8n_connected ? 'text-success' : 'text-danger' }}">
                                {{ $settings->is_n8n_connected ? 'Terhubung' : 'Terputus' }}
                            </span>
                        </div>
                    </div>
                    <!-- Read-only info -->
                    <div class="mb-3">
                        <small class="text-slate-400 d-block mb-1">Endpoint</small>
                        <span class="info-badge">{{ $settings->n8n_api_url ?? '-' }}</span>
                    </div>
                    <button class="btn btn-outline-warning btn-sm w-100" onclick="checkN8nConnection()">
                        <i class="fas fa-sync-alt me-1"></i> Cek Koneksi n8n
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bot Status + Cek Semua -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-success text-white" style="width:48px;height:48px;">
                                    <i class="fab fa-whatsapp fa-lg"></i>
                                </div>
                                <div>
                                        <h5 class="mb-0 fw-bold text-slate-900">WhatsApp Bot</h5>
                                        <small class="text-slate-500">
                                            Nomor: <strong>
                                                @if($settings->bot_number)
                                                    {{ '0' . substr(preg_replace('/^62/', '', $settings->bot_number), 0) }}
                                                @else
                                                    Belum dikonfigurasi
                                                @endif
                                            </strong>
                                        </small>
                                    </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mt-3 mt-md-0">
                            <div id="bot-status-badge">
                                {!! $settings->getStatusBadge() !!}
                            </div>
                            <small class="text-slate-400 d-block mt-1" id="last-check">
                                @if($settings->last_connection_check)
                                    Terakhir dicek: {{ $settings->last_connection_check->diffForHumans() }}
                                @else
                                    Belum pernah dicek
                                @endif
                            </small>
                        </div>
                        <div class="col-md-3 text-end mt-3 mt-md-0">
                            <button class="btn btn-primary" onclick="checkAllConnections()">
                                <i class="fas fa-check-double me-1"></i> Cek Semua
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Section (hidden by default) -->
    <div class="row mb-4" id="qr-section" style="display: none;">
        <div class="col-md-6 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-slate-900">
                            <i class="fas fa-qrcode me-2"></i>QR Code WhatsApp
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary" onclick="loadQrCode()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="qr-container" id="qr-container">
                        <div class="text-center text-slate-400">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                            <p>Memuat QR Code...</p>
                        </div>
                    </div>
                    <p class="text-center text-slate-500 small mt-3 mb-0">
                        Scan QR Code dengan aplikasi WhatsApp di HP Anda untuk menghubungkan bot.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bot Settings Form (only editable fields) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-mobile-alt text-success"></i>
                <h5 class="mb-0 fw-bold">Pengaturan Bot</h5>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('kecamatan.settings.waha-n8n.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4 align-items-end">
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
                        <div class="form-text text-slate-400">Format: 08xxxxxxxxxx (bisa juga 628xxx)</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-slate-700 fw-semibold d-block">Status Bot</label>
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <label class="toggle-switch">
                                <input type="checkbox" name="bot_enabled" value="1"
                                    {{ $settings->bot_enabled ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
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

                <!-- Logout Session -->
                <div class="mt-4 pt-3 border-top border-light d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-slate-400">
                            <i class="fas fa-info-circle me-1"></i>
                            Session: <span class="info-badge">{{ $settings->waha_session_name ?? 'default' }}</span>
                            &nbsp;|&nbsp;
                            Webhook: <span class="info-badge">{{ $settings->n8n_webhook_url ?? '-' }}</span>
                        </small>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="logoutSession()">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout Session WA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Test Message Section -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-paper-plane text-primary"></i>
                <h5 class="mb-0 fw-bold">Test Kirim Pesan</h5>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-slate-700 fw-semibold">Nomor Tujuan</label>
                    <input type="text" id="test-phone" class="form-control bg-white border-slate-200 rounded-3"
                        placeholder="628xxxxxxxxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-slate-700 fw-semibold">Pesan</label>
                    <input type="text" id="test-message" class="form-control bg-white border-slate-200 rounded-3"
                        placeholder="Halo, ini pesan test dari bot" value="Test koneksi bot WhatsApp.">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100" onclick="sendTestMessage()">
                        <i class="fas fa-paper-plane me-1"></i> Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Check WAHA Connection
    function checkWahaConnection() {
        const statusEl = document.getElementById('waha-status');
        const cardEl = document.getElementById('waha-card');

        statusEl.innerHTML = '<span class="status-indicator checking"></span><span class="fw-semibold text-warning">Mengecek...</span>';
        cardEl.classList.remove('connected', 'disconnected');
        cardEl.classList.add('checking');

        fetch('{{ route("kecamatan.settings.waha-n8n.check-waha") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            cardEl.classList.remove('checking');
            if (data.success) {
                const isConnected = data.status === 'WORKING' || data.status === 'CONNECTED' || data.status === 'ONLINE';
                cardEl.classList.add(isConnected ? 'connected' : 'disconnected');
                statusEl.innerHTML = `<span class="status-indicator ${isConnected ? 'online' : 'offline'}"></span>
                    <span class="fw-semibold ${isConnected ? 'text-success' : 'text-danger'}">${data.message}</span>`;

                if (data.status === 'SCAN_QR_CODE') {
                    document.getElementById('qr-section').style.display = 'block';
                    loadQrCode();
                }
            } else {
                cardEl.classList.add('disconnected');
                statusEl.innerHTML = `<span class="status-indicator offline"></span><span class="fw-semibold text-danger">${data.message}</span>`;
            }
            showToast(data.message, data.success ? 'success' : 'error');
        })
        .catch(() => {
            cardEl.classList.remove('checking');
            cardEl.classList.add('disconnected');
            statusEl.innerHTML = '<span class="status-indicator offline"></span><span class="fw-semibold text-danger">Gagal terhubung</span>';
            showToast('Gagal mengecek koneksi WAHA', 'error');
        });
    }

    // Check n8n Connection
    function checkN8nConnection() {
        const statusEl = document.getElementById('n8n-status');
        const cardEl = document.getElementById('n8n-card');

        statusEl.innerHTML = '<span class="status-indicator checking"></span><span class="fw-semibold text-warning">Mengecek...</span>';
        cardEl.classList.remove('connected', 'disconnected');
        cardEl.classList.add('checking');

        fetch('{{ route("kecamatan.settings.waha-n8n.check-n8n") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            cardEl.classList.remove('checking');
            cardEl.classList.add(data.success ? 'connected' : 'disconnected');
            statusEl.innerHTML = `<span class="status-indicator ${data.success ? 'online' : 'offline'}"></span>
                <span class="fw-semibold ${data.success ? 'text-success' : 'text-danger'}">${data.message}</span>`;
            showToast(data.message, data.success ? 'success' : 'error');
        })
        .catch(() => {
            cardEl.classList.remove('checking');
            cardEl.classList.add('disconnected');
            statusEl.innerHTML = '<span class="status-indicator offline"></span><span class="fw-semibold text-danger">Gagal terhubung</span>';
            showToast('Gagal mengecek koneksi n8n', 'error');
        });
    }

    // Check All Connections
    function checkAllConnections() {
        checkWahaConnection();
        setTimeout(() => checkN8nConnection(), 600);
    }

    // Start WAHA Session
    function startWahaSession() {
        Swal.fire({
            title: 'Memulai Session...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('{{ route("kecamatan.settings.waha-n8n.start-session") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                setTimeout(() => checkWahaConnection(), 2000);
            }
        })
        .catch(() => {
            Swal.close();
            showToast('Gagal memulai session', 'error');
        });
    }

    // Logout Session
    function logoutSession() {
        Swal.fire({
            title: 'Logout Session?',
            text: 'Anda akan logout dari session WhatsApp saat ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("kecamatan.settings.waha-n8n.logout-session") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    showToast(data.message, data.success ? 'success' : 'error');
                    if (data.success) setTimeout(() => location.reload(), 1500);
                })
                .catch(() => showToast('Gagal logout session', 'error'));
            }
        });
    }

    // Load QR Code
    function loadQrCode() {
        const container = document.getElementById('qr-container');
        container.innerHTML = '<div class="text-center text-slate-400"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><p>Memuat QR Code...</p></div>';

        fetch('{{ route("kecamatan.settings.waha-n8n.qr-code") }}')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.qr) {
                const qrSrc = data.qr.uri || data.qr;
                container.innerHTML = `<img src="${qrSrc}" class="qr-code img-fluid" alt="QR Code">`;
            } else {
                container.innerHTML = `<div class="text-center text-danger"><i class="fas fa-exclamation-circle fa-2x mb-3"></i><p>${data.message || 'Gagal memuat QR Code'}</p></div>`;
            }
        })
        .catch(() => {
            container.innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-circle fa-2x mb-3"></i><p>Gagal memuat QR Code</p></div>';
        });
    }

    // Send Test Message
    function sendTestMessage() {
        const phone = document.getElementById('test-phone').value.trim();
        const message = document.getElementById('test-message').value.trim();

        if (!phone || !message) {
            showToast('Nomor dan pesan harus diisi', 'error');
            return;
        }

        Swal.fire({
            title: 'Mengirim pesan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('{{ route("kecamatan.settings.waha-n8n.test-message") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone, message })
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            showToast(data.message, data.success ? 'success' : 'error');
        })
        .catch(() => {
            Swal.close();
            showToast('Gagal mengirim pesan', 'error');
        });
    }

    // Toast Notification
    function showToast(message, type = 'info') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
        Toast.fire({ icon: type, title: message });
    }

    // Auto-check on page load
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => checkAllConnections(), 800);
    });
</script>
@endpush
