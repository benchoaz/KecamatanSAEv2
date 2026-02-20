@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('kecamatan.pembangunan.index') }}">Monitoring
                                    Pembangunan</a></li>
                            <li class="breadcrumb-item active">{{ $project->desa->nama_desa }}</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold text-dark mb-0">{{ $project->nama_kegiatan }}</h4>
                </div>
                <a href="{{ route('kecamatan.pembangunan.index') }}" class="btn btn-white border rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase small text-muted mb-4">Informasi Dasar</h6>

                            <div class="mb-3">
                                <label class="small text-muted">Desa Pelaksana</label>
                                <div class="fw-bold">{{ $project->desa->nama_desa }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted">Lokasi</label>
                                <div class="fw-bold">{{ $project->lokasi }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted">Anggaran</label>
                                <div class="fw-bold text-success">Rp
                                    {{ number_format($project->pagu_anggaran, 0, ',', '.') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted">Status</label>
                                <span class="badge bg-soft-primary text-primary">{{ $project->status_kegiatan }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent pt-4 pb-0 border-0">
                            <h6 class="fw-bold mb-0">Riwayat Logbook Progres</h6>
                        </div>
                        <div class="card-body p-4">
                            @if($project->logbooks->count() > 0)
                                <div class="timeline-logbook">
                                    @foreach($project->logbooks as $log)
                                        <div class="d-flex gap-3 mb-4 pb-4 border-bottom position-relative">
                                            <div class="flex-shrink-0 text-center" style="width: 60px;">
                                                <div class="small text-muted fw-bold">{{ $log->created_at->format('d M') }}</div>
                                                <div class="small text-muted">{{ $log->created_at->format('Y') }}</div>
                                                <div class="h5 mt-2 fw-bold text-primary">{{ $log->progres_fisik }}%</div>
                                            </div>
                                            <div class="flex-grow-1">
                                                @if($log->foto_progres)
                                                    <div class="mb-2">
                                                        <a href="{{ asset('storage/' . $log->foto_progres) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $log->foto_progres) }}"
                                                                class="img-fluid rounded border shadow-sm"
                                                                style="max-height: 200px; width: auto;">
                                                        </a>
                                                    </div>
                                                @endif
                                                <p class="mb-1 text-dark">{{ $log->catatan }}</p>
                                                @if($log->kendala)
                                                    <div class="alert alert-soft-warning py-2 px-3 small mt-2 mb-0">
                                                        <strong>Kendala:</strong> {{ $log->kendala }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-check fa-3x mb-3 text-slate-200"></i>
                                    <p>Belum ada update logbook dari desa.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection