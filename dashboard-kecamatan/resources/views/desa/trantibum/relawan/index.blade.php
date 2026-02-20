@extends('layouts.desa')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Tim Relawan Tangguh Bencana</h4>
                            <p class="text-muted mb-0">Daftar anggota relawan aktif desa.</p>
                        </div>
                        <a href="{{ route('desa.trantibum.relawan.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i> Tambah Anggota
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row g-4">
                            @forelse($relawans as $relawan)
                                <div class="col-md-6 col-lg-4 col-xl-3">
                                    <div class="card h-100 border shadow-sm text-center pt-4">
                                        <div class="mx-auto" style="width: 100px; height: 100px;">
                                            <img src="{{ $relawan->foto ? asset('storage/' . $relawan->foto) : asset('assets/images/avatars/01.png') }}"
                                                class="img-fluid rounded-circle border border-3 border-white shadow"
                                                style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title mb-1 fw-bold">{{ $relawan->nama }}</h5>
                                            <p class="text-primary mb-2 fw-bold small text-uppercase">{{ $relawan->jabatan }}
                                            </p>

                                            <div class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3">
                                                {{ $relawan->nik }}
                                            </div>

                                            <div class="d-flex justify-content-center gap-2 mt-3">
                                                @if($relawan->sk_file)
                                                    <a href="{{ asset('storage/' . $relawan->sk_file) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-danger rounded-pill" title="Lihat SK">
                                                        <i class="fas fa-file-pdf me-1"></i> SK
                                                    </a>
                                                @endif
                                                <form action="{{ route('desa.trantibum.relawan.destroy', $relawan->id) }}"
                                                    method="POST" onsubmit="return confirm('Hapus anggota ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 text-slate-200"></i>
                                        <p>Belum ada data relawan.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                            {{ $relawans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection