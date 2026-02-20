@extends('layouts.kecamatan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @if($plainToken)
                    {{-- ════════════════════════════════════════════════════
                    SEKALI TAMPIL: Token berhasil dibuat
                    ════════════════════════════════════════════════════ --}}

                    {{-- Alert: berhasil --}}
                    <div class="alert alert-success d-flex align-items-start gap-3 mb-4 border-0 shadow-sm">
                        <div class="fs-3">✅</div>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1">Token Berhasil Dibuat!</h5>
                            <p class="mb-0">Salin token di bawah <strong>sekarang juga</strong>. Token ini <strong
                                    class="text-danger">tidak akan ditampilkan lagi</strong> setelah Anda meninggalkan halaman
                                ini.</p>
                        </div>
                    </div>

                    {{-- ─── TOKEN DISPLAY CARD ─── --}}
                    <div class="card border-0 shadow mb-4" style="border-left: 5px solid #198754 !important;">
                        <div class="card-header d-flex align-items-center gap-2 py-3"
                            style="background: linear-gradient(135deg, #198754, #20c997); border-radius: .375rem .375rem 0 0;">
                            <i class="fas fa-key text-white fs-5"></i>
                            <span class="text-white fw-bold fs-6">DASHBOARD_API_TOKEN</span>
                            <span class="badge bg-white text-success ms-auto">Salin & Simpan</span>
                        </div>
                        <div class="card-body p-4">

                            {{-- Label panduan --}}
                            <label class="small text-muted fw-semibold mb-2 d-block text-uppercase tracking-wide">
                                <i class="fas fa-clipboard me-1"></i>Token (klik tombol Salin di kanan):
                            </label>

                            {{-- Input + Copy Button --}}
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-dark text-success border-0 font-monospace px-3">
                                    <i class="fas fa-lock me-1"></i>
                                </span>
                                <input type="text" class="form-control font-monospace border-0 bg-dark text-success"
                                    id="tokenDisplay" value="{{ $plainToken }}" readonly
                                    style="letter-spacing: .03em; font-size: .9rem;">
                                <button class="btn btn-success px-4 fw-bold" type="button" id="copyBtn" onclick="copyToken()">
                                    <i class="fas fa-copy me-2"></i>Salin
                                </button>
                            </div>

                            {{-- Instruksi n8n --}}
                            <div class="mt-4 p-3 rounded" style="background: #f8f9fa; border-left: 4px solid #0dcaf0;">
                                <p class="small fw-semibold mb-1 text-dark">
                                    <i class="fas fa-robot me-1 text-info"></i>Cara pakai di n8n:
                                </p>
                                <p class="small text-muted mb-2">Set sebagai <strong>Environment Variable</strong> di n8n:</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text font-monospace bg-dark text-warning border-0">Key:</span>
                                    <input type="text" class="form-control font-monospace bg-dark text-light border-0"
                                        value="DASHBOARD_API_TOKEN" readonly>
                                    <span
                                        class="input-group-text font-monospace bg-dark text-success border-0 border-start border-secondary">Value:</span>
                                    <input type="text" class="form-control font-monospace bg-dark text-success border-0"
                                        id="tokenDisplayEnv" value="{{ $plainToken }}" readonly>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="copyEnvValue()"
                                        title="Salin nilai token">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <p class="small text-muted mt-2 mb-0">
                                    Atau gunakan langsung di HTTP Header:
                                    <code
                                        class="bg-dark text-light px-2 py-1 rounded d-inline-block mt-1">Authorization: Bearer {{ Str::limit($plainToken, 20) }}...</code>
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- ⚠️ Peringatan penting --}}
                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Peringatan Penting</h6>
                        <ul class="mb-0 small">
                            <li>Token <strong>hanya ditampilkan sekali ini saja</strong> — setelah meninggalkan halaman ini,
                                token tidak bisa dilihat lagi</li>
                            <li>Jika token hilang atau lupa, Anda harus <strong>membuat token baru</strong></li>
                            <li>Simpan token di tempat aman (password manager, .env file)</li>
                            <li>Jangan bagikan token kepada siapapun</li>
                        </ul>
                    </div>

                @endif {{-- end if $plainToken --}}

                {{-- ─── DETAIL TOKEN ─── --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2 text-primary"></i>Detail Token</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0">
                            <tr class="border-bottom">
                                <td class="ps-4 fw-semibold text-muted small text-uppercase py-3" style="width: 180px;">Nama
                                </td>
                                <td class="py-3 fw-bold">{{ $apiToken->name }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="ps-4 fw-semibold text-muted small text-uppercase py-3">Dibuat Oleh</td>
                                <td class="py-3"><i
                                        class="fas fa-user-circle me-2 text-muted"></i>{{ $apiToken->user->nama_lengkap }}
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="ps-4 fw-semibold text-muted small text-uppercase py-3">Dibuat Pada</td>
                                <td class="py-3">{{ $apiToken->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="ps-4 fw-semibold text-muted small text-uppercase py-3">Abilities</td>
                                <td class="py-3">
                                    @if($apiToken->abilities)
                                        @foreach($apiToken->abilities as $ability)
                                            <span
                                                class="badge bg-info bg-opacity-15 text-info border border-info me-1">{{ $ability }}</span>
                                        @endforeach
                                    @else
                                        <span
                                            class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary">Full
                                            Access</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="ps-4 fw-semibold text-muted small text-uppercase py-3">Kedaluwarsa</td>
                                <td class="py-3">
                                    @if($apiToken->expires_at)
                                        {{ $apiToken->expires_at->format('d M Y H:i:s') }}
                                    @else
                                        <span class="badge bg-light text-muted border">∞ Tidak ada batas waktu</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-semibold text-muted small text-uppercase py-3">Status</td>
                                <td class="py-3">
                                    @if($apiToken->isRevoked())
                                        <span class="badge bg-danger"><i class="fas fa-ban me-1"></i>Dicabut</span>
                                    @elseif($apiToken->isExpired())
                                        <span class="badge bg-warning text-dark"><i
                                                class="fas fa-clock me-1"></i>Kedaluwarsa</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-top py-3 d-flex gap-2">
                        <a href="{{ route('kecamatan.settings.api-tokens.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                        @if($plainToken)
                            <a href="{{ route('kecamatan.settings.api-tokens.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Token Lain
                            </a>
                        @else
                            <a href="{{ route('kecamatan.settings.api-tokens.create') }}" class="btn btn-warning">
                                <i class="fas fa-redo me-2"></i>Buat Token Baru (Token Lama Hilang?)
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyToken() {
                const val = document.getElementById('tokenDisplay').value;
                copyToClipboard(val, 'copyBtn', '<i class="fas fa-copy me-2"></i>Salin');
            }

            function copyEnvValue() {
                const val = document.getElementById('tokenDisplayEnv').value;
                navigator.clipboard.writeText(val).then(() => {
                    showToast('Nilai token disalin!');
                });
            }

            function copyToClipboard(text, btnId, originalHtml) {
                navigator.clipboard.writeText(text).then(() => {
                    const btn = document.getElementById(btnId);
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Disalin!';
                    btn.classList.replace('btn-success', 'btn-secondary');
                    showToast('Token berhasil disalin ke clipboard!');
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.replace('btn-secondary', 'btn-success');
                    }, 3000);
                }).catch(() => {
                    document.execCommand('copy');
                    alert('Token disalin ke clipboard!');
                });
            }

            function showToast(msg) {
                // Simple toast notification
                const toast = document.createElement('div');
                toast.className = 'position-fixed bottom-0 end-0 m-4 p-3 bg-success text-white rounded shadow';
                toast.style.cssText = 'z-index:9999; font-size:.9rem; min-width:220px;';
                toast.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + msg;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        </script>
    @endpush

@endsection