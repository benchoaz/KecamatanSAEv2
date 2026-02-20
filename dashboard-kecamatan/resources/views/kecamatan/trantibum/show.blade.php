@extends($layout)

@section('title', 'Detail Laporan Trantibum')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ auth()->user()->desa_id ? route('desa.trantibum.index') : route('kecamatan.trantibum.index') }}">
                <i class="fas fa-arrow-left"></i> Kembali ke Monitoring
            </a>
        </div>
        <div class="header-title d-flex justify-content-between align-items-center w-100">
            <div>
                <h1 class="text-white">Detail Laporan Keamanan</h1>
                <p class="text-muted">ID Laporan: #{{ $report->uuid }} | Asal: {{ $report->desa->nama_desa }}</p>
            </div>
            @if(!auth()->user()->desa_id && $report->status === 'submitted')
                <button class="btn btn-success" onclick="document.getElementById('actionForm').submit()">
                    <i class="fas fa-check-double me-2"></i> Tandai Sudah Ditindaklanjuti
                </button>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header border-secondary py-3">
                    <h6 class="mb-0 text-white">Informasi Laporan</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Kategori Insiden</label>
                            <span class="text-white fw-bold">{{ $report->aspek->nama_aspek }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Waktu Laporan</label>
                            <span class="text-white">{{ $report->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    <hr class="border-secondary">
                    <div class="mt-3">
                        <label class="text-muted small d-block mb-2">Kronologi / Catatan Desa</label>
                        <div class="text-white bg-secondary bg-opacity-10 p-3 rounded border border-secondary border-opacity-25">
                            {{ $report->catatan_review ?? 'Data kronologi detail belum disertakan. Laporan rutin kondusifitas wilayah.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evidence Section -->
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary py-3">
                    <h6 class="mb-0 text-white">Dokumentasi & Bukti Dukung</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($report->buktiDukung as $bukti)
                            <div class="col-md-4">
                                <div class="evidence-card border border-secondary rounded p-2 text-center">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                    <div class="small text-truncate text-white">{{ $bukti->nama_file }}</div>
                                    <a href="/files/{{ $report->id }}/{{ $bukti->nama_file }}" target="_blank" class="btn btn-sm btn-link text-info">Lihat File</a>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">Tidak ada lampiran foto/dokumen.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary py-3">
                    <h6 class="mb-0 text-white">Status & Tracking</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-status ps-3 border-start border-secondary py-2">
                        <div class="status-item mb-4 position-relative">
                            <i class="fas fa-circle text-success position-absolute start-0 translate-middle-x bg-dark px-1" style="font-size: 0.6rem; left: -1px;"></i>
                            <div class="small fw-bold text-white">Laporan Terkirim</div>
                            <div class="extra-small text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="status-item position-relative">
                            <i class="fas fa-circle text-{{ $report->status === 'approved' ? 'success' : 'warning' }} position-absolute start-0 translate-middle-x bg-dark px-1" style="font-size: 0.6rem; left: -1px;"></i>
                            <div class="small fw-bold text-white">{{ $report->status === 'approved' ? 'Selesai / Atensi Camat' : 'Menunggu Atensi' }}</div>
                            <div class="extra-small text-muted">Proses validasi oleh Kasi Trantibum</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="actionForm" action="{{ route('kecamatan.verifikasi.process', $report->id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="action" value="approve">
        <input type="hidden" name="catatan" value="Laporan telah diperiksa dan ditindaklanjuti oleh Kecamatan.">
    </form>
@endsection
