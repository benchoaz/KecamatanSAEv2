@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Monitoring Pembangunan Desa</h4>
                    <p class="text-muted mb-0">Pantau progres fisik dan penyerapan anggaran pembangunan desa.</p>
                </div>
            </div>
        </div>

        <!-- Stats Review -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-brand-600 text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Total Proyek</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['total_proyek'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-hammer fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Total Anggaran</div>
                            <div class="h4 mb-0 fw-bold">Rp {{ number_format($stats['total_anggaran'], 0, ',', '.') }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-coins fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Proyek Berjalan</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['proyek_berjalan'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-spinner fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-emerald-500 text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Proyek Selesai</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['proyek_selesai'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Desa</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Anggaran</th>
                                    <th style="width: 20%;">Progres Fisik</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td><span class="fw-bold text-primary">{{ $project->desa->nama_desa }}</span></td>
                                        <td>
                                            <div class="fw-bold">{{ $project->nama_kegiatan }}</div>
                                            <div class="small text-muted">{{ $project->lokasi }}</div>
                                        </td>
                                        <td>Rp {{ number_format($project->pagu_anggaran, 0, ',', '.') }}</td>
                                        <td>
                                            @php 
                                                                                    $pct = (int) Str::replace('%', '', $project->progres_fisik);
                                                $color = 'bg-primary';
                                                if ($pct >= 100)
                                                    $color = 'bg-success';
                                                elseif ($pct > 75)
                                                    $color = 'bg-info';
                                                elseif ($pct < 25)
                                                    $color = 'bg-warning';
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $pct }}%"></div>
                                                </div>
                                                <span class="small fw-bold">{{ $project->progres_fisik }}</span>
                                            </div
                                           >    
                                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                                Update: {{ $project->logbooks->first()?->created_at?->format('d M') ?? '-' }}
                                            </div>


                                                                               </td>
                                        <td>
                                            <span class="badge bg-soft-secondary text-secondary">{{ $project->status_kegiatan }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('kecamatan.pembangunan.show', $project->id) }}" class="btn btn-sm btn-icon btn-soft-primary rounded-circle" title="Detail & Logbook">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="text-muted">Belum ada data proyek pembangunan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection