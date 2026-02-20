@extends('layouts.desa')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Laporan Trantibum & Bencana</h4>
                            <p class="text-muted mb-0">Daftar laporan kejadian yang telah dikirim ke Kecamatan.</p>
                        </div>
                        <a href="{{ route('desa.trantibum.kejadian.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Buat Laporan Baru
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Detail Kejadian</th>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kejadians as $index => $kejadian)
                                        <tr>
                                            <td>{{ $kejadians->firstItem() + $index }}</td>
                                            <td>
                                                @if($kejadian->kategori == 'Bencana Alam')
                                                    <span class="badge bg-danger">Bencana</span>
                                                @elseif($kejadian->kategori == 'Kriminalitas')
                                                    <span class="badge bg-dark">Kriminal</span>
                                                @elseif($kejadian->kategori == 'Ketertiban Umum')
                                                    <span class="badge bg-warning text-dark">Ketertiban</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $kejadian->kategori ?? 'Umum' }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $kejadian->jenis_kejadian }}</td>
                                            <td>
                                                {{ $kejadian->waktu_kejadian->format('d M Y') }}<br>
                                                <small class="text-muted">{{ $kejadian->waktu_kejadian->format('H:i') }}
                                                    WIB</small>
                                            </td>
                                            <td>{{ Str::limit($kejadian->lokasi_deskripsi, 30) }}</td>
                                            <td>
                                                @if($kejadian->status == 'dilaporkan')
                                                    <span class="badge bg-warning">Dilaporkan</span>
                                                @elseif($kejadian->status == 'ditangani')
                                                    <span class="badge bg-info">Ditangani</span>
                                                @elseif($kejadian->status == 'selesai')
                                                    <span class="badge bg-success">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex align-items-center list-user-action">
                                                    <a class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Lihat Detail"
                                                        href="{{ route('desa.trantibum.kejadian.show', $kejadian->id) }}">
                                                        <span class="btn-inner">
                                                            <i class="fas fa-eye text-primary"></i>
                                                        </span>
                                                    </a>
                                                    <form action="{{ route('desa.trantibum.kejadian.destroy', $kejadian->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-soft-danger"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                                            <span class="btn-inner">
                                                                <i class="fas fa-trash-alt text-danger"></i>
                                                            </span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                    <p>Belum ada laporan kejadian.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $kejadians->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection