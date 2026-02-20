@extends('layouts.desa')

@section('title', 'Detail Pembangunan - ' . $item->nama_kegiatan)

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('desa.pembangunan.index') }}"
                                class="text-decoration-none">Pembangunan</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold text-slate-800 mb-0">{{ $item->nama_kegiatan }}</h2>
                    <div class="d-flex gap-2">
                        @php
                            $statusClass = [
                                'Draft' => 'bg-slate-100 text-slate-600',
                                'Dikirim' => 'bg-blue-100 text-blue-600',
                                'Dikembalikan' => 'bg-amber-100 text-amber-600',
                                'Dicatat' => 'bg-emerald-100 text-emerald-600',
                            ][$item->status_laporan] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 fw-normal">Status:
                            {{ $item->status_laporan }}</span>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-8">
                        <!-- Identitas & Progres -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active rounded-pill px-4" id="pills-detail-tab"
                                            data-bs-toggle="pill" data-bs-target="#pills-detail" type="button" role="tab"
                                            aria-controls="pills-detail" aria-selected="true">Detail Proyek</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill px-4" id="pills-logbook-tab"
                                            data-bs-toggle="pill" data-bs-target="#pills-logbook" type="button" role="tab"
                                            aria-controls="pills-logbook" aria-selected="false">
                                            Logbook Progres
                                            <span
                                                class="badge bg-danger ms-1 text-white">{{ $item->logbooks->count() }}</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-4">
                                <div class="tab-content" id="pills-tabContent">
                                    <!-- TAB DETAIL -->
                                    <div class="tab-pane fade show active" id="pills-detail" role="tabpanel"
                                        aria-labelledby="pills-detail-tab">
                                        <h6 class="fw-bold text-slate-500 text-uppercase small mb-4">Informasi Pelaksanaan
                                        </h6>
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="small text-slate-400">Lokasi</div>
                                                <div class="fw-bold text-slate-800">{{ $item->lokasi }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-slate-400">Tahun Anggaran</div>
                                                <div class="fw-bold text-slate-800">{{ $item->tahun_anggaran }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-slate-400">Status Pengerjaan</div>
                                                <div class="fw-bold text-slate-800">{{ $item->status_kegiatan }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-slate-400">Progres Fisik</div>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <div class="progress flex-grow-1" style="height: 10px;">
                                                        @php $pct = (int) Str::replace('%', '', $item->progres_fisik); @endphp
                                                        <div class="progress-bar bg-emerald-500" role="progressbar"
                                                            style="width: {{ $pct }}%"></div>
                                                    </div>
                                                    <span class="fw-bold text-slate-800">{{ $item->progres_fisik }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB LOGBOOK -->
                                    <div class="tab-pane fade" id="pills-logbook" role="tabpanel"
                                        aria-labelledby="pills-logbook-tab">

                                        <!-- Form Input Logbook -->
                                        @if($item->status_laporan != 'Selesai')
                                            <div class="card bg-soft-light border-0 mb-4">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3">Update Progres Baru</h6>
                                                    <form action="{{ route('desa.pembangunan.logbook.store') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="pembangunan_desa_id" value="{{ $item->id }}">
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label small fw-bold">Persentase Fisik
                                                                    (%)</label>
                                                                <input type="number" name="progres_fisik" class="form-control"
                                                                    min="0" max="100" placeholder="0-100" required>
                                                            </div>
                                                            <div class="col-md-8 mb-3">
                                                                <label class="form-label small fw-bold">Foto Progres</label>
                                                                <input type="file" name="foto_progres" class="form-control"
                                                                    accept="image/*" required>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label small fw-bold">Catatan /
                                                                    Kendala</label>
                                                                <textarea name="catatan" class="form-control" rows="2"
                                                                    placeholder="Catatan perkembangan atau kendala di lapangan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm rounded-pill px-4">
                                                                <i class="fas fa-plus me-1"></i> Simpan Progres
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Timeline Logbook -->
                                        <div class="timeline-logbook">
                                            @forelse($item->logbooks as $log)
                                                <div class="d-flex gap-3 mb-4 pb-4 border-bottom position-relative">
                                                    <div class="flex-shrink-0 text-center" style="width: 60px;">
                                                        <div class="small text-muted fw-bold">
                                                            {{ $log->created_at->format('d M') }}</div>
                                                        <div class="small text-muted">{{ $log->created_at->format('Y') }}</div>
                                                        <div class="h5 mt-2 fw-bold text-primary">{{ $log->progres_fisik }}%
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        @if($log->foto_progres)
                                                            <div class="mb-2">
                                                                <img src="{{ asset('storage/' . $log->foto_progres) }}"
                                                                    class="img-fluid rounded border shadow-sm"
                                                                    style="max-height: 200px; width: auto;">
                                                            </div>
                                                        @endif
                                                        <p class="mb-1 text-dark">{{ $log->catatan }}</p>
                                                        @if($log->kendala)
                                                            <div class="alert alert-soft-warning py-2 px-3 small mt-2 mb-0">
                                                                <strong>Kendala:</strong> {{ $log->kendala }}
                                                            </div>
                                                        @endif
                                                        <div class="mt-2 text-muted small">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ $log->created_at->format('H:i') }} WIB
                                                        </div>
                                                    </div>
                                                    @if(auth()->id() == $item->created_by || auth()->user()->hasRole('Operator Desa'))
                                                        <div class="ms-2">
                                                            <form action="{{ route('desa.pembangunan.logbook.destroy', $log->id) }}"
                                                                method="POST" onsubmit="return confirm('Hapus log ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-icon btn-sm btn-soft-danger rounded-circle">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-clipboard-list fa-3x mb-3 text-slate-200"></i>
                                                    <p>Belum ada catatan progres.</p>
                                                </div>
                                            @endforelse
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Anggaran -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-500 text-uppercase small mb-4">Realisasi Keuangan</h6>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="small text-slate-400">Pagu Anggaran</div>
                                        <div class="h5 fw-bold text-slate-800 mb-0">Rp
                                            {{ number_format($item->pagu_anggaran, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-slate-400">Realisasi</div>
                                        <div class="h5 fw-bold text-emerald-600 mb-0">Rp
                                            {{ number_format($item->realisasi_anggaran, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="p-3 bg-slate-50 rounded-3">
                                            <div class="small text-slate-600 italic">
                                                <i class="fas fa-info-circle me-1"></i> Data anggaran ini bersifat faktual
                                                untuk monitoring internal Kecamatan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Dokumen -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-500 text-uppercase small mb-4">Dokumen Pendukung</h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 border-0 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-red-50 text-red-600 rounded p-2 me-3">
                                                <i class="fas fa-file-pdf fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="small fw-bold">RAB Kegiatan</div>
                                                @if($item->rab_file)
                                                    <a href="{{ asset('storage/' . $item->rab_file) }}" target="_blank"
                                                        class="small text-decoration-none">Lihat Dokumen</a>
                                                @else
                                                    <span class="small text-slate-400 italic">Belum diunggah</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 border-0 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-blue-50 text-blue-600 rounded p-2 me-3">
                                                <i class="fas fa-map-location-dot fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="small fw-bold">Gambar Rencana</div>
                                                @if($item->gambar_rencana_file)
                                                    <a href="{{ asset('storage/' . $item->gambar_rencana_file) }}"
                                                        target="_blank" class="small text-decoration-none">Lihat Dokumen</a>
                                                @else
                                                    <span class="small text-slate-400 italic">Belum diunggah</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Histori -->
                        <div class="card border-0 bg-slate-800 text-white rounded-4 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-400 text-uppercase small mb-3">Informasi Pelaporan</h6>
                                <div class="mb-3">
                                    <div class="small text-slate-400">Dibuat Pada</div>
                                    <div class="fw-medium">{{ $item->created_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div>
                                    <div class="small text-slate-400">Update Terakhir</div>
                                    <div class="fw-medium">{{ $item->updated_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5 mb-5">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-slate rounded-pill px-4">Kembali</a>
                    <div class="d-flex gap-2">
                        @if($item->status_laporan === 'Draft' || $item->status_laporan === 'Dikembalikan')
                            <a href="{{ route('desa.pembangunan.edit', $item->id) }}"
                                class="btn btn-slate-700 text-white rounded-pill px-4">Edit Data</a>
                            <form action="{{ route('desa.pembangunan.submit', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-emerald text-white rounded-pill px-4">Kirim
                                    Laporan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection