@extends('layouts.trantibum')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Direktori Relawan</h4>
                    <p class="text-muted mb-0">Database gabungan relawan tangguh bencana se-kecamatan.</p>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row g-4">
                @forelse($relawans as $relawan)
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body text-center pt-4">
                                <div class="mx-auto mb-3" style="width: 80px; height: 80px;">
                                    <img src="{{ $relawan->foto ? asset('storage/' . $relawan->foto) : asset('assets/images/avatars/01.png') }}"
                                        class="img-fluid rounded-circle border border-3 border-light shadow-sm"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <h5 class="fw-bold mb-1">{{ $relawan->nama }}</h5>
                                <p class="text-muted small mb-2">{{ $relawan->jabatan }} - {{ $relawan->desa->nama_desa }}</p>

                                <div class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3">
                                    {{ $relawan->nik }}
                                </div>

                                <div class="d-grid">
                                    @if($relawan->sk_file)
                                        <a href="{{ asset('storage/' . $relawan->sk_file) }}" target="_blank"
                                            class="btn btn-sm btn-outline-danger rounded-pill">
                                            <i class="fas fa-file-pdf me-1"></i> Lihat SK
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-light rounded-pill" disabled>SK Tidak Ada</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center">
                        <p class="text-muted">Belum ada data relawan terdaftar.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $relawans->links() }}
            </div>
        </div>
    </div>
@endsection