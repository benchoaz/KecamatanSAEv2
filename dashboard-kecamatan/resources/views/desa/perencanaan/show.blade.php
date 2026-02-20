@extends('layouts.desa')

@section('title', 'Detail Perencanaan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-10 mx-auto">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('desa.pemerintahan.detail.perencanaan.index') }}"
                            class="btn btn-sm btn-light border rounded-circle">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h4 class="fw-bold mb-0">{{ $perencanaan->tipe_dokumen }} Tahun {{ $perencanaan->tahun }}</h4>
                    </div>
                    <div>
                        @if($perencanaan->status === 'draft')
                            <form action="{{ route('desa.pemerintahan.detail.perencanaan.submit', $perencanaan->id) }}"
                                method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-brand-600 text-white rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim ke Kecamatan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="text-muted small fw-bold text-uppercase mb-3">Informasi Dokumen</h6>

                                <div class="mb-3">
                                    <label class="text-muted small d-block">Status</label>
                                    <span
                                        class="badge bg-{{ $perencanaan->status === 'diterima' ? 'success' : ($perencanaan->status === 'dikirim' ? 'primary' : 'secondary') }} rounded-pill px-3">
                                        {{ strtoupper($perencanaan->status) }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small d-block">Tahun</label>
                                    <span class="fw-bold text-dark">{{ $perencanaan->tahun }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small d-block">Mode Input</label>
                                    <span class="fw-bold text-slate-700">{{ strtoupper($perencanaan->mode_input) }}</span>
                                </div>

                                @if($perencanaan->nomor_perdes)
                                    <div class="mb-3">
                                        <label class="text-muted small d-block">Nomor Perdes</label>
                                        <span class="fw-bold text-slate-700">{{ $perencanaan->nomor_perdes }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block">Tanggal Penetapan</label>
                                        <span
                                            class="fw-bold text-slate-700">{{ $perencanaan->tanggal_perdes ? $perencanaan->tanggal_perdes->format('d F Y') : '-' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($perencanaan->catatan_kecamatan)
                            <div class="card border-0 shadow-sm rounded-4 bg-light-warning">
                                <div class="card-body p-4 text-warning-900">
                                    <h6 class="fw-bold mb-2"><i class="fas fa-comment-dots me-2"></i> Catatan Kecamatan</h6>
                                    <p class="small mb-0">{{ $perencanaan->catatan_kecamatan }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <div
                                class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Live Preview / Viewer</h6>
                                <a href="{{ asset('storage/' . $perencanaan->file_ba) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-external-link-alt me-2"></i> Buka Fullscreen
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <!-- In a real world app, use a proper PDF viewer. For now, we'll suggest opening in a new tab if iframe fails -->
                                <iframe src="{{ asset('storage/' . $perencanaan->file_ba) }}" width="100%" height="600px"
                                    style="border: none;">
                                    Browsermu tidak mendukung tampilan PDF. Silakan download file di sini: <a
                                        href="{{ asset('storage/' . $perencanaan->file_ba) }}">Download PDF</a>
                                </iframe>
                            </div>
                        </div>
                        {{-- Rincian Kegiatan Section (Conditional for Structured Mode) --}}
                        @if($perencanaan->mode_input === 'terstruktur' && $perencanaan->status === 'diterima')
                            <div class="card border-0 shadow-sm rounded-4 mt-4">
                                <div
                                    class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-success"><i class="fas fa-tasks me-2"></i> Rincian Kegiatan
                                        (RKP/APBDes)</h6>
                                    <button class="btn btn-sm btn-brand-600 text-white rounded-pill px-3">
                                        <i class="fas fa-plus me-1"></i> Tambah Kegiatan
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if($perencanaan->usulan->isEmpty())
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-clipboard-list fa-2x mb-2 opacity-50"></i>
                                            <p class="small mb-0">Belum ada rincian kegiatan yang terhubung.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm small">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Bidang</th>
                                                        <th>Kegiatan</th>
                                                        <th>Anggaran</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($perencanaan->usulan as $usulan)
                                                        <tr>
                                                            <td>{{ $usulan->bidang }}</td>
                                                            <td>{{ $usulan->uraian }}</td>
                                                            <td class="text-end">{{ number_format($usulan->anggaran, 0, ',', '.') }}
                                                            </td>
                                                            <td><span class="badge bg-light text-dark border">On Plan</span></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($perencanaan->mode_input === 'terstruktur')
                            <div class="card border-0 shadow-sm rounded-4 mt-4 bg-light">
                                <div class="card-body text-center py-4 text-muted">
                                    <i class="fas fa-lock fa-2x mb-2 opacity-50"></i>
                                    <p class="small mb-0">Form Rincian Kegiatan akan terbuka otomatis setelah dokumen ini
                                        <strong>Divalidasi / Diterima</strong> oleh Kecamatan.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-light-warning {
            background-color: #fffbeb;
        }

        .text-warning-900 {
            color: #78350f;
        }

        .btn-brand-600 {
            background-color: #9DC183;
            border-color: #9DC183;
        }

        .btn-brand-600:hover {
            background-color: #7fa665;
            border-color: #7fa665;
        }
    </style>
@endsection