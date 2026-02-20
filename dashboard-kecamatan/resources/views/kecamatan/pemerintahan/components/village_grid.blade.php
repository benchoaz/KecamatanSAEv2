<div class="row g-4">
    @foreach($desas as $desa)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-translate-y">
                <div class="card-body p-4 text-center">
                    <div class="village-icon-circle mb-3 mx-auto shadow-sm">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <h5 class="card-title fw-bold text-slate-800 mb-1">{{ $desa->nama_desa }}</h5>
                    <p class="text-muted small mb-4">Kode Desa: {{ $desa->kode_desa }}</p>

                    <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                        class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                        Lihat Detail <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    .village-icon-circle {
        width: 60px;
        height: 60px;
        background: var(--surface-bg);
        color: var(--brand-color);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 24px;
        border: 2px solid #e2e8f0;
    }

    .hover-translate-y:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    .text-slate-800 {
        color: #1e293b;
    }
</style>