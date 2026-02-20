@extends('layouts.desa')

@section('title', 'Bantuan Administrasi Kegiatan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row align-items-center mb-4">
            <div class="col-lg-8">
                <h2 class="fw-bold text-slate-800 mb-1">Bantuan Administrasi Kegiatan</h2>
                <p class="text-slate-500">Kumpulan template draf untuk memudahkan penatausahaan kegiatan di desa.</p>
            </div>
            @if ($activity)
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('desa.pembangunan.administrasi.index') }}"
                        class="btn btn-outline-slate rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Pilih Kegiatan Lain
                    </a>
                </div>
            @endif
        </div>

        @if (!$activity)
            <!-- Selection Mode -->
            <div class="row g-4 mb-5">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-slate-800 mb-0">Pilih Kegiatan yang Akan Dibantu</h5>
                            <p class="text-slate-500 small mb-0">Pilih salah satu draf laporan Anda untuk menyiapkan
                                administrasi otomatis.</p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="ps-4 border-0 text-slate-500 small fw-bold">NAMA KEGIATAN</th>
                                            <th class="border-0 text-slate-500 small fw-bold">JENIS</th>
                                            <th class="border-0 text-slate-500 small fw-bold">ANGGARAN</th>
                                            <th class="text-end pe-4 border-0 text-slate-500 small fw-bold">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($activities as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-slate-800">{{ $item->nama_kegiatan }}</div>
                                                    <div class="small text-slate-400">Tahun {{ $item->tahun_anggaran }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-slate-100 text-slate-600 rounded-pill px-2 py-1 small">
                                                        {{ $item->jenis_kegiatan ?? 'Fisik' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="small fw-bold text-slate-700">Rp
                                                        {{ number_format($item->pagu_anggaran, 0, ',', '.') }}</div>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('desa.pembangunan.administrasi.index', $item->id) }}"
                                                        class="btn btn-emerald text-white rounded-pill px-3 py-1 small">
                                                        Minta Bantuan <i class="fas fa-magic ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="text-slate-400 italic">Belum ada draf laporan pembangunan/kegiatan.
                                                    </div>
                                                    <a href="{{ route('desa.pembangunan.fisik.create') }}"
                                                        class="btn btn-link text-emerald mt-2">Buat Laporan Baru</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Result Mode: Checklist -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-emerald-600 text-white mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <h5 class="fw-bold mb-1">Membantu Kegiatan: {{ $activity->nama_kegiatan }}</h5>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach($activity->komponen_belanja ?? [] as $komp)
                                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2 py-1 small">
                                                <i class="fas fa-check-circle me-1"></i>{{ $komp }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <div class="small opacity-75">Sistem secara otomatis menyiapkan {{ count($checklist) }} draf
                                        dokumen.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                @foreach($checklist as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover-shadow">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="bg-emerald-50 text-emerald-600 rounded-3 p-3 d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 48px; height: 48px;">
                                    @php
                                        $icon = 'fa-file-alt';
                                        if (str_contains($item['template'], 'kwitansi'))
                                            $icon = 'fa-receipt';
                                        if (str_contains($item['template'], 'daftar-hadir'))
                                            $icon = 'fa-users';
                                    @endphp
                                    <i class="fas {{ $icon }} fa-lg"></i>
                                </div>
                                <h6 class="fw-bold text-slate-800 mb-1">{{ $item['title'] }}</h6>
                                <p class="text-slate-500 small flex-grow-1">{{ $item['description'] }}</p>

                                @if($item['template'])
                                    <button
                                        class="btn btn-outline-emerald w-100 rounded-pill mt-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-download me-2"></i>Unduh Template
                                    </button>
                                @else
                                    <div
                                        class="mt-3 p-2 rounded bg-slate-50 text-slate-500 small text-center italic border border-slate-100">
                                        Gunakan menu "Laporan" untuk upload foto.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Common Tool: Tax Assistant (SAE Style) -->
        <div class="row g-4 mb-5">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-4 bg-slate-900 text-white overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fas fa-calculator fa-lg text-emerald-400"></i>
                                    <h4 class="fw-bold mb-0">Asisten Perhitungan Pajak</h4>
                                </div>
                                <p class="text-slate-400 small mb-4">Layanan bantu hitung estimasi PPN dan PPh untuk belanja
                                    barang/jasa kegiatan desa. Alat ini bersifat edukatif untuk mempermudah bendahara.</p>

                                @if($activity)
                                    <div class="bg-slate-800 rounded-4 p-3 mb-4 border border-slate-700">
                                        <h6 class="small fw-bold text-emerald-400 mb-2">Tips Pajak untuk Kegiatan Ini:</h6>
                                        <ul class="list-unstyled mb-0 small text-slate-300">
                                            @php $foundTax = false; @endphp
                                            @foreach($activity->komponen_belanja ?? [] as $komp)
                                                @if($taxInfo = \App\Helpers\SaeHelper::getTaxInfo($komp))
                                                    <li class="mb-2 d-flex gap-2">
                                                        <i class="fas fa-info-circle mt-1"></i>
                                                        <span><strong>{{ $komp }}:</strong> {{ $taxInfo }}</span>
                                                    </li>
                                                    @php $foundTax = true; @endphp
                                                @endif
                                            @endforeach
                                            @if(!$foundTax)
                                                <li class="italic text-slate-400 text-center">Tidak ada catatan pajak khusus untuk
                                                    komponen terpilih.</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                <button class="btn btn-emerald text-white rounded-pill px-4">Buka Kalkulator Pajak</button>
                            </div>
                            <div class="col-md-5 d-none d-md-block text-end">
                                <i class="fas fa-file-invoice-dollar fa-7x opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-emerald {
            background-color: #10b981;
            border: none;
        }

        .btn-emerald:hover {
            background-color: #059669;
        }

        .text-emerald {
            color: #10b981;
        }

        .bg-emerald-50 {
            background-color: #ecfdf5;
        }

        .text-emerald-600 {
            color: #059669;
        }

        .btn-outline-emerald {
            color: #059669;
            border-color: #059669;
        }

        .btn-outline-emerald:hover {
            background-color: #059669;
            color: white;
        }

        .btn-outline-slate {
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-outline-slate:hover {
            background-color: #f8fafc;
            color: #334155;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .opacity-20 {
            opacity: 0.2;
        }
    </style>
@endsection