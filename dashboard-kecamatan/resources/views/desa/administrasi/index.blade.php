@extends('layouts.desa')

@section('title', 'Administrasi Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-slate-800">Administrasi Desa</h2>
                <p class="text-slate-500">Pusat Input & Arsip Data Pemerintahan Desa.</p>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert bg-primary-50 border-primary-100 text-primary-800 rounded-4 mb-5 d-flex align-items-center p-4">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">Prinsip Pengelolaan Data:</h6>
                <ul class="mb-0 small">
                    <li>Data yang Anda input bersifat <strong>Draft</strong> sebelum dikirim.</li>
                    <li>Setelah status <strong>Dikirim</strong>, data tidak dapat diubah kecuali dikembalikan oleh
                        Kecamatan.</li>
                    <li>Kecamatan bertindak sebagai pengawas dan verifikator data.</li>
                </ul>
            </div>
        </div>

        <div class="row g-4">
            <!-- 1. Perangkat Desa -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-blue-50 text-blue-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-user-tie fa-lg"></i>
                            </div>
                            @if($counts['perangkat']['draft'] > 0 || $counts['perangkat']['revisi'] > 0)
                                <span class="badge bg-warning text-dark rounded-pill h-auto py-2 px-3">
                                    {{ $counts['perangkat']['draft'] + $counts['perangkat']['revisi'] }} Action Needed
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold text-slate-800">Perangkat Desa</h5>
                        <p class="text-slate-500 small mb-4">Input data Kepala Desa, Sekretaris, dan perangkat lainnya
                            beserta SK.</p>
                        <a href="{{ route('desa.administrasi.personil.index', ['kategori' => 'perangkat']) }}"
                            class="btn btn-primary w-100 rounded-pill">Kelola Data</a>
                    </div>
                </div>
            </div>

            <!-- 2. BPD -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-indigo-50 text-indigo-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-users-rectangle fa-lg"></i>
                            </div>
                            @if($counts['bpd']['draft'] > 0)
                                <span class="badge bg-warning text-dark rounded-pill h-auto py-2 px-3">
                                    {{ $counts['bpd']['draft'] }} Draft
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold text-slate-800">Badan Permusyawaratan</h5>
                        <p class="text-slate-500 small mb-4">Arsip data anggota BPD, SK pengangkatan, dan masa jabatan.</p>
                        <a href="{{ route('desa.administrasi.personil.index', ['kategori' => 'bpd']) }}"
                            class="btn btn-indigo text-white w-100 rounded-pill">Kelola Data</a>
                    </div>
                </div>
            </div>

            <!-- 3. Lembaga Desa -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-purple-50 text-purple-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-sitemap fa-lg"></i>
                            </div>
                            @if($counts['lembaga']['draft'] > 0)
                                <span class="badge bg-warning text-dark rounded-pill h-auto py-2 px-3">
                                    {{ $counts['lembaga']['draft'] }} Draft
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold text-slate-800">Lembaga Desa</h5>
                        <p class="text-slate-500 small mb-4">Data LPM, PKK, Karang Taruna, dan lembaga kemasyarakatan
                            lainnya.</p>
                        <a href="{{ route('desa.administrasi.lembaga.index') }}"
                            class="btn btn-purple text-white w-100 rounded-pill">Kelola Lembaga</a>
                    </div>
                </div>
            </div>

            <!-- 4. Laporan (LKPJ/LPPD) -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-teal-50 text-teal-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-file-contract fa-lg"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-slate-800">Laporan Tahunan</h5>
                        <p class="text-slate-500 small mb-4">Arsip dokumen LKPJ, LPPD, dan APBDes desa sesuai tahun
                            anggaran.</p>
                        <a href="{{ route('desa.administrasi.dokumen.index', ['tipe' => 'laporan']) }}"
                            class="btn btn-teal text-white w-100 rounded-pill">Arsip Laporan</a>
                    </div>
                </div>
            </div>

            <!-- 5. Peraturan Desa (Perdes) -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-scale transition-all group">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-orange-50 text-orange-600 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px;">
                                <i class="fas fa-gavel fa-lg"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-slate-800">Peraturan Desa (Perdes)</h5>
                        <p class="text-slate-500 small mb-4">Input & Arsip Peraturan Desa dan Peraturan Kepala Desa.</p>
                        <a href="{{ route('desa.administrasi.dokumen.index', ['tipe' => 'perdes']) }}"
                            class="btn btn-orange text-white w-100 rounded-pill">Kelola Perdes</a>
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

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .bg-indigo-50 {
            background-color: #eef2ff;
        }

        .text-indigo-600 {
            color: #4f46e5;
        }

        .btn-indigo {
            background-color: #4f46e5;
        }

        .btn-indigo:hover {
            background-color: #4338ca;
        }

        .bg-purple-50 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #9333ea;
        }

        .btn-purple {
            background-color: #9333ea;
        }

        .bg-teal-50 {
            background-color: #ccfbf1;
        }

        .text-teal-600 {
            color: #0d9488;
        }

        .btn-teal {
            background-color: #0d9488;
        }

        .bg-orange-50 {
            background-color: #fff7ed;
        }

        .text-orange-600 {
            color: #ea580c;
        }

        .btn-orange {
            background-color: #ea580c;
        }

        .btn-orange:hover {
            background-color: #c2410c;
        }
    </style>
@endsection