@extends('layouts.kecamatan')

@section('title', $title)

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ route('kecamatan.kesra.index') }}" class="text-primary"><i class="fas fa-arrow-left"></i> Kembali
                ke Dashboard</a>
        </div>
        <div class="header-title">
            <h1>{{ $title }}</h1>
            <p class="text-muted">
                @if($desa_id)
                    {{ $desc }}
                @else
                    Pilih Desa untuk Melihat Detail Monitoring Kesra
                @endif
            </p>
        </div>
    </div>

    @if(!$desa_id)
        <div class="card bg-white border-gray-200 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Nama Desa</th>
                            <th class="text-center">Total Laporan Masuk</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desas as $index => $desa)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-slate-700"> Desa {{ $desa->nama_desa }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-soft text-primary px-3 py-2"
                                        style="font-size: 0.85rem;">{{ $desa->submissions_count }} Laporan</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card bg-white border-gray-200 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-gray-800 fw-bold">Daftar Laporan Desa</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">Desa</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Diterima</th>
                                <th class="text-end pe-4">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $s)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $s->desa->nama_desa }}</div>
                                        <div class="extra-small text-muted">Oleh: {{ $s->submittedBy->name ?? 'System' }}</div>
                                    </td>
                                    <td>{{ $s->tahun }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $s->status === 'submitted' ? 'info' : ($s->status === 'returned' ? 'warning' : 'success') }}">
                                            {{ strtoupper($s->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('kecamatan.verifikasi.show', $s->uuid) }}"
                                            class="btn btn-sm btn-outline-info me-1">
                                            <i class="fas fa-eye me-1"></i> Periksa
                                        </a>
                                        @if($s->status === 'submitted')
                                            <button class="btn btn-sm btn-primary"
                                                onclick="reviewSubmission({{ $s->id }}, '{{ $s->desa->nama_desa }}')">
                                                <i class="fas fa-tasks me-1"></i> Telaah
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada laporan untuk kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <div class="modal fade" id="reviewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="reviewForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Telaah Laporan: <span id="desaName"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Aksi Rekomendasi</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="action" id="act_return" value="return"
                                            required>
                                        <label class="btn btn-outline-danger w-100 py-3" for="act_return">
                                            <i class="fas fa-undo mb-2 d-block fa-lg"></i>
                                            Kembalikan (Perbaiki)
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="action" id="act_recommend"
                                            value="recommend">
                                        <label class="btn btn-outline-success w-100 py-3" for="act_recommend">
                                            <i class="fas fa-check-double mb-2 d-block fa-lg"></i>
                                            Rekomendasikan ke Camat
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan Penelaahan <span class="text-danger">*</span></label>
                                <textarea name="catatan" class="form-control" rows="4"
                                    placeholder="Berikan catatan perbaikan atau alasan kelayakan..." required
                                    minlength="10"></textarea>
                                <small class="text-muted">Min. 10 karakter. Catatan akan dikirim ke desa & audit log.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Hasil Telaah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
@endpush

@push('scripts')
    <script>
        function reviewSubmission(id, desa) {
            document.getElementById('desaName').innerText = desa;
            document.getElementById('reviewForm').action = "/kecamatan/kesra/process/" + id;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        }
    </script>
@endpush