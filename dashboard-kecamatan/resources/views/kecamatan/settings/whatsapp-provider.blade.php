@extends('layouts.kecamatan')

@section('title', 'Provider WhatsApp')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
            <i class="fab fa-whatsapp text-success fs-3"></i>
        </div>
        <div>
            <h1 class="fw-bold fs-4 mb-0">Konfigurasi Provider WhatsApp</h1>
            <p class="text-muted small mb-0">
                Pilih dan konfigurasi gateway WhatsApp. Jika WAHA bermasalah, ganti provider tanpa ubah kode.
            </p>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('kecamatan.settings.waha-n8n.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Pengaturan Bot
            </a>
            <a href="{{ route('kecamatan.settings.waha-n8n.workflow.download') }}"
               class="btn btn-outline-primary btn-sm" title="Download workflow n8n yang siap import">
                <i class="fas fa-download me-1"></i> Download Workflow n8n
            </a>
        </div>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
        });
    </script>
    @endif

    {{-- Active provider badge --}}
    <div class="alert alert-info border-0 d-flex align-items-center gap-3 mb-4">
        <i class="fas fa-plug fs-5"></i>
        <div>
            Provider aktif saat ini:
            <strong class="ms-1">{{ $settings->getActiveProviderLabel() }}</strong>
            &nbsp;
            <span class="badge bg-{{ $settings->getActiveProvider() === 'waha' ? 'success' : 'primary' }}">
                {{ strtoupper($settings->getActiveProvider()) }}
            </span>
        </div>
    </div>

    <form action="{{ route('kecamatan.settings.waha-n8n.provider.update') }}" method="POST" id="providerForm">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- LEFT: Provider Selector + n8n config --}}
            <div class="col-lg-4">

                {{-- Provider selector --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light fw-semibold">
                        <i class="fas fa-exchange-alt text-primary me-2"></i> Pilih Provider
                    </div>
                    <div class="card-body p-3">
                        @foreach($providers as $key => $label)
                        <div class="form-check provider-option p-3 rounded-3 mb-2 border {{ $settings->getActiveProvider() === $key ? 'border-primary bg-primary bg-opacity-5' : 'border-light' }}"
                             style="cursor:pointer;" onclick="selectProvider('{{ $key }}')">
                            <input class="form-check-input" type="radio" name="active_provider"
                                   id="provider_{{ $key }}" value="{{ $key }}"
                                   {{ $settings->getActiveProvider() === $key ? 'checked' : '' }}>
                            <label class="form-check-label d-flex align-items-center gap-2 w-100" for="provider_{{ $key }}" style="cursor:pointer;">
                                @if($key === 'waha')
                                    <span class="badge bg-success">WAHA</span>
                                @elseif($key === 'fonnte')
                                    <span class="badge bg-warning text-dark">Fonnte</span>
                                @elseif($key === 'ultramsg')
                                    <span class="badge bg-info">UltraMsg</span>
                                @else
                                    <span class="badge bg-secondary">HTTP</span>
                                @endif
                                <span>{{ $label }}</span>
                            </label>
                        </div>
                        @endforeach

                        {{-- Test connection --}}
                        <button type="button" class="btn btn-outline-success w-100 mt-2" id="btnTestConn">
                            <i class="fas fa-plug me-1"></i> Test Koneksi
                        </button>
                        <div id="testResult" class="mt-2 d-none"></div>
                    </div>
                </div>

                {{-- n8n config --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light fw-semibold">
                        <i class="fas fa-project-diagram text-warning me-2"></i> Konfigurasi n8n
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">URL n8n</label>
                            <input type="url" name="n8n_api_url" class="form-control form-control-sm"
                                   placeholder="http://localhost:5678"
                                   value="{{ old('n8n_api_url', $settings->n8n_api_url) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">API Key n8n</label>
                            <input type="password" name="n8n_api_key" class="form-control form-control-sm"
                                   placeholder="n8n_api_xxx..."
                                   value="{{ old('n8n_api_key', $settings->n8n_api_key) }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Webhook URL n8n</label>
                            <input type="url" name="n8n_webhook_url" class="form-control form-control-sm"
                                   placeholder="http://n8n:5678/webhook/whatsapp-primary"
                                   value="{{ old('n8n_webhook_url', $settings->n8n_webhook_url) }}">
                            <div class="form-text">URL ini digunakan sebagai trigger node di workflow n8n.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Per-provider config panels --}}
            <div class="col-lg-8">

                {{-- WAHA --}}
                <div class="provider-panel card border-0 shadow-sm rounded-4" id="panel_waha"
                     style="{{ $settings->getActiveProvider() !== 'waha' ? 'display:none' : '' }}">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
                        <span class="badge bg-success px-3 py-2">WAHA</span>
                        <span class="fw-semibold">WAHA — Self-hosted WhatsApp API</span>
                        <a href="https://waha.devlike.pro/" target="_blank" class="ms-auto text-muted small">
                            <i class="fas fa-external-link-alt me-1"></i>Docs
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-success border-0 small mb-3">
                            <i class="fas fa-check-circle me-1"></i>
                            Provider default. Gratis, self-hosted di server sendiri menggunakan Docker.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">WAHA API URL <span class="text-danger">*</span></label>
                                <input type="url" name="waha_api_url" class="form-control"
                                       placeholder="http://waha:3000"
                                       value="{{ old('waha_api_url', $settings->waha_api_url) }}">
                                <div class="form-text">URL internal container WAHA (atau IP:port jika tidak pakai Docker).</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Session Name</label>
                                <input type="text" name="waha_session_name" class="form-control"
                                       placeholder="default"
                                       value="{{ old('waha_session_name', $settings->waha_session_name ?? 'default') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">API Key WAHA</label>
                                <input type="password" name="waha_api_key" class="form-control"
                                       placeholder="Kosongkan jika tidak ada API key"
                                       value="{{ old('waha_api_key', $settings->waha_api_key) }}">
                                <div class="form-text">Diset via env <code>WAHA_API_KEY</code> di docker-compose. Isi di sini agar tersimpan di DB juga.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fonnte --}}
                <div class="provider-panel card border-0 shadow-sm rounded-4" id="panel_fonnte"
                     style="{{ $settings->getActiveProvider() !== 'fonnte' ? 'display:none' : '' }}">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark px-3 py-2">Fonnte</span>
                        <span class="fw-semibold">Fonnte — Gateway WA Indonesia</span>
                        <a href="https://fonnte.com" target="_blank" class="ms-auto text-muted small">
                            <i class="fas fa-external-link-alt me-1"></i>fonnte.com
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning border-0 small mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Daftar di <a href="https://fonnte.com" target="_blank">fonnte.com</a>, hubungkan nomor WhatsApp, lalu salin token API di bawah.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Token API Fonnte <span class="text-danger">*</span></label>
                                <input type="password" name="fonnte_token" class="form-control"
                                       placeholder="Token dari dashboard Fonnte"
                                       value="{{ old('fonnte_token', $settings->fonnte_token) }}">
                                <div class="form-text">Buka <strong>Fonnte Dashboard → Device → Token</strong>.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Device ID <span class="text-muted">(opsional)</span></label>
                                <input type="text" name="fonnte_device" class="form-control"
                                       placeholder="ID perangkat"
                                       value="{{ old('fonnte_device', $settings->fonnte_device) }}">
                                <div class="form-text">Jika akun punya banyak device.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- UltraMsg --}}
                <div class="provider-panel card border-0 shadow-sm rounded-4" id="panel_ultramsg"
                     style="{{ $settings->getActiveProvider() !== 'ultramsg' ? 'display:none' : '' }}">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
                        <span class="badge bg-info px-3 py-2">UltraMsg</span>
                        <span class="fw-semibold">UltraMsg — WhatsApp Cloud API</span>
                        <a href="https://ultramsg.com" target="_blank" class="ms-auto text-muted small">
                            <i class="fas fa-external-link-alt me-1"></i>ultramsg.com
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 small mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Daftar di <a href="https://ultramsg.com" target="_blank">ultramsg.com</a>, buat instance, hubungkan nomor WA via QR, lalu isi data di bawah.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Instance ID <span class="text-danger">*</span></label>
                                <input type="text" name="ultramsg_instance_id" class="form-control"
                                       placeholder="instance12345"
                                       value="{{ old('ultramsg_instance_id', $settings->ultramsg_instance_id) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Token <span class="text-danger">*</span></label>
                                <input type="password" name="ultramsg_token" class="form-control"
                                       placeholder="Token dari dashboard UltraMsg"
                                       value="{{ old('ultramsg_token', $settings->ultramsg_token) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Generic HTTP --}}
                <div class="provider-panel card border-0 shadow-sm rounded-4" id="panel_generic_http"
                     style="{{ $settings->getActiveProvider() !== 'generic_http' ? 'display:none' : '' }}">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
                        <span class="badge bg-secondary px-3 py-2">Generic</span>
                        <span class="fw-semibold">Generic HTTP — Provider Bebas</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-secondary border-0 small mb-3">
                            <i class="fas fa-code me-1"></i>
                            Cocok untuk provider apapun yang punya REST API (WA-Gateway, Zenziva, dll). Konfigurasi URL, header auth, dan mapping field secara manual.
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Endpoint URL (POST) <span class="text-danger">*</span></label>
                                <input type="url" name="generic_http_url" class="form-control"
                                       placeholder="https://api.contohprovider.com/send"
                                       value="{{ old('generic_http_url', $settings->generic_http_url) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Field Nomor HP</label>
                                <input type="text" name="generic_http_phone_field" class="form-control"
                                       placeholder="target"
                                       value="{{ old('generic_http_phone_field', $settings->generic_http_phone_field ?? 'target') }}">
                                <div class="form-text">Nama key di request body untuk no HP. Contoh: <code>target</code>, <code>to</code>, <code>phone</code></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Field Pesan</label>
                                <input type="text" name="generic_http_message_field" class="form-control"
                                       placeholder="message"
                                       value="{{ old('generic_http_message_field', $settings->generic_http_message_field ?? 'message') }}">
                                <div class="form-text">Nama key di request body untuk teks pesan. Contoh: <code>message</code>, <code>body</code>, <code>text</code></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Headers (JSON)</label>
                                <textarea name="generic_http_headers_raw" class="form-control font-monospace"
                                          rows="4" placeholder='{"Authorization": "Bearer TOKEN_ANDA", "Content-Type": "application/json"}'>{{ old('generic_http_headers_raw', $settings->generic_http_headers ? json_encode($settings->generic_http_headers, JSON_PRETTY_PRINT) : '') }}</textarea>
                                <div class="form-text">Format JSON object. Untuk auth bearer: <code>{"Authorization": "Bearer tokenmu"}</code></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Body Tambahan (JSON) <span class="text-muted">(opsional)</span></label>
                                <textarea name="generic_http_extra_raw" class="form-control font-monospace"
                                          rows="3" placeholder='{"countryCode": "62", "type": "text"}'>{{ old('generic_http_extra_raw', $settings->generic_http_extra_body ? json_encode($settings->generic_http_extra_body, JSON_PRETTY_PRINT) : '') }}</textarea>
                                <div class="form-text">Field tambahan yang selalu dikirim bersama nomor & pesan.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Save button --}}
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-outline-success" id="btnTestConn2">
                        <i class="fas fa-plug me-1"></i> Test Koneksi
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>{{-- end row --}}
    </form>

    {{-- Info box --}}
    <div class="mt-4 p-3 bg-light rounded-3 border small text-muted">
        <i class="fas fa-lightbulb text-warning me-2"></i>
        <strong>Cara Kerja:</strong> Ketika sistem perlu kirim notifikasi WhatsApp,
        pertama dicoba lewat <strong>n8n webhook</strong>. Jika gagal, otomatis jatuh ke provider yang dipilih di sini.
        Kamu bisa ganti provider kapan saja tanpa restart server.
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide provider panels based on selected radio
    function selectProvider(key) {
        document.querySelectorAll('.provider-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.provider-option').forEach(el => {
            el.classList.remove('border-primary', 'bg-primary', 'bg-opacity-5');
            el.classList.add('border-light');
        });

        const panel = document.getElementById('panel_' + key);
        if (panel) panel.style.display = '';

        const option = document.querySelector('#provider_' + key).closest('.provider-option');
        if (option) {
            option.classList.remove('border-light');
            option.classList.add('border-primary', 'bg-primary', 'bg-opacity-5');
        }

        document.getElementById('provider_' + key).checked = true;
    }

    // Test connection (AJAX)
    async function testConnection() {
        const selected = document.querySelector('input[name=active_provider]:checked')?.value;
        if (!selected) { alert('Pilih provider dulu!'); return; }

        const btn = document.getElementById('btnTestConn');
        const btn2 = document.getElementById('btnTestConn2');
        const resultBox = document.getElementById('testResult');

        [btn, btn2].forEach(b => { b.disabled = true; b.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengecek...'; });

        try {
            const res = await fetch('{{ route("kecamatan.settings.waha-n8n.provider.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ provider: selected }),
            });
            const data = await res.json();

            resultBox.className = `mt-2 alert ${data.success ? 'alert-success' : 'alert-danger'} small py-2`;
            resultBox.innerHTML = `
                <i class="fas fa-${data.success ? 'check-circle' : 'times-circle'} me-1"></i>
                <strong>${data.provider_name}:</strong> ${data.message}
            `;
            resultBox.classList.remove('d-none');
        } catch (err) {
            resultBox.className = 'mt-2 alert alert-danger small py-2';
            resultBox.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> Gagal memanggil endpoint: ${err.message}`;
            resultBox.classList.remove('d-none');
        } finally {
            [btn, btn2].forEach(b => { b.disabled = false; b.innerHTML = '<i class="fas fa-plug me-1"></i> Test Koneksi'; });
        }
    }

    document.getElementById('btnTestConn').addEventListener('click', testConnection);
    document.getElementById('btnTestConn2').addEventListener('click', testConnection);
</script>
@endpush
