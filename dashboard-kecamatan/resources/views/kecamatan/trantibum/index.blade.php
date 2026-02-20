@extends('layouts.trantibum')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Dashboard Trantibum</h4>
                    <p class="text-muted mb-0">Monitoring Ketentraman dan Ketertiban Umum Kecamatan</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Total Kejadian</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['total_kejadian'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Kejadian Bulan Ini</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['kejadian_bulan_ini'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt fa-2x text-white-50"></i>
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
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Total Relawan Aktif</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['total_relawan'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-users-cog fa-2x text-white-50"></i>
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
                            <div class="small fw-bold text-white-50 text-uppercase mb-1">Desa Terdampak</div>
                            <div class="h2 mb-0 fw-bold">{{ $stats['desa_terdampak'] }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-map-marked-alt fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Tables -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0">Laporan Terbaru</h6>
                    <a href="{{ route('kecamatan.trantibum.kejadian') }}"
                        class="btn btn-sm btn-soft-primary rounded-pill">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Desa</th>
                                    <th>Kategori</th>
                                    <th>Kejadian</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_incidents as $incident)
                                    <tr>
                                        <td><span class="fw-bold">{{ $incident->desa->nama_desa }}</span></td>
                                        <td>
                                            @if($incident->kategori == 'Bencana Alam')
                                                <span class="badge bg-danger rounded-pill">Bencana</span>
                                            @elseif($incident->kategori == 'Kriminalitas')
                                                <span class="badge bg-dark rounded-pill">Kriminal</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">{{ $incident->kategori }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $incident->jenis_kejadian }}</td>
                                        <td class="small text-muted">{{ $incident->waktu_kejadian->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada laporan masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0">Statistik Kategori</h6>
                </div>
                <div class="card-body">
                    @foreach($categories as $category => $count)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <div class="small fw-bold text-dark">{{ $category }}</div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ ($count / $stats['total_kejadian']) * 100 }}%"></div>
                                </div>
                            </div>
                            <div class="ms-3 fw-bold">{{ $count }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection