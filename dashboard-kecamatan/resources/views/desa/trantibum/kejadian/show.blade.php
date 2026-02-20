@extends('layouts.desa')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card mb-4 print-hide">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('desa.trantibum.kejadian.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                        <div>
                            <button onclick="window.print()" class="btn btn-primary btn-sm">
                                <i class="fas fa-print me-1"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card paper-document">
                    <div class="card-body p-5">
                        <!-- Judul Laporan -->
                        <div class="text-center mb-5">
                            <h4 class="fw-bold text-uppercase text-decoration-underline mb-2">LAPORAN KEJADIAN / BENCANA
                            </h4>
                        </div>

                        <!-- Tujuan Surat -->
                        <div class="mb-4">
                            <p class="mb-1">Kepada Yth :</p>
                            <ol class="list-unstyled ms-3">
                                <li>1. Bapak Bupati {{ appProfile()->region_name }}</li>
                                <li>2. Bapak Wabup {{ appProfile()->region_name }}</li>
                                <li>3. Bapak Camat
                                    {{ auth()->user()->desa->kecamatan->nama_kecamatan ?? appProfile()->region_name }}</li>
                            </ol>
                        </div>

                        <p>Izin melaporkan kejadian sbb:</p>

                        <!-- Detail Kejadian -->
                        <div class="mb-4">
                            <p class="fw-bold mb-1">Jenis Kejadian Bencana:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {{ $kejadian->jenis_kejadian }}</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Waktu Kejadian:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- Hari/Tanggal: {{ $kejadian->waktu_kejadian->translatedFormat('l / d F Y') }}</li>
                                <li>- Pukul: {{ $kejadian->waktu_kejadian->format('H.i') }} WIB</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Kronologi:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {!! nl2br(e($kejadian->kronologi)) !!}</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Lokasi Terdampak:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {{ $kejadian->lokasi_deskripsi }}</li>
                                @if($kejadian->lokasi_koordinat)
                                    <li>- Koordinat: {{ $kejadian->lokasi_koordinat }}</li>
                                @endif
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Dampak Kerusakan:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {!! nl2br(e($kejadian->dampak_kerusakan ?? '-')) !!}</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Kondisi saat ini:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {!! nl2br(e($kejadian->kondisi_mutakhir ?? '-')) !!}</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Upaya Yang Dilakukan:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {!! nl2br(e($kejadian->upaya_penanganan ?? '-')) !!}</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <p class="fw-bold mb-1">Pihak/Unsur yang terlibat:</p>
                            <ul class="list-unstyled ms-3">
                                <li>- {!! nl2br(e($kejadian->pihak_terlibat ?? '-')) !!}</li>
                            </ul>
                        </div>

                        @if($kejadian->foto_kejadian)
                            <div class="mb-5 mt-4">
                                <p class="fw-bold mb-2">Dokumentasi:</p>
                                <img src="{{ asset('storage/' . $kejadian->foto_kejadian) }}" class="img-fluid border rounded"
                                    style="max-height: 400px;">
                            </div>
                        @endif

                        <div class="mb-5">
                            <p>Demikian untuk menjadikan periksa.</p>
                        </div>

                        <!-- Tanda Tangan -->
                        <div class="row mt-5">
                            <div class="col-6 offset-6 text-center">
                                <p class="mb-5">Pembuat Laporan,<br>Kepala Desa {{ auth()->user()->desa->nama_desa }}</p>
                                <br><br>
                                <p class="fw-bold text-uppercase text-decoration-underline">
                                    {{ auth()->user()->nama_lengkap }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .print-hide {
                display: none !important;
            }

            .sidebar,
            .header,
            .footer {
                display: none !important;
            }

            .content-inner {
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            body {
                background: white !important;
                font-family: 'Times New Roman', serif;
            }
        }

        .paper-document {
            font-family: 'Times New Roman', serif;
            font-size: 1.1rem;
            line-height: 1.6;
        }
    </style>
@endsection