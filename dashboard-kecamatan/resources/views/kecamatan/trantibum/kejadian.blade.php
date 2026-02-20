@extends('layouts.trantibum')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Data Laporan Kejadian</h4>
                    <p class="text-muted mb-0">Arsip seluruh laporan trantibum & bencana dari desa.</p>
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
                                    <th>Tanggal</th>
                                    <th>Desa</th>
                                    <th>Kategori</th>
                                    <th>Jenis Kejadian</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kejadians as $kejadian)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $kejadian->waktu_kejadian->format('d/m/Y') }}</div>
                                            <div class="small text-muted">{{ $kejadian->waktu_kejadian->format('H:i') }} WIB
                                            </div>
                                        </td>
                                        <td>{{ $kejadian->desa->nama_desa }}</td>
                                        <td>
                                            @if($kejadian->kategori == 'Bencana Alam')
                                                <span class="badge bg-danger">Bencana</span>
                                            @elseif($kejadian->kategori == 'Kriminalitas')
                                                <span class="badge bg-dark">Kriminal</span>
                                            @elseif($kejadian->kategori == 'Ketertiban Umum')
                                                <span class="badge bg-warning text-dark">Ketertiban</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $kejadian->kategori }}</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $kejadian->jenis_kejadian }}</td>
                                        <td class="text-wrap" style="max-width: 200px;">
                                            {{ Str::limit($kejadian->lokasi_deskripsi, 50) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-success text-success">Terlapor</span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-icon btn-soft-primary rounded-circle"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada data kejadian ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $kejadians->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection