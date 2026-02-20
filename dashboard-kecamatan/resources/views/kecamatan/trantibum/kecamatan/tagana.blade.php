@extends('layouts.trantibum')

@section('title', 'Data TAGANA Desa')

@section('content')
    <div class="content-header mb-4">
        <div class="header-title">
            <h1 class="text-slate-900 fw-bold display-6">Direktori TAGANA Desa</h1>
            <p class="text-slate-500 fs-5 mb-0">Data Kontak Person Taruna Siaga Bencana di Seluruh Wilayah</p>
            <div class="header-accent"></div>
        </div>
    </div>

    <!-- Alert Info (Sesuai Request User) -->
    <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="d-flex align-items-stretch">
                <div class="bg-brand-600 p-4 d-flex align-items-center justify-content-center" style="width: 100px;">
                    <i class="fas fa-tower-broadcast fa-2x text-white"></i>
                </div>
                <div class="p-4 bg-white flex-grow-1">
                    <h5 class="fw-bold text-slate-900 mb-1">Pusat Koordinasi TAGANA Wilayah</h5>
                    <p class="mb-0 text-slate-500">Direktori ini digunakan untuk mempercepat response time penanganan
                        bencana dengan menyediakan akses langsung ke kontak person lapangan di tiap desa.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section-header-premium mb-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <h4><i class="fas fa-list-check me-3 text-brand-600"></i>Daftar Kontak TAGANA Desa</h4>
            <span class="badge bg-slate-100 text-slate-600 px-3 py-2 rounded-pill small fw-bold">
                {{ count($reports) }} Desa Terdata
            </span>
        </div>
        <div class="section-divider"></div>
    </div>

    <div class="card card-premium overflow-hidden border-0 shadow-premium">
        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="ps-4 py-3">ASAL DESA</th>
                        <th class="py-3">KONTAK UTAMA</th>
                        <th class="py-3">SALURAN WA</th>
                        <th class="text-center py-3">LAST UPDATE</th>
                        <th class="text-end pe-4 py-3">DETAIL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        @php
                            $hpIndikator = $report->jawabanIndikator->firstWhere('indikator.kode_indikator', 'ind_tagana_hp');
                            $hpValue = $hpIndikator ? $hpIndikator->jawaban : 'Belum Diisi';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-brand-50 text-brand-600 rounded-circle p-2 me-3 fw-bold"
                                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        {{ substr($report->desa->nama_desa, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800">Desa {{ $report->desa->nama_desa }}</div>
                                        <div class="text-slate-400 small">Region {{ appProfile()->region_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-slate-900 fw-bold">Petugas TAGANA</div>
                                <div class="small text-emerald-600 fw-medium">
                                    <i class="fas fa-circle-check me-1 small"></i> Terverifikasi
                                </div>
                            </td>
                            <td>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $hpValue) }}" target="_blank"
                                    class="text-decoration-none">
                                    <div class="btn-wa-premium">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        <span>{{ $hpValue }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="text-slate-600 small fw-bold">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div class="text-slate-400" style="font-size: 0.75rem;">
                                    {{ $report->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('kecamatan.trantibum.show', $report->id) }}" class="btn-action-circle">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state-premium py-4">
                                    <div class="icon-box-empty mx-auto mb-4">
                                        <i class="fas fa-phone-slash shadow-lg"></i>
                                    </div>
                                    <h5 class="fw-bold text-slate-800 mb-2">Belum Ada Data Kontak</h5>
                                    <p class="text-slate-500 small mx-auto" style="max-width: 300px;">Data nomor HP TAGANA akan
                                        muncul di sini setelah desa melakukan sinkronisasi data Trantibum.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .btn-wa-premium {
            display: inline-flex;
            align-items: center;
            background: #f0fdf4;
            color: #16a34a;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            border: 1px solid #dcfce7;
            transition: all 0.3s ease;
        }

        .btn-wa-premium:hover {
            background: #dcfce7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
            color: #15803d;
        }

        .btn-action-circle {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .btn-action-circle:hover {
            background: var(--brand-600);
            color: white;
            border-color: var(--brand-600);
            transform: scale(1.1);
        }

        .icon-box-empty {
            width: 80px;
            height: 80px;
            background: #f8fafc;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #cbd5e1;
            transform: rotate(-10deg);
        }
    </style>
@endpush