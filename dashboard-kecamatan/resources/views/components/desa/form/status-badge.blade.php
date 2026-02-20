@props(['status', 'catatan' => null])

@php
    $badgeClass = match ($status) {
        'draft' => 'bg-secondary-subtle text-secondary border-secondary',
        'dikirim' => 'bg-primary-subtle text-primary border-primary',
        'dikembalikan' => 'bg-warning-subtle text-warning-900 border-warning',
        'diterima' => 'bg-success-subtle text-success border-success',
        default => 'bg-light text-muted'
    };

    $icon = match ($status) {
        'draft' => 'fa-pencil-alt',
        'dikirim' => 'fa-paper-plane',
        'dikembalikan' => 'fa-exclamation-triangle',
        'diterima' => 'fa-check-circle',
        default => 'fa-circle'
    };

    $label = match ($status) {
        'draft' => 'DRAFT',
        'dikirim' => 'DIKIRIM',
        'dikembalikan' => 'PERLU REVISI',
        'diterima' => 'DITERIMA',
        default => 'UNKNOWN'
    };
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-4 d-flex align-items-center justify-content-between">
        <div>
            <div class="small fw-bold text-uppercase text-slate-400 mb-1">Status Dokumen</div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge {{ $badgeClass }} border px-3 py-1.5 rounded-pill d-flex align-items-center gap-2">
                    <i class="fas {{ $icon }}"></i> {{ $label }}
                </span>
                @if($status === 'dikirim')
                    <span class="text-slate-500 small"><i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                        Kecamatan</span>
                @elseif($status === 'diterima')
                    <span class="text-success small fw-bold"><i class="fas fa-check-double me-1"></i> Data Sah &
                        Terarsip</span>
                @endif
            </div>
        </div>
    </div>
    @if($status === 'dikembalikan' && $catatan)
        <div class="bg-warning-subtle px-4 py-3 border-top border-warning-subtle">
            <div class="d-flex">
                <i class="fas fa-comment-alt text-warning-900 mt-1 me-3"></i>
                <div>
                    <strong class="text-warning-900 d-block">Catatan Kecamatan:</strong>
                    <p class="mb-0 text-slate-800">{{ $catatan }}</p>
                </div>
            </div>
        </div>
    @endif
</div>