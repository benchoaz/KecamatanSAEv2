@extends('layouts.kecamatan')

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="fas fa-key me-2 text-primary"></i>Manajemen API Token
                </h4>
                <p class="text-muted mb-0">Kelola token akses untuk integrasi eksternal (n8n, bot WhatsApp, dll)</p>
            </div>
            <a href="{{ route('kecamatan.settings.api-tokens.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i>Buat Token Baru
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ⚠️ Banner: Token Hilang / Lupa --}}
        <div class="card border-0 mb-4"
            style="background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); border-left: 5px solid #ffc107 !important;">
            <div class="card-body py-3 d-flex align-items-start gap-3">
                <div class="fs-3 text-warning pt-1">⚠️</div>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Token hilang atau tidak tersimpan?</h6>
                    <p class="mb-2 text-dark small">
                        Token API <strong>hanya ditampilkan sekali</strong> saat pertama kali dibuat dan tidak bisa dilihat
                        lagi.
                        Jika hilang, <strong>cabut token lama</strong> lalu buat token baru.
                    </p>
                    <a href="{{ route('kecamatan.settings.api-tokens.create') }}" class="btn btn-sm btn-warning fw-bold">
                        <i class="fas fa-plus me-1"></i>Buat Token Baru Sekarang
                    </a>
                </div>
            </div>
        </div>

        {{-- Token Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-list me-2 text-muted"></i>Daftar Token Aktif</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Token</th>
                                <th>Dibuat Oleh</th>
                                <th>Abilities</th>
                                <th>Terakhir Digunakan</th>
                                <th>Kedaluwarsa</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $token)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $token->name }}</div>
                                        <small class="text-muted">Dibuat {{ $token->created_at->format('d M Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-circle me-1 text-muted"></i>
                                        {{ $token->user->nama_lengkap }}
                                    </td>
                                    <td>
                                        @if($token->abilities)
                                            @foreach($token->abilities as $ability)
                                                <span
                                                    class="badge bg-info bg-opacity-15 text-info border border-info me-1 mb-1">{{ $ability }}</span>
                                            @endforeach
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary">Full
                                                Access</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->last_used_at)
                                            <span class="text-success small"><i class="fas fa-circle me-1"
                                                    style="font-size:.55rem"></i>{{ $token->last_used_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-muted small">Belum pernah</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->expires_at)
                                            <span class="small">{{ $token->expires_at->format('d M Y') }}</span>
                                        @else
                                            <span class="badge bg-light text-muted border">∞ Permanen</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->isRevoked())
                                            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i>Dicabut</span>
                                        @elseif($token->isExpired())
                                            <span class="badge bg-warning text-dark"><i
                                                    class="fas fa-clock me-1"></i>Kedaluwarsa</span>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            {{-- View Detail --}}
                                            <a href="{{ route('kecamatan.settings.api-tokens.show', $token) }}"
                                                class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Revoke --}}
                                            @if($token->isValid())
                                                <form action="{{ route('kecamatan.settings.api-tokens.revoke', $token) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Cabut token ini? Token tidak dapat digunakan setelah dicabut.')">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                                        title="Cabut Token">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            {{-- Delete --}}
                                            <form action="{{ route('kecamatan.settings.api-tokens.destroy', $token) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus token secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Token">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-key fa-3x text-muted opacity-50"></i>
                                        </div>
                                        <p class="text-muted mb-3">Belum ada API token yang dibuat</p>
                                        <a href="{{ route('kecamatan.settings.api-tokens.create') }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Buat Token Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{ $tokens->links() }}

        {{-- Info & Cara Pakai --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-shield-alt me-2 text-primary"></i>Keamanan Token</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small text-muted mb-0 ps-3">
                            <li class="mb-2">Token disimpan dalam bentuk hash (SHA-256) — tidak bisa dilihat lagi</li>
                            <li class="mb-2">Token hanya ditampilkan <strong>sekali</strong> saat pertama kali dibuat</li>
                            <li class="mb-2">Jika token hilang → cabut & buat token baru</li>
                            <li>Jangan bagikan token kepada siapapun</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="fas fa-code me-2 text-primary"></i>Cara Pakai di n8n / Bot
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">Tambahkan di header HTTP Request:</p>
                        <pre
                            class="bg-dark text-light p-3 rounded small mb-2"><code>Authorization: Bearer &lt;TOKEN_ANDA&gt;</code></pre>
                        <p class="small text-muted mb-0">Atau set sebagai environment variable
                            <code>DASHBOARD_API_TOKEN</code> di n8n.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection