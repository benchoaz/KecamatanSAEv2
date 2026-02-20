@extends('layouts.kecamatan')

@section('title', 'Rekomendasi ke Camat')

@section('content')
    <div class="content-header mb-4">
        <div class="header-breadcrumb">
            <a href="{{ route('kesra.index') }}" class="text-teal-600"><i class="fas fa-arrow-left"></i> Kembali ke
                Dashboard</a>
        </div>
        <div class="header-title">
            <h1>Rekomendasi ke Camat</h1>
            <p class="text-muted">Daftar laporan Bidang Kesra yang telah ditelaah dan direkomendasikan untuk persetujuan
                Camat.</p>
        </div>
    </div>

    <div class="card bg-white border-gray-200 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4">Asal Desa</th>
                        <th>Program / Aspek</th>
                        <th>Penelaah</th>
                        <th>Tanggal Rekomendasi</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-gray-800">{{ $s->desa->nama_desa }}</div>
                                <div class="extra-small text-muted">ID: #SUB-{{ $s->id }}</div>
                            </td>
                            <td>{{ $s->aspek->nama_aspek }}</td>
                            <td>{{ $s->reviewedBy->name ?? 'System' }}</td>
                            <td>{{ $s->reviewed_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('submissions.show', $s->id) }}" class="btn btn-sm btn-icon"><i
                                        class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada laporan yang direkomendasikan ke
                                Camat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
@endpush