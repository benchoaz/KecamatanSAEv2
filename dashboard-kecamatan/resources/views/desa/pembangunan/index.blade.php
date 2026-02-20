@extends('layouts.desa')

@section('title', 'Pembangunan & BLT Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-slate-800">Pembangunan & BLT Desa</h2>
                <p class="text-slate-500">Monitoring kegiatan pembangunan dan penyaluran bantuan sosial desa.</p>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert bg-success-50 border-success-100 text-success-800 rounded-4 mb-5 d-flex align-items-center p-4">
            <i class="fas fa-leaf fa-2x me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">Prinsip Pendataan Faktual:</h6>
                <ul class="mb-0 small">
                    <li>Gunakan data apa adanya sesuai kondisi di lapangan.</li>
                    <li>Sistem ini bertujuan untuk pembinaan dan monitoring, bukan audit teknis.</li>
                    <li>Mohon lampirkan foto dokumentasi yang paling menggambarkan progres terkini.</li>
                </ul>
            </div>
        </div>

        <div class="row g-4">
            <!-- 1. Pembangunan Fisik -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-emerald-50 text-emerald-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-hammer fa-lg"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-slate-800">Pembangunan Fisik</h5>
                        <p class="text-slate-500 small mb-4">Pelaporan progres pembangunan infrastruktur seperti jalan,
                            jembatan, dan irigasi.</p>
                        <a href="{{ route('desa.pembangunan.fisik.index') }}"
                            class="btn btn-emerald text-white w-100 rounded-pill shadow-sm">Buka Modul</a>
                    </div>
                </div>
            </div>

            <!-- 2. Kegiatan Non-Fisik -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-amber-50 text-amber-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-people-carry-box fa-lg"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-slate-800">Kegiatan Non-Fisik</h5>
                        <p class="text-slate-500 small mb-4">Pelaporan kegiatan pemberdayaan, pelatihan, dan operasional
                            lainnya.</p>
                        <a href="{{ route('desa.pembangunan.non-fisik.index') }}"
                            class="btn btn-amber text-white w-100 rounded-pill shadow-sm">Buka Modul</a>
                    </div>
                </div>
            </div>

            <!-- 3. BLT Desa -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-sky-50 text-sky-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-hand-holding-heart fa-lg"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-slate-800">BLT Desa</h5>
                        <p class="text-slate-500 small mb-4">Monitoring penyaluran Bantuan Langsung Tunai Dana Desa bagi
                            KPM.</p>
                        <a href="{{ route('desa.blt.index') }}"
                            class="btn btn-sky text-white w-100 rounded-pill shadow-sm">Buka Modul</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-scale:hover {
            transform: translateY(-5px);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .bg-emerald-50 {
            background-color: #ecfdf5;
        }

        .text-emerald-600 {
            color: #059669;
        }

        .btn-emerald {
            background-color: #10b981;
        }

        .btn-emerald:hover {
            background-color: #059669;
        }

        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .text-amber-600 {
            color: #d97706;
        }

        .btn-amber {
            background-color: #f59e0b;
        }

        .btn-amber:hover {
            background-color: #d97706;
        }

        .bg-sky-50 {
            background-color: #f0f9ff;
        }

        .text-sky-600 {
            color: #0284c7;
        }

        .btn-sky {
            background-color: #0ea5e9;
        }

        .btn-sky:hover {
            background-color: #0284c7;
        }
    </style>
@endsection