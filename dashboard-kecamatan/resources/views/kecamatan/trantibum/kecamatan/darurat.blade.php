@extends('layouts.trantibum')

@section('title', 'Pusat Nomor Darurat')

@section('content')
    <div class="container-fluid py-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h1 class="text-slate-900 fw-bold display-6">Direktori Kontak Darurat</h1>
                <p class="text-slate-500 fs-5 mb-0">Akses cepat koordinasi keamanan dan keselamatan wilayah</p>
            </div>
        </div>

        <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-5">
            <div class="card-body p-0">
                <div class="d-flex align-items-stretch">
                    <div class="bg-red-600 p-4 d-flex align-items-center justify-content-center" style="width: 100px;">
                        <i class="fas fa-triangle-exclamation fa-2x text-white"></i>
                    </div>
                    <div class="p-4 bg-white flex-grow-1">
                        <h5 class="fw-bold text-slate-900 mb-1">PROTOKOL TANGGAP DARURAT</h5>
                        <p class="mb-0 text-slate-500 small">Gunakan nomor-nomor di bawah ini hanya untuk situasi yang
                            benar-benar memerlukan penanganan segera. Pastikan Anda menyebutkan **Lokasi Kejadian** dan
                            **Jenis Kondisi** dengan jelas saat melapor.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Grid -->
        <div class="row g-4 mb-5">
            <!-- POLISI -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-premium h-100 border-0 shadow-sm hover-up overflow-hidden">
                    <div class="p-4 text-center border-bottom bg-slate-50">
                        <div class="bg-blue-100 text-blue-600 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-shield-halved fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-0">KEPOLISIAN</h5>
                        <span class="text-slate-400 small">KAMTIBMAS / KRIMINAL</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="display-5 fw-bold text-blue-700 mb-3">110</div>
                        <p class="small text-slate-500 mb-4">Layanan 24 Jam Bebas Pulsa Nasional</p>
                        <a href="tel:110" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-phone me-2"></i>Hubungi Sekarang
                        </a>
                    </div>
                </div>
            </div>

            <!-- AMBULANS -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-premium h-100 border-0 shadow-sm hover-up overflow-hidden">
                    <div class="p-4 text-center border-bottom bg-slate-50">
                        <div class="bg-red-100 text-red-600 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-ambulance fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-0">MEDIS / PSC</h5>
                        <span class="text-slate-400 small">DARURAT KESEHATAN</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="display-5 fw-bold text-red-700 mb-3">119</div>
                        <p class="small text-slate-500 mb-4">Public Safety Center (PSC) Kabupaten</p>
                        <a href="tel:119" class="btn btn-danger w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-hand-holding-medical me-2"></i>Panggil Ambulans
                        </a>
                    </div>
                </div>
            </div>

            <!-- DAMKAR -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-premium h-100 border-0 shadow-sm hover-up overflow-hidden">
                    <div class="p-4 text-center border-bottom bg-slate-50">
                        <div class="bg-amber-100 text-amber-600 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-fire-extinguisher fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-0">DAMKAR</h5>
                        <span class="text-slate-400 small">KEBAKARAN & RESCUE</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="display-5 fw-bold text-amber-700 mb-3">113</div>
                        <p class="small text-slate-500 mb-4">Pemadam Kebakaran & Penyelamatan</p>
                        <a href="tel:113" class="btn btn-warning w-100 rounded-pill py-2 shadow-sm text-white">
                            <i class="fas fa-fire me-2"></i>Lapor Kebakaran
                        </a>
                    </div>
                </div>
            </div>

            <!-- BPBD -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-premium h-100 border-0 shadow-sm hover-up overflow-hidden">
                    <div class="p-4 text-center border-bottom bg-slate-50">
                        <div class="bg-emerald-100 text-emerald-600 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-house-chimney-crack fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-0">BPBD / 112</h5>
                        <span class="text-slate-400 small">BENCANA ALAM & UMUM</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="display-5 fw-bold text-emerald-700 mb-3">112</div>
                        <p class="small text-slate-500 mb-4">Pusat Pengendali Operasi Bencana</p>
                        <a href="tel:112" class="btn btn-success w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-tower-broadcast me-2"></i>Pusdalops 112
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Local Hotline Trantibum -->
        <div class="section-header-premium mb-4">
            <h4><i class="fas fa-building-shield me-3 text-brand-600"></i>Kontak Sekretariat Trantibum & Linmas</h4>
            <div class="section-divider"></div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-premium border-0 shadow-premium p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="{{ asset('img/social/wa-business.png') }}" class="img-fluid mb-3 mb-md-0"
                                style="max-height: 80px;"
                                onerror="this.src='https://cdn-icons-png.flaticon.com/512/733/733585.png'">
                        </div>
                        <div class="col-md-7">
                            <h4 class="fw-bold text-slate-900">WhatsApp Center Trantibum Kecamatan</h4>
                            <p class="text-slate-500 mb-0">Garis koordinasi langsung dengan kasi Trantibum dan jajaran
                                Satpol PP {{ appProfile()->region_level }} {{ appProfile()->region_name }} untuk penanganan
                                gangguan ketertiban umum di wilayah desa.</p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="https://wa.me/6281234567890" target="_blank"
                                class="btn btn-wa-premium d-inline-flex px-4 py-3">
                                <i class="fab fa-whatsapp me-2 fa-lg"></i>
                                <span class="fw-bold">0812-3456-7890</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .hover-up {
            transition: all 0.3s ease;
        }

        .hover-up:hover {
            transform: translateY(-10px);
        }

        .bg-red-600 {
            background-color: #dc2626;
        }

        .text-red-700 {
            color: #b91c1c;
        }

        .bg-red-100 {
            background-color: #fee2e2;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .bg-amber-100 {
            background-color: #fef3c7;
        }

        .text-amber-600 {
            color: #d97706;
        }

        .text-amber-700 {
            color: #b45309;
        }

        .btn-wa-premium {
            background: #25d366;
            color: white;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-wa-premium:hover {
            background: #128c7e;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 211, 102, 0.4);
            transform: scale(1.05);
        }
    </style>
@endpush