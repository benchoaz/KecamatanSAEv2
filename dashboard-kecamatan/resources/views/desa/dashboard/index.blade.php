@extends('layouts.desa')

@section('title', 'Dashboard Desa')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header with Year Filter -->
        <div class="row mb-5 align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-slate-800 mb-1">Informasi Desa</h2>
                <p class="text-slate-500 fs-5 mb-0">Gambaran umum data dan status administrasi desa tahun {{ $tahun }}.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <form action="{{ route('desa.dashboard') }}" method="GET" class="d-inline-block">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden">
                        <span class="input-group-text bg-white border-end-0 ps-3 text-secondary"><i
                                class="fas fa-calendar-alt"></i></span>
                        <select name="tahun"
                            class="form-select border-start-0 ps-2 pe-5 py-2 fw-semibold text-slate-700 focus-ring-none"
                            style="min-width: 120px;" onchange="this.form.submit()">
                            @php
                                $currentYear = date('Y');
                                $years = range($currentYear - 1, $currentYear + 2); // Show Previous 1 year to Next 2 years
                            @endphp
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- 1. Statistik Personil (Cards) -->
        <div class="row g-4 mb-5">
            <!-- Perangkat Desa -->
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden group hover-scale transition-all">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div
                                class="bg-primary-50 rounded-4 p-3 d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-user-tie fa-2x text-primary-600"></i>
                            </div>
                            <span class="badge bg-primary-50 text-primary-600 rounded-pill px-3 py-2">Data Personil</span>
                        </div>
                        <h1 class="display-4 fw-bold text-slate-800 mb-1">{{ $stats['perangkat'] }}</h1>
                        <p class="text-slate-500 fw-medium">Total Perangkat Desa</p>
                        <a href="{{ route('desa.pemerintahan.detail.personil.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <!-- Anggota BPD -->
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden group hover-scale transition-all">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-brand-50 rounded-4 p-3 d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-users-rectangle fa-2x text-brand-600"></i>
                            </div>
                            <span class="badge bg-brand-50 text-brand-600 rounded-pill px-3 py-2">Badan
                                Permusyawaratan</span>
                        </div>
                        <h1 class="display-4 fw-bold text-slate-800 mb-1">{{ $stats['bpd'] }}</h1>
                        <p class="text-slate-500 fw-medium">Total Anggota BPD</p>
                        <a href="{{ route('desa.pemerintahan.detail.bpd.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <!-- Lembaga Desa -->
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden group hover-scale transition-all">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-purple-50 rounded-4 p-3 d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-sitemap fa-2x text-purple-600"></i>
                            </div>
                            <span class="badge bg-purple-50 text-purple-600 rounded-pill px-3 py-2">Kelembagaan</span>
                        </div>
                        <h1 class="display-4 fw-bold text-slate-800 mb-1">{{ $stats['lembaga'] }}</h1>
                        <p class="text-slate-500 fw-medium">Total Lembaga Desa</p>
                        <a href="{{ route('desa.pemerintahan.detail.lembaga.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Ringkasan Anggaran & Penyerapan (New) -->
        <div class="row mb-5">
            <div class="col-12">
                <div
                    class="card border-0 shadow-sm rounded-4 overflow-hidden bg-slate-900 border-start border-4 border-emerald-500">
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-center">
                            <div class="col-lg-7 text-white">
                                <h4 class="fw-bold mb-3">Penyerapan Anggaran {{ $tahun }}</h4>
                                <p class="text-slate-400 mb-4 mb-lg-0">Monitoring penyerapan dana berdasarkan hasil input
                                    kegiatan fisik & BLT dibandingkan dengan Pagu Awal yang Anda tetapkan.</p>

                                <div class="row g-4 mt-2">
                                    <div class="col-md-6">
                                        <div class="small text-slate-500 fw-bold text-uppercase tracking-wider">Total Pagu
                                            Desa (Awal)</div>
                                        <div class="h3 fw-bold mb-0">Rp
                                            {{ number_format($pembangunan['total_pagu'], 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-slate-500 fw-bold text-uppercase tracking-wider">Realisasi
                                            Terserap</div>
                                        <div class="h3 fw-bold mb-0 text-emerald-400">Rp
                                            {{ number_format($pembangunan['total_realisasi'], 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                                <div class="position-relative d-inline-block">
                                    <div class="progress-circle mx-auto" style="--value: {{ $pembangunan['persentase'] }}">
                                        <span class="h2 fw-bold text-white mb-0">{{ $pembangunan['persentase'] }}%</span>
                                        <span class="small d-block text-slate-400">Terserap</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Status Perencanaan (Info Chart/Table) -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-4 px-4">
                        <h5 class="fw-bold text-slate-800 mb-0">Status Dokumen Perencanaan {{ $tahun }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-slate-50 text-slate-600 small fw-bold text-uppercase">
                                    <tr>
                                        <th class="py-3 px-4">Jenis Dokumen</th>
                                        <th class="py-3 px-4">Keterangan</th>
                                        <th class="py-3 px-4 text-end">Status Terkini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($perencanaan as $key => $status)
                                        @php
                                            $badgeClass = match ($status) {
                                                'Selesai' => 'bg-success-subtle text-success',
                                                'Verifikasi', 'Dikirim' => 'bg-primary-subtle text-primary',
                                                'Revisi', 'Dikembalikan' => 'bg-danger-subtle text-danger',
                                                'Draft' => 'bg-warning-subtle text-warning-900',
                                                'Lengkap' => 'bg-info-subtle text-info',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };

                                            $label = [
                                                'musdes' => 'Musyawarah Desa (Musdes)',
                                                'rpjmdes' => 'RPJM Desa',
                                                'rkpdes' => 'RKP Desa Th. ' . $tahun,
                                                'apbdes' => 'APB Desa Th. ' . $tahun
                                            ][$key] ?? $key;

                                            $desc = [
                                                'musdes' => 'Berita Acara & Dokumentasi Pelaksanaan Musdes',
                                                'rpjmdes' => 'Rencana Pembangunan Jangka Menengah (6 Tahun)',
                                                'rkpdes' => 'Rencana Kerja Pemerintah Desa Tahunan',
                                                'apbdes' => 'Anggaran Pendapatan dan Belanja Desa'
                                            ][$key] ?? '';
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 fw-semibold text-slate-700">{{ $label }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $desc }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 fw-medium">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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

        .bg-purple-50 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #9333ea;
        }

        .bg-success-subtle {
            background-color: #d1fae5;
        }

        .bg-primary-subtle {
            background-color: #e0f2fe;
        }

        .bg-warning-subtle {
            background-color: #fef3c7;
        }

        .bg-danger-subtle {
            background-color: #fee2e2;
        }

        .bg-info-subtle {
            background-color: #cffafe;
        }

        .bg-secondary-subtle {
            background-color: #f1f5f9;
        }

        /* Progress Circle */
        .progress-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(closest-side, #0f172a 79%, transparent 80% 100%), conic-gradient(#10b981 calc(var(--value) * 1%), #334155 0);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .text-emerald-400 {
            color: #34d399;
        }

        .bg-slate-900 {
            background-color: #0f172a;
        }

        .bg-slate-800 {
            background-color: #1e293b;
        }
    </style>
@endsection