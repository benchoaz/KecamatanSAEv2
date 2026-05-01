<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <script>
        // Force HTTPS to prevent POST redirect data loss on mobile HTTP connections
        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            location.replace(`https:${location.href.substring(location.protocol.length)}`);
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO Meta Tags --}}
    <title>{{ appProfile()->full_region_name }}
        {{ $appProfile->region_parent ?? 'Kabupaten Probolinggo' }} – Layanan & Informasi Publik
    </title>
    <meta name="description"
        content="Website resmi {{ appProfile()->full_region_name }} yang menyediakan informasi layanan pemerintahan, berita kecamatan, peta desa, serta etalase UMKM warga.">
    <meta name="keywords"
        content="{{ appProfile()->full_region_name }}, layanan kecamatan, desa {{ appProfile()->region_name }}, UMKM {{ appProfile()->region_name }}, kantor kecamatan {{ appProfile()->region_name }}, pelayanan publik">
    <meta name="author" content="Pemerintah {{ appProfile()->full_region_name }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ appProfile()->full_region_name }} – Layanan & Informasi Publik">
    <meta property="og:description"
        content="Website resmi {{ appProfile()->full_region_name }} yang menyediakan informasi layanan pemerintahan, berita kecamatan, peta desa, serta etalase UMKM warga.">
    @if(appProfile()->logo_path)
        <meta property="og:image" content="{{ asset('storage/' . appProfile()->logo_path) }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ appProfile()->full_region_name }}">
    <meta name="twitter:description"
        content="Website resmi {{ appProfile()->full_region_name }} untuk layanan publik dan informasi warga.">

    {{-- Geo Tags for Local SEO --}}
    <meta name="geo.region" content="ID-JI">
    <meta name="geo.placename" content="{{ appProfile()->full_region_name }}">
    <meta name="geo.position"
        content="{{ appProfile()->map_latitude ?? -7.8 }};{{ appProfile()->map_longitude ?? 113.3 }}">
    <meta name="ICBM" content="{{ appProfile()->map_latitude ?? -7.8 }}, {{ appProfile()->map_longitude ?? 113.3 }}">

    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Tailwind CSS + DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts - Poppins (lebih mirip referensi) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Swiper.js (Modern Slider) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Fix: Ensure page is always visible on initial load */
        html[data-theme="dark"],
        html[data-theme="high-contrast"] {
            --fallback-b1: #ffffff;
            --fallback-b2: #f8fafc;
            --fallback-b3: #f1f5f9;
            --fallback-bc: #1e293b;
            --fallback-p: #0d9488;
            --fallback-pc: #ffffff;
            --fallback-s: #0f766e;
            --fallback-sc: #ffffff;
            --fallback-a: #f59e0b;
            --fallback-ac: #1e293b;
            --fallback-n: #1e293b;
            --fallback-nc: #ffffff;
        }

        /* Force light theme on landing page */
        html[data-theme="dark"] body,
        html[data-theme="high-contrast"] body {
            background-color: #f8fafc !important;
            color: #1e293b !important;
        }

        /* Prevent modal backdrop from blocking if no modal is open */
        dialog:not([open])::backdrop {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        /* Force hide all modal backdrops when no modal is open */
        .modal-backdrop,
        [class*="modal-"][class*="backdrop"] {
            display: none !important;
        }

        /* Ensure body is always interactive */
        body {
            pointer-events: auto !important;
        }

        a,
        a:hover,
        a:focus,
        a:active,
        .underline,
        .hover\:underline,
        .menu a,
        .navbar a,
        .navbar span,
        .navbar div {
            text-decoration: none !important;
            border-bottom-width: 0 !important;
            border-bottom-style: none !important;
            box-shadow: none !important;
        }

        * {
            text-decoration: none !important;
        }
    </style>

    {{-- JSON-LD Structured Data for Local SEO --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "GovernmentOrganization",
      "name": "{{ appProfile()->region_level }} {{ appProfile()->region_name }}",
      "description": "Pemerintah {{ appProfile()->region_level }} {{ appProfile()->region_name }} - Layanan Administrasi dan Informasi Publik",
      "url": "{{ url('/') }}",
      "telephone": "{{ appProfile()->phone ?? '+62-XXXXXXX' }}",
      "email": "{{ appProfile()->email ?? 'kontak@domain.go.id' }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ appProfile()->address ?? 'Alamat Kantor' }}",
        "addressLocality": "{{ appProfile()->region_name }}",
        "addressRegion": "Jawa Timur",
        "postalCode": "67219",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{{ appProfile()->map_latitude ?? -7.8 }}",
        "longitude": "{{ appProfile()->map_longitude ?? 113.3 }}"
      },
      "areaServed": {
        "@type": "AdministrativeArea",
        "name": "{{ appProfile()->region_level }} {{ appProfile()->region_name }}"
      },
      "sameAs": [
        "https://probolinggokab.go.id"
      ]
    }
    </script>
    <link rel="stylesheet" href="{{ asset('css/min/common-map.min.css') }}">
</head>

<body class="bg-slate-50">

    @include('layouts.partials.public.navbar')
    @include('layouts.partials.public.announcements')

    <!-- Hero Section Styles -->
    <style>
        .hero-swiper {
            width: 100%;
            height: 75vh;
            min-height: 550px;
        }

        .hero-slide {
            position: relative;
            overflow: hidden;
            background: white;
        }

        .slide-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            z-index: 10;
        }

        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.8) 30%, rgba(255, 255, 255, 0.2) 60%, transparent 100%);
            z-index: 15;
        }

        .hero-content {
            position: relative;
            z-index: 20;
            height: 100%;
            display: flex;
            align-items: center;
            padding-top: 8rem; /* Increased for mobile to clear info banner */
            padding-bottom: 3rem;
        }

        @media (min-width: 1024px) {
            .hero-content {
                padding-top: 2.5rem; /* Desktop padding adjustment */
                padding-bottom: 0;
            }
        }

        .text-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .swiper-slide-active .text-reveal {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }

        /* Custom Swiper Navigation */
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 20px !important;
            font-weight: 900;
        }
        .swiper-button-next, .swiper-button-prev {
            width: 50px !important;
            height: 50px !important;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            border-radius: 50%;
            color: #0d9488 !important;
            transition: all 0.3s;
        }
        .swiper-pagination-bullet-active {
            background: #0d9488 !important;
        }
    </style>

    <!-- Dynamic Hero Swiper -->
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            
            <!-- Slide 1: Welcome & WhatsApp Call Center -->
            <div class="swiper-slide hero-slide">
                <div class="slide-bg opacity-85" style="background-image: url('{{ $heroBg ?? 'https://images.unsplash.com/photo-1596328330776-6d9b4b0e503b?q=80&w=1600' }}');"></div>
                <div class="slide-overlay"></div>
                
                <div class="container mx-auto px-6 hero-content">
                    <div class="flex flex-col lg:flex-row items-center w-full gap-12">
                        <!-- Left: Content -->
                        <div class="w-full lg:w-3/5 text-left">
                            <div class="text-reveal delay-100 inline-flex items-center gap-2 bg-[#dcfce7] text-[#166534] px-4 py-1.5 rounded-full mb-6 shadow-sm border border-emerald-100">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                <span class="text-xs font-bold uppercase tracking-wide">Ngobrol SAE Bareng {{ appProfile()->region_level }}</span>
                            </div>
                            
                            <h1 class="text-reveal delay-200 text-5xl md:text-7xl font-black text-[#1e293b] mb-6 leading-[1.1] tracking-tight">
                                Solusi Cepat<br>
                                Urusan <span class="text-[#0f766e]">Layanan Publik</span>
                            </h1>
                            
                            <p class="text-reveal delay-300 text-lg md:text-xl text-[#475569] mb-10 leading-relaxed font-medium max-w-xl">
                                Akses berbagai layanan publik secara digital, cepat, dan transparan. Mudahkan urusan administrasi Anda dari mana saja.
                            </p>

                            <div class="text-reveal delay-400">
                                <a href="#layanan" 
                                   class="btn bg-[#0f766e] hover:bg-[#115e59] text-white border-0 rounded-2xl px-12 h-16 font-black shadow-xl shadow-slate-200 transition-all flex items-center gap-2">
                                    Mulai Layanan Digital <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Right: Visual Balance (Regional Leader Photo) -->
                        <div class="w-full lg:w-2/5 flex justify-center lg:justify-end relative order-first lg:order-last mb-6 lg:mb-0">
                            <div class="text-reveal delay-300 relative group scale-[0.8] sm:scale-90 md:scale-95 lg:scale-100">
                                <div class="absolute -inset-10 bg-emerald-100 rounded-full blur-3xl opacity-50 animate-pulse"></div>
                                <div class="relative bg-white/20 backdrop-blur-md p-2.5 md:p-4 rounded-[2.5rem] md:rounded-[3.5rem] border border-white/50 shadow-2xl transition-all duration-700 hover:rotate-2">
                                    <div class="aspect-[4/5] w-[160px] sm:w-[240px] md:w-[280px] lg:w-[320px] rounded-[2rem] md:rounded-[3rem] overflow-hidden border-4 border-white shadow-inner bg-slate-100">
                                        @if($appProfile->hero_image_path)
                                            <img src="{{ asset('storage/' . $appProfile->hero_image_path) }}" 
                                                 alt="{{ $appProfile->hero_image_alt ?? 'Pimpinan' }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-user-circle text-[8rem] md:text-[10rem]"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Leader Floating Badge -->
                                    @if($appProfile->leader_name)
                                    <div class="absolute -bottom-6 -left-4 md:-left-6 bg-white p-4 md:p-5 rounded-[1.5rem] md:rounded-3xl shadow-xl border border-emerald-50 min-w-[200px] md:min-w-[240px] transform -rotate-3 group-hover:rotate-0 transition-transform">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1 h-8 md:w-1.5 md:h-10 bg-emerald-500 rounded-full"></div>
                                            <div>
                                                <h4 class="text-slate-900 font-black text-xs md:text-sm leading-tight">{{ $appProfile->leader_name }}</h4>
                                                <p class="text-[8px] md:text-[10px] text-emerald-600 font-bold uppercase tracking-widest mt-1">
                                                    {{ $appProfile->leader_title ?? 'Pimpinan Wilayah' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Digital Public Services -->
            <div class="swiper-slide hero-slide">
                <div class="slide-bg opacity-70" style="background-image: url('{{ appProfile()->image_festival ? asset('storage/' . appProfile()->image_festival) : 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?q=80&w=1600' }}');"></div>
                <div class="slide-overlay"></div>
                
                <div class="container mx-auto px-6 hero-content">
                    <div class="flex flex-col lg:flex-row items-center w-full gap-12">
                        <!-- Left: Content -->
                        <div class="w-full lg:w-3/5 text-left">
                            <div class="text-reveal delay-100 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full mb-6 shadow-sm border border-blue-100">
                                <span class="w-2 h-2 bg-blue-500 rounded-full shadow-sm"></span>
                                <span class="text-xs font-bold uppercase tracking-wide">Pelayanan Terpadu & Transparan</span>
                            </div>
                            
                            <h1 class="text-reveal delay-200 text-5xl md:text-7xl font-black text-[#1e293b] mb-6 leading-[1.1] tracking-tight">
                                Kemudahan<br>
                                <span class="text-[#0369a1]">Administrasi Kita</span>
                            </h1>
                            
                            <p class="text-reveal delay-300 text-lg md:text-xl text-[#475569] mb-10 leading-relaxed font-medium max-w-xl">
                                Nikmati kemudahan mengajukan berkas secara online dari mana saja. Transparan, akuntabel, dan bebas ribet.
                            </p>

                            <div class="text-reveal delay-400 flex flex-wrap gap-4">
                                <a href="#layanan" 
                                   class="btn bg-[#0369a1] hover:bg-[#075985] text-white border-0 rounded-2xl px-12 h-16 font-black shadow-xl shadow-blue-200 transition-all">
                                    Lihat Jenis Layanan <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Right: Visual Balance -->
                        <div class="hidden lg:flex w-2/5 justify-end relative">
                            <div class="text-reveal delay-300 relative">
                                <div class="absolute -inset-10 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
                                <div class="relative bg-white/20 backdrop-blur-sm p-12 rounded-[3rem] border border-white/30 shadow-2xl -rotate-3 hover:rotate-0 transition-all duration-700">
                                    <i class="fas fa-file-invoice text-blue-600 text-[150px] drop-shadow-2xl"></i>
                                    <div class="absolute -bottom-4 -left-4 bg-white p-4 rounded-2xl shadow-xl border border-blue-50">
                                        <i class="fas fa-check-circle text-emerald-500 text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(appProfile()->is_menu_statistik_active)
            <!-- Slide 3: Regional Potential -->
            <div class="swiper-slide hero-slide">
                <!-- Statistic Background -->
                <div class="slide-bg opacity-70" style="background-image: url('{{ asset('assets/images/hero_statistics_bg.png') }}');"></div>
                <div class="slide-overlay"></div>
                
                <div class="container mx-auto px-6 hero-content">
                    <div class="flex flex-col lg:flex-row items-center w-full gap-12">
                        <!-- Left: Content -->
                        <div class="w-full lg:w-3/5 text-left">
                            <div class="text-reveal delay-100 inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full mb-6 shadow-sm border border-blue-100">
                                <span class="w-2 h-2 bg-blue-500 rounded-full shadow-sm"></span>
                                <span class="text-xs font-bold uppercase tracking-wide">Laporan & Statistik Publik</span>
                            </div>
                            
                            <h1 class="text-reveal delay-200 text-5xl md:text-7xl font-black text-[#1e293b] mb-6 leading-[1.1] tracking-tight">
                                Statistik Terpadu<br>
                                {{ appProfile()->region_name }}
                            </h1>
                            
                            <p class="text-reveal delay-300 text-lg md:text-xl text-[#475569] mb-10 leading-relaxed font-medium max-w-xl">
                                Pantau perkembangan wilayah melalui data kependudukan, kesehatan, dan ekonomi yang akurat dan transparan dari 17 desa.
                            </p>

                            <div class="text-reveal delay-400 flex flex-wrap gap-4">
                                <a href="{{ route('landing.statistik.index') }}" 
                                   class="btn bg-amber-600 hover:bg-amber-700 text-white border-0 rounded-2xl px-12 h-16 font-black shadow-xl shadow-amber-200 transition-all">
                                    Data Statistik <i class="fas fa-chart-line ml-2"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Right: Visual Balance -->
                        <div class="hidden lg:flex w-2/5 justify-end relative">
                            <div class="text-reveal delay-300 relative">
                                <div class="absolute -inset-10 bg-amber-100 rounded-full blur-3xl opacity-50"></div>
                                <div class="relative bg-white/20 backdrop-blur-sm p-12 rounded-[3rem] border border-white/30 shadow-2xl rotate-6 hover:rotate-0 transition-all duration-700">
                                    <i class="fas fa-map-marked-alt text-amber-600 text-[150px] drop-shadow-2xl"></i>
                                    <div class="absolute -top-4 -left-4 bg-white p-4 rounded-2xl shadow-xl border border-amber-50">
                                        <i class="fas fa-heart text-red-500 text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Arrows -->
        <div class="swiper-button-next hidden lg:flex"></div>
        <div class="swiper-button-prev hidden lg:flex"></div>
    </div>


    <!-- Section: Pusat Layanan Digital (Unified) -->
    @if($featuredLayanan->isNotEmpty())
    <div class="relative z-30 py-12">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <!-- Group Header -->
                <div class="flex flex-col items-center mb-8 text-center animate-fade-in">
                    <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/50 shadow-sm mb-4">
                        <span class="w-1.5 h-1.5 bg-teal-500 rounded-full"></span>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-700">Pusat Layanan Digital</span>
                    </div>
                </div>



                <!-- Section: Layanan Paling Dicari (Grid) -->
                <div class="grid grid-cols-2 md:grid-cols-{{ min(4, count($featuredLayanan) > 0 ? count($featuredLayanan) : 1) }} gap-5">
                    @forelse($featuredLayanan as $svc)
                        @php
                            $bgColor = $svc->warna_bg ?? 'bg-white/90';
                            $textColor = $svc->warna_text ?? 'text-slate-800';
                            $iconBg = str_replace('bg-', 'bg-', $svc->warna_bg); // Simple fallback
                            if (str_contains($iconBg, '-50')) {
                                $iconBg = str_replace('-50', '-100', $iconBg);
                            } else {
                                $iconBg = 'bg-slate-100';
                            }
                            
                            if ($svc->has_nodes) {
                                $onClick = "window.location.href='" . route('apply.layanan', $svc->slug) . "'";
                            } elseif ($svc->link_type === 'loker') {
                                $onClick = "window.location.href='" . route('public.loker.create') . "'";
                            } elseif ($svc->link_type === 'umkm') {
                                // Specific UMKM modal call if different
                                $onClick = "openSubmissionModal('{$svc->nama_layanan}', '{$svc->deskripsi_syarat}', " . json_encode($svc->attachment_requirements ?? []) . ")";
                            } elseif ($svc->link_type === 'external') {
                                $onClick = "window.location.href='{$svc->custom_link}'";
                            } else {
                                $onClick = "openSubmissionModal('{$svc->nama_layanan}', '{$svc->deskripsi_syarat}', " . json_encode($svc->attachment_requirements ?? []) . ")";
                            }
                        @endphp
                        <div onclick="{!! $onClick !!}"
                            class="bg-white/90 backdrop-blur-xl p-6 rounded-[2.5rem] shadow-lg border border-white hover:scale-105 transition-all cursor-pointer group">
                            <div
                                class="w-14 h-14 {{ $svc->warna_bg ?? 'bg-emerald-50' }} {{ $svc->warna_text ?? 'text-emerald-600' }} rounded-2xl flex items-center justify-center mb-4 group-hover:{{ str_replace('text-', 'bg-', $svc->warna_text ?? 'text-emerald-600') }} group-hover:text-white transition-all duration-500 shadow-sm">
                                <i class="fas {{ $svc->ikon ?? 'fa-star' }} text-2xl"></i>
                            </div>
                            <h3 class="font-black text-slate-800 text-sm mb-1 line-clamp-1 group-hover:text-teal-600 transition-colors">{{ $svc->nama_layanan }}</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight line-clamp-1">{{ $svc->estimasi_waktu ?? 'Akses Cepat' }}</p>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 bg-white/40 backdrop-blur-md rounded-[3rem] border border-white/50 border-dashed">
                            <p class="text-slate-400 text-sm font-black italic">Belum ada layanan populer yang diatur.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Subtle Help Text -->
                <p class="text-center text-[10px] text-slate-400 mt-8 font-black uppercase tracking-widest opacity-60">
                    <i class="fas fa-info-circle mr-2"></i> Akses data Anda menggunakan nomor yang terdaftar
                </p>
            </div>
        </div>
    </div>
    @endif



    <!-- Section: WhatsApp Slim Banner (NEW) -->
    <div class="container mx-auto px-6 relative z-10 mt-8 mb-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-1 rounded-[2rem] shadow-xl shadow-emerald-900/10 transition-transform hover:scale-[1.01]">
                <div class="bg-white/10 backdrop-blur-md rounded-[1.9rem] px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white text-2xl animate-pulse">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-black text-sm md:text-base leading-tight">Butuh Bantuan Cepat?</h4>
                            <p class="text-white/80 text-[10px] font-bold uppercase tracking-wider">Konsultasi Layanan via Chatbot 24/7</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                        <div class="flex items-center gap-3 text-center sm:text-right">
                            <div class="block">
                                <p class="text-white/60 text-[9px] font-black uppercase tracking-widest leading-none mb-1">WhatsApp Center</p>
                                <p class="text-white font-black text-[13px]">{{ appProfile()->whatsapp_bot_number ?? '08xxxxxxxxxx' }}</p>
                            </div>
                        </div>
                        @php
                            $waNumber = preg_replace('/[^0-9]/', '', appProfile()->whatsapp_bot_number ?? '');
                            $waLink = str_starts_with($waNumber, '0') ? '62' . substr($waNumber, 1) : $waNumber;
                        @endphp
                        <a href="https://wa.me/{{ $waLink }}" 
                           target="_blank"
                           class="btn bg-white hover:bg-emerald-50 text-emerald-700 border-0 rounded-2xl px-8 h-12 font-black transition-all flex items-center gap-2 w-full sm:w-auto justify-center">
                            Mulai Chat <i class="fas fa-paper-plane text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Info Hari Ini (Restored & Spaced) -->
    <div class="container mx-auto px-6 relative z-10 mt-12 mb-12">
        <div
            class="bg-white/80 backdrop-blur-md py-8 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-white/50">
            <div class="px-6 md:px-10">
                <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800 leading-none">Info Hari Ini</h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Update
                                Terkini Kecamatan</p>
                        </div>
                    </div>
                    <div class="h-px flex-grow bg-slate-100 mx-6 hidden md:block"></div>
                    <a href="#pengumuman" class="text-xs font-bold text-teal-600">Lihat Semua Info</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($publicAnnouncements->take(3) as $ann)
                        <div
                            class="bg-slate-50 p-5 rounded-2xl border border-slate-200/50 hover:bg-white hover:border-teal-200 transition-all flex flex-col h-full">
                            <div class="flex justify-between items-start mb-3">
                                <span
                                    class="bg-amber-50 text-amber-700 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter">Penting</span>
                                <span
                                    class="text-[10px] text-slate-400 font-bold tracking-tight">{{ $ann->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 mb-2 line-clamp-2">Agenda Kecamatan</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4 flex-grow">
                                {{ Str::limit($ann->content, 120) }}
                            </p>
                            <hr class="border-slate-100 mb-3">
                            <button onclick="openBotWithQuery('{{ $ann->content }}')"
                                class="text-xs font-black text-teal-600 hover:text-teal-700 flex items-center gap-2">
                                Pelajari Selengkapnya <i class="fas fa-chevron-right text-[8px]"></i>
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 italic">
                            Belum ada pengumuman khusus hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- Isolated Container: Low Z-Index, Relative Position, No Overlap with Global ID -->
    @auth
        @if(auth()->user()->hasRole('Operator Kecamatan') || auth()->user()->hasRole('Super Admin'))
            <div class="py-4 relative z-10">
                <div class="container mx-auto px-6 flex justify-center md:justify-end gap-4">
                    <a href="{{ route('economy.create') }}"
                        class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-black text-xs bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-xl transition-colors">
                        <i class="fas fa-plus-circle"></i> Daftar Usaha / Jasa Baru
                    </a>
                    <a href="{{ route('kecamatan.pelayanan.visitor.index') }}"
                        class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-teal-600 transition-colors px-4 py-1.5 rounded-full border border-slate-200 bg-white hover:border-teal-200 hover:bg-teal-50 hover:shadow-sm no-underline decoration-0">
                        <i class="fas fa-book-open text-teal-500"></i>
                        <span>Akses Internal: Buku Tamu Digital</span>
                        <i class="fas fa-external-link-alt text-[10px] ml-1 opacity-50"></i>
                    </a>
                </div>
            </div>
        @endif
    @endauth
    <!-- END INTERNAL ACCESS -->

    @if(appProfile()->is_menu_statistik_active)
    <!-- Section: Statistik & Kepercayaan (NEW) -->
    <div class="py-16 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center group">
                    <div
                        class="text-4xl md:text-5xl font-black text-teal-600 mb-2 group-hover:scale-110 transition-transform">
                        1.2k+</div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Layanan Selesai</p>
                    <div class="w-8 h-1 bg-teal-100 mx-auto mt-4 rounded-full"></div>
                </div>
                <div class="text-center group">
                    <div
                        class="text-4xl md:text-5xl font-black text-amber-500 mb-2 group-hover:scale-110 transition-transform">
                        17</div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Desa Terintegrasi</p>
                    <div class="w-8 h-1 bg-amber-100 mx-auto mt-4 rounded-full"></div>
                </div>
                <div class="text-center group">
                    <div
                        class="text-4xl md:text-5xl font-black text-blue-600 mb-2 group-hover:scale-110 transition-transform">
                        24h</div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Respon Cepat</p>
                    <div class="w-8 h-1 bg-blue-100 mx-auto mt-4 rounded-full"></div>
                </div>
                <div onclick="openSurveyModal()" class="text-center group cursor-pointer">
                    <div
                        class="text-4xl md:text-5xl font-black text-emerald-600 mb-2 group-hover:scale-110 transition-transform">
                        98%</div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kepuasan Warga</p>
                    <div class="w-12 h-1 bg-emerald-100 mx-auto mt-4 rounded-full group-hover:w-20 transition-all">
                    </div>
                    <p
                        class="text-[9px] text-emerald-500 font-bold mt-2 opacity-0 group-hover:opacity-100 transition-opacity uppercase tracking-tighter italic">
                        Nilai Layanan Kami <i class="fas fa-chevron-right ml-1"></i></p>
                </div>
            </div>
        </div>
    </div>
    @endif



    @if(appProfile()->is_menu_pelayanan_active)
    <!-- Section: Layanan Terpadu (REFINED) -->
    <div class="w-full h-24 bg-gradient-to-b from-slate-50 to-white"></div>
    <div id="layanan" class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <div
                    class="inline-flex items-center gap-2 bg-teal-50 text-teal-700 px-4 py-2 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-magic"></i>
                    <span>Layanan Lintas Desa</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-slate-800 mb-4">Layanan Kecamatan Terpadu</h2>
                <p class="text-slate-500 max-w-2xl mx-auto font-medium leading-relaxed">
                    Proses administrasi cepat untuk rekomendasi, validasi, dan koordinasi publik tingkat kecamatan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($masterLayanan as $svc)
                    @if(in_array($svc->slug, ['kependudukan', 'pengaduan', 'jam-layanan'])) @continue @endif
                    @php
                        // Government Symbol Mapping
                        $iconMap = [
                            'ktp' => 'fa-id-card',
                            'kk' => 'fa-users',
                            'pindah' => 'fa-truck-moving',
                            'akta' => 'fa-file-signature',
                            'sktm' => 'fa-hand-holding-heart',
                            'usaha' => 'fa-briefcase',
                            'izin' => 'fa-stamp',
                            'domisili' => 'fa-map-marker-alt',
                        ];
                        $finalIcon = $svc->ikon ?? 'fa-file-shield';
                        foreach($iconMap as $key => $icon) {
                            if(str_contains(strtolower($svc->nama_layanan), $key)) {
                                $finalIcon = $icon;
                                break;
                            }
                        }
                    @endphp
                    <div
                        class="group bg-white rounded-[2.5rem] p-8 border border-slate-100 hover:border-[#003366]/20 transition-all duration-500 hover:shadow-[0_20px_50px_-12px_rgba(0,51,102,0.1)] active:scale-[0.98] relative overflow-hidden flex flex-col h-full cursor-pointer">
                        <!-- Top Accent -->
                        <div
                            class="absolute top-0 left-0 w-full h-1.5 bg-[#003366] opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>

                        <div class="flex items-start gap-6 mb-6">
                            <div
                                class="w-16 h-16 rounded-2xl bg-slate-50 text-[#003366] flex items-center justify-center shrink-0 shadow-sm group-hover:bg-[#003366] group-hover:text-white transition-all duration-500">
                                <i class="fas {{ $finalIcon }} text-2xl"></i>
                            </div>
                            <div>
                                <h3
                                    class="text-xl font-black text-slate-800 mb-1 group-hover:text-[#003366] transition-colors leading-tight">
                                    {{ $svc->nama_layanan }}
                                </h3>
                                <div
                                    class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span>Layanan Terverifikasi</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 mb-8 flex-grow">
                            <div
                                class="bg-slate-50 rounded-2xl p-4 border border-slate-100 group-hover:bg-white group-hover:border-slate-200 transition-colors">
                                <p
                                    class="text-[10px] font-black text-[#003366] uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <i class="fas fa-clipboard-list"></i> Persyaratan Dokumen
                                </p>
                                <p class="text-xs text-slate-600 leading-relaxed font-medium line-clamp-3">
                                    {{ $svc->deskripsi_syarat }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ $svc->slug ? route('apply.layanan', $svc->slug) : '#' }}"
                            class="btn btn-sm bg-[#003366] hover:bg-[#004488] border-none text-white rounded-xl px-6 w-full py-3 h-auto font-black uppercase tracking-widest text-[10px] flex items-center justify-center transition-all shadow-lg shadow-[#003366]/10 active:scale-95">
                            Ajukan Permohonan
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Lacak Berkas (Moved Here) -->
            <div class="mt-16 max-w-4xl mx-auto">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-black text-slate-800">Sudah Punya Berkas? Cek Statusnya Disini</h3>
                </div>
                <div class="bg-white p-2 rounded-[2.5rem] shadow-xl border border-slate-200 flex flex-col md:flex-row items-center gap-2 mb-8">
                    <div class="flex-grow flex items-center px-6 gap-3 w-full">
                        <i class="fas fa-search text-teal-500 text-xl"></i>
                        <input type="text" id="trackingInput" name="q"
                            placeholder="Masukkan No. WhatsApp atau ID Permohonan..."
                            class="bg-transparent border-none focus:ring-0 text-base font-bold w-full text-slate-800 h-14">
                    </div>
                    <button onclick="handleTracking()"
                        class="btn bg-teal-600 hover:bg-teal-700 text-white border-0 rounded-[1.8rem] px-10 h-14 w-full md:w-auto font-black shadow-lg shadow-teal-900/10">
                        Lacak Berkas
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif



    <!-- Section: Pariwisata & Budaya (NEW) -->
    @if(appProfile()->image_pariwisata || appProfile()->image_festival)
        <div class="py-24 bg-slate-50 overflow-hidden border-t border-slate-100">
            <div class="container mx-auto px-6 text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-slate-800 mb-4">Potensi Pariwisata & Budaya</h2>
                <p class="text-slate-500 max-w-2xl mx-auto font-medium">Temukan kekayaan alam dan warisan budaya yang membanggakan di {{ appProfile()->full_region_name }}.</p>
            </div>
            
            <div class="container mx-auto px-6">
                <div class="flex flex-col lg:flex-row gap-8 items-center">
                    @if(appProfile()->image_pariwisata)
                        <div class="w-full lg:w-1/2 group relative">
                            <div class="aspect-video rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white">
                                <img src="{{ asset('storage/' . appProfile()->image_pariwisata) }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                <div class="absolute inset-0 bg-gradient-to-t from-teal-900/60 to-transparent"></div>
                                <div class="absolute bottom-10 left-10 text-white text-left">
                                    <h3 class="text-3xl font-black mb-2">Pesona Wisata</h3>
                                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Destinasi Unggulan Lokal</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(appProfile()->image_festival)
                        <div class="w-full lg:w-1/2 group relative">
                            <div class="aspect-video rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white">
                                <img src="{{ asset('storage/' . appProfile()->image_festival) }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                <div class="absolute inset-0 bg-gradient-to-t from-amber-900/60 to-transparent"></div>
                                <div class="absolute bottom-10 left-10 text-white text-left">
                                    <h3 class="text-3xl font-black mb-2">Event & Festival</h3>
                                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Agenda Budaya & Hiburan</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if(appProfile()->is_menu_umkm_active)
    <!-- Section: Pasar Rakyat & Produk UMKM (NEW - Buyer Experience) -->
    <div id="pasar" class="py-24 bg-slate-50 border-t border-slate-100 relative overflow-hidden">
        {{-- Background Decorations --}}
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-amber-100/30 rounded-full blur-[100px] translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-teal-100/30 rounded-full blur-[100px] -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                <div class="text-left">
                    <div class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 px-4 py-1.5 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest border border-amber-100">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Ekonomi & UMKM Lokal</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-slate-800 mb-2">Pasar Rakyat Digital</h2>
                    <p class="text-slate-500 font-medium max-w-xl">Dukung ekonomi warga dengan membeli produk dan jasa langsung dari tetangga kita.</p>
                </div>
                <a href="{{ route('economy.index', ['tab' => 'produk']) }}" class="btn bg-white hover:bg-slate-900 hover:text-white border-slate-200 text-slate-800 rounded-2xl px-8 h-14 font-black transition-all shadow-sm flex items-center gap-2 group">
                    Eksplorasi Semua Produk <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            {{-- Grid Produk & Toko --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Sisi Kiri: Produk Unggulan (Katalog) --}}
                <div class="lg:col-span-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($featuredProducts as $fp)
                        @php
                            $waPhone = preg_replace('/[^0-9]/', '', $fp->contact_wa ?? '');
                            if (str_starts_with($waPhone, '0')) { $waPhone = '62' . substr($waPhone, 1); }
                            $detailLink = route('economy.produk.show', $fp->id);
                        @endphp
                        <div class="group bg-white rounded-[2.5rem] p-4 border border-slate-100 hover:border-teal-200 transition-all duration-500 hover:shadow-2xl hover:shadow-teal-900/5">
                            <div class="aspect-[4/3] rounded-[2rem] overflow-hidden mb-5 relative">
                                <img src="{{ $fp->image_path ? asset('storage/' . $fp->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute top-4 right-4 flex flex-col items-end gap-2">
                                    <span class="bg-white/90 backdrop-blur-md text-slate-800 text-[10px] font-black px-3 py-1.5 rounded-xl shadow-sm border border-white">
                                        Rp {{ number_format($fp->price ?? 0, 0, ',', '.') }}
                                    </span>
                                    @php $opStatus = $fp->operational_status; @endphp
                                    <span class="bg-white/90 backdrop-blur-md text-{{ $opStatus['color'] }}-600 text-[9px] font-black px-3 py-1.5 rounded-xl shadow-sm border border-{{ $opStatus['color'] }}-100 flex items-center gap-1.5">
                                        <i class="fas {{ $opStatus['icon'] }}"></i> {{ $opStatus['label'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="px-2">
                                <p class="text-[9px] font-black text-teal-600 uppercase tracking-widest mb-1">{{ $fp->name }}</p>
                                <h4 class="text-lg font-black text-slate-800 mb-4 line-clamp-1 group-hover:text-teal-700 transition-colors">{{ $fp->product }}</h4>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $detailLink }}" class="flex-1 bg-slate-50 hover:bg-teal-600 hover:text-white text-slate-600 font-bold py-3 rounded-xl text-xs text-center transition-all">Lihat Detail</a>
                                    <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="w-11 h-11 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all"><i class="fab fa-whatsapp text-lg"></i></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Sisi Kanan: Toko Terverifikasi (Highlight) --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 h-full flex flex-col">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i class="fas fa-certificate text-amber-500"></i> Toko Terverifikasi
                        </h4>
                        
                        <div class="space-y-6 flex-grow">
                            @foreach($officialUmkms as $ou)
                            <a href="{{ route('umkm_rakyat.show', $ou->slug) }}" class="flex items-center gap-4 group/toko">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-100 group-hover/toko:scale-110 transition-transform">
                                    @if($ou->foto_usaha)
                                        <img src="{{ asset('storage/' . $ou->foto_usaha) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-store"></i></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-black text-slate-800 group-hover/toko:text-amber-600 transition-colors truncate">{{ $ou->nama_usaha }}</h5>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $ou->jenis_usaha }} • {{ $ou->desa }}</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover/toko:bg-amber-50 group-hover/toko:text-amber-500 transition-all">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </div>
                            </a>
                            @endforeach
                        </div>

                        <div class="mt-8 p-6 bg-amber-50 rounded-3xl border border-amber-100">
                            <p class="text-xs font-bold text-amber-800 leading-relaxed mb-4">Ingin produk Anda tayang di sini? Daftar sekarang gratis!</p>
                            <a href="{{ route('economy.create') }}" class="text-[10px] font-black uppercase tracking-widest text-amber-600 flex items-center gap-2 hover:gap-3 transition-all">
                                Buka Usaha / Jasa <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(appProfile()->is_menu_berita_active)
    <!-- Berita & Informasi Section -->
    <div id="berita" class="py-20 bg-white border-t border-slate-100">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Warta Kecamatan</h2>
                <a href="{{ route('public.berita.index') }}"
                    class="group flex items-center text-sm font-semibold text-rose-600 hover:text-rose-700 transition-colors">
                    Lihat Semua
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($latestBerita as $item)
                    <div class="group bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden hover:shadow-2xl transition-all duration-500 flex flex-col h-full">
                        {{-- Image Wrapper with Background Pattern --}}
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-200 to-slate-100 animate-pulse"></div>
                            
                            <img src="{{ $item->thumbnail_url }}" 
                                 alt="{{ $item->judul }}"
                                 onerror="this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800'; this.onerror=null;"
                                 class="relative z-10 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            
                            {{-- Badge: Positioned more elegantly --}}
                            <div class="absolute top-4 left-4 z-20">
                                @php
                                    $catColors = [
                                        'Pemerintahan' => 'bg-blue-600',
                                        'Pembangunan' => 'bg-emerald-600',
                                        'Sosial' => 'bg-purple-600',
                                        'Ekonomi' => 'bg-amber-600',
                                        'default' => 'bg-rose-600'
                                    ];
                                    $color = $catColors[$item->kategori] ?? $catColors['default'];
                                @endphp
                                <span class="{{ $color }} text-white text-[9px] font-black px-3 py-1.5 rounded-xl shadow-lg uppercase tracking-widest backdrop-blur-md bg-opacity-90">
                                    {{ $item->kategori }}
                                </span>
                            </div>

                            {{-- Bottom Gradient Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-15"></div>
                        </div>

                        {{-- Content --}}
                        <div class="p-8 flex-grow flex flex-col">
                            <div class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-widest gap-2 mb-4">
                                <span class="text-rose-600">{{ $item->author->nama_lengkap ?? 'Admin' }}</span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span>{{ $item->published_at ? $item->published_at->diffForHumans() : '-' }}</span>
                            </div>

                            <h3 class="text-lg font-black text-slate-800 leading-tight mb-4 group-hover:text-rose-600 transition-colors line-clamp-2">
                                <a href="{{ route('public.berita.show', $item->slug) }}">
                                    {{ $item->judul }}
                                </a>
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-6 font-medium">
                                {{ Str::limit($item->ringkasan, 120) }}
                            </p>

                            <div class="mt-auto pt-6 border-t border-slate-50">
                                <a href="{{ route('public.berita.show', $item->slug) }}" class="text-[10px] font-black text-rose-600 uppercase tracking-widest flex items-center gap-2 group/btn">
                                    Baca Selengkapnya <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-16 text-center bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                        <div
                            class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="far fa-newspaper text-slate-400 text-2xl"></i>
                        </div>
                        <h3 class="text-slate-900 font-medium mb-1">Belum ada berita</h3>
                        <p class="text-slate-500 text-sm">Nantikan informasi terbaru dari kami.</p>
                    </div>
                @endforelse
            </div> <!-- Close Grid -->

            <div class="mt-16 text-center">
                <a href="{{ route('public.berita.index') }}"
                    class="btn bg-white hover:bg-rose-50 text-rose-600 border-rose-100 rounded-2xl px-10 font-black shadow-sm transition-all h-14">
                    Lihat Semua Berita & Kegiatan <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div> <!-- Close Container -->
    </div> <!-- Close Section -->
    @endif



    <!-- Section: Survei Kepuasan Masyarakat (SKM) - STRATEGIC LOCATION -->
    <div id="survey-section" class="py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div
                class="bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 rounded-[3rem] p-8 md:p-16 relative overflow-hidden shadow-2xl border border-white/5">
                <!-- Ornaments -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl opacity-30">
                </div>

                <div class="flex flex-col lg:flex-row items-center gap-16 relative z-10">
                    <!-- Left: Content -->
                    <div class="w-full lg:w-3/5 text-center lg:text-left">
                        <div
                            class="inline-flex items-center gap-3 bg-teal-500/10 text-teal-300 px-5 py-2 rounded-full mb-8 border border-teal-500/20">
                            <i class="fas fa-star text-xs"></i>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Partisipasi Publik</span>
                        </div>
                        <h2 class="text-4xl md:text-6xl font-black text-white mb-6 leading-[1.1]">
                            Survei Kepuasan<br>
                            <span class="text-teal-400">Masyarakat (SKM)</span>
                        </h2>
                        <p class="text-lg text-slate-300 mb-10 leading-relaxed font-medium max-w-2xl">
                            Bantu kami meningkatkan kualitas pelayanan publik di
                            <strong>{{ appProfile()->region_level }} {{ appProfile()->region_name }}</strong>.
                            Penilaian Anda sangat berharga untuk menciptakan transparansi dan akuntabilitas yang lebih
                            baik.
                        </p>

                        <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                            <a href="https://sukma.jatimprov.go.id/fe/survey?idUser=2710" target="_blank"
                                class="btn bg-teal-500 hover:bg-teal-400 text-slate-900 border-0 rounded-2xl px-10 h-16 font-black shadow-[0_10px_30px_rgba(20,184,166,0.3)] transition-all">
                                <i class="fas fa-external-link-alt mr-2 text-sm"></i>
                                Isi Survei Sekarang
                            </a>
                            <button onclick="openSurveyModal()"
                                class="btn bg-white/10 hover:bg-white/20 text-white border border-white/10 backdrop-blur-md rounded-2xl px-10 h-16 font-black transition-all">
                                <i class="fas fa-qrcode mr-2 text-sm"></i>
                                Scan QR Code
                            </button>
                        </div>

                        <div class="mt-10 flex items-center justify-center lg:justify-start gap-6">
                            <div class="flex -space-x-3">
                                <img class="w-10 h-10 rounded-full border-2 border-slate-800"
                                    src="https://ui-avatars.com/api/?name=Warga+1&background=0d9488&color=fff"
                                    alt="Warga">
                                <img class="w-10 h-10 rounded-full border-2 border-slate-800"
                                    src="https://ui-avatars.com/api/?name=Warga+2&background=0369a1&color=fff"
                                    alt="Warga">
                                <img class="w-10 h-10 rounded-full border-2 border-slate-800"
                                    src="https://ui-avatars.com/api/?name=Warga+3&background=15803d&color=fff"
                                    alt="Warga">
                            </div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                <span class="text-white">1,500+</span> Responden Telah Berpartisipasi
                            </p>
                        </div>
                    </div>

                    <!-- Right: Visual/QR -->
                    <div class="w-full lg:w-2/5 flex justify-center">
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-teal-500/20 blur-[50px] rounded-full group-hover:bg-teal-500/30 transition-all duration-500">
                            </div>
                            <div
                                class="bg-white p-8 rounded-[2.5rem] shadow-2xl relative z-10 border border-teal-500/20 transform group-hover:rotate-3 transition-transform duration-500 max-w-[300px]">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=https://sukma.jatimprov.go.id/fe/survey?idUser=2710"
                                    alt="QR Code Survei Kepuasan Masyarakat" class="w-full h-auto rounded-xl">
                                <p
                                    class="text-center text-[10px] font-black text-slate-400 uppercase tracking-widest mt-6">
                                    Scan untuk Menilai</p>
                                <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-center gap-2">
                                    <img src="{{ appProfile()->logo_path ? asset('storage/' . appProfile()->logo_path) : asset('img/logo-default.png') }}"
                                        class="h-6 w-auto opacity-50 gray-scale grayscale" alt="Logo">
                                    <span class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter">Portal
                                        SUKMA JATIM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium SEO Optimized Footer -->
    @include('layouts.partials.public.footer')


    <!-- Accessibility Assets -->
    <link rel="stylesheet" href="{{ asset('css/min/accessibility.min.css') }}">
    <script src="{{ asset('js/min/accessibility.min.js') }}" defer></script>

    <!-- Accessibility & Voice Floating Buttons -->
    <div class="fixed bottom-5 left-5 z-[60] flex items-center gap-3">
        <!-- Voice Guide Toggle (Accessibility Orange) -->
        <button id="btnVoiceGuideToggle" onclick="activateVoiceGuide()"
            class="btn btn-circle bg-orange-600 hover:bg-orange-700 border-0 shadow-lg w-14 h-14 relative group transition-all duration-300"
            aria-label="Aktifkan Pemandu Suara" title="Bantuan Suara">
            <i class="fas fa-deaf text-white text-xl transition-transform group-hover:scale-110"></i>
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span id="voice-ping"
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 hidden"></span>
                <span id="voice-dot" class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 hidden"></span>
            </span>
        </button>

        <!-- Accessibility Toggle -->
        <button id="accessibility-toggle"
            class="btn btn-circle bg-blue-600 hover:bg-blue-700 border-0 shadow-lg w-14 h-14"
            aria-label="Buka Menu Aksesibilitas">
            <i class="fas fa-wheelchair text-white text-xl"></i>
        </button>
    </div>

    <!-- Sticky Survey Tab (Side) -->
    <div class="sticky-survey-tab">
        <button onclick="openSurveyModal()" class="survey-tab-btn">
            <i class="fas fa-star text-amber-400"></i>
            <span>Isi Survei Kepuasan</span>
        </button>
    </div>

    <!-- Floating Action Button (Chatbot) -->
    <div class="fixed bottom-5 right-5 z-[60] group">
        <div class="absolute bottom-full right-0 mb-3 hidden group-hover:block transition-all animate-bounce">
            <span
                class="bg-teal-600 text-white text-xs px-3 py-1 rounded-full shadow-lg whitespace-nowrap italic">Asisten Digital</span>
        </div>
        <button onclick="document.getElementById('publicServiceModal').showModal()"
            class="btn btn-circle bg-teal-600 hover:bg-teal-700 border-0 shadow-xl w-16 h-16 transform transition-transform hover:scale-110">
            <i class="fas fa-robot text-white text-2xl"></i>
        </button>
    </div>


    <!-- Service Submission Modal (PERFECTED & COMPACT) -->
    <dialog id="permohonanModal" class="modal modal-bottom sm:modal-middle">
        <div
            class="modal-box max-w-2xl rounded-3xl bg-white p-0 overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[90vh]">
            <!-- Modal Header (Compact & Professional) -->
            <div
                class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 flex justify-between items-center text-white shrink-0 shadow-lg">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-paper-plane text-lg text-teal-100"></i>
                    </div>
                    <div>
                        <h3 id="modalServiceTitle" class="font-black text-sm uppercase tracking-wider opacity-90">
                            Formulir Pengajuan</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                            <span class="text-[9px] font-bold text-teal-100 uppercase tracking-widest">Respon Cepat
                                1x24
                                Jam</span>
                        </div>
                    </div>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost text-teal-100 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>

            <form id="submissionForm" class="p-6 space-y-5 bg-slate-50/30 overflow-y-auto flex-grow">
                @csrf
                <!-- Identification Section (3-Column Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="form-control">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">Nama
                            Lengkap</label>
                        <input type="text" name="nama_pemohon" placeholder="Sesuai KTP..."
                            class="input input-bordered bg-slate-50 border-slate-200 h-11 rounded-xl focus:border-teal-500 font-medium transition-all text-xs"
                            required>
                    </div>

                    <div class="form-control">
                        <label
                            class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">WhatsApp</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-[10px]">+62</span>
                            <input type="tel" name="whatsapp" placeholder="812xxxx..."
                                class="input input-bordered bg-slate-50 border-slate-200 h-11 rounded-xl focus:border-teal-500 font-medium pl-9 w-full transition-all text-xs"
                                required>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">NIK
                            (Opsional)</label>
                        <input type="text" name="nik" placeholder="16 Digit..."
                            class="input input-bordered bg-slate-50 border-slate-200 h-11 rounded-xl focus:border-teal-500 font-medium transition-all text-xs"
                            maxlength="16">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">Asal
                            Desa</label>
                        <select name="desa_id"
                            class="select select-bordered bg-slate-50 border-slate-200 h-11 rounded-xl focus:border-teal-500 font-medium transition-all text-xs"
                            required>
                            <option value="" disabled selected>Pilih Desa...</option>
                            @foreach($desas as $desa)
                                <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                            @endforeach
                            <option value="999">Luar Wilayah {{ appProfile()->region_level }}
                                {{ appProfile()->region_name }}
                            </option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">Jenis
                            Layanan</label>
                        <input type="text" id="inputJenisLayanan" name="jenis_layanan" readonly
                            class="input input-bordered bg-slate-100 border-slate-200 h-11 rounded-xl font-bold text-teal-700 text-xs">
                    </div>
                </div>

                <!-- Job Directory Specific Selection (Click-only) -->
                <div id="jobSelectionArea" class="hidden space-y-3">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-1 block">Pilih
                        Kategori Jasa / Pekerjaan</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" onclick="setJobType('Jasa Harian', 'Tukang / Jasa Profesional')"
                            class="job-type-btn p-3 rounded-2xl border border-slate-200 bg-white hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center text-center group">
                            <div
                                class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-2 group-hover:bg-white">
                                <i class="fas fa-tools text-teal-600"></i>
                            </div>
                            <span class="text-[9px] font-black text-slate-700 leading-tight">Jasa Harian</span>
                        </button>

                        <button type="button" onclick="setJobType('Transportasi', 'Ojek / Angkutan Rakyat')"
                            class="job-type-btn p-3 rounded-2xl border border-slate-200 bg-white hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center text-center group">
                            <div
                                class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-2 group-hover:bg-white">
                                <i class="fas fa-motorcycle text-teal-600"></i>
                            </div>
                            <span class="text-[9px] font-black text-slate-700 leading-tight">Transportasi</span>
                        </button>

                        <button type="button" onclick="setJobType('Jasa Keliling', 'Kuliner / Sayur Keliling')"
                            class="job-type-btn p-3 rounded-2xl border border-slate-200 bg-white hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center text-center group">
                            <div
                                class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-2 group-hover:bg-white">
                                <i class="fas fa-store text-teal-600"></i>
                            </div>
                            <span class="text-[9px] font-black text-slate-700 leading-tight">Jasa Keliling</span>
                        </button>

                        <button type="button" onclick="setJobType('Lainnya', 'Tenaga Kerja / Pekerja Umum')"
                            class="job-type-btn p-3 rounded-2xl border border-slate-200 bg-white hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center text-center group">
                            <div
                                class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-2 group-hover:bg-white">
                                <i class="fas fa-users text-teal-600"></i>
                            </div>
                            <span class="text-[9px] font-black text-slate-700 leading-tight">Lainnya</span>
                        </button>
                    </div>
                </div>

                <div class="form-control">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">Uraian /
                        Keperluan Singkat</label>
                    <textarea name="uraian"
                        placeholder="Contoh: Mengajukan pembuatan KK baru karena penambahan anggota keluarga..."
                        class="textarea textarea-bordered bg-slate-50 border-slate-200 rounded-xl focus:border-teal-500 min-h-[80px] font-medium transition-all text-xs"
                        required></textarea>
                </div>

                <!-- Attachment Area (DYNAMIC & LABELED) -->
                <div class="bg-teal-50/50 p-4 rounded-2xl border border-dashed border-teal-200">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-paperclip text-teal-600 text-xs text-xs text-xs"></i>
                            <span class="text-[11px] font-black text-teal-700 uppercase tracking-widest">Persyaratan
                                Berkas</span>
                        </div>
                        <button type="button" onclick="addAttachmentField()"
                            class="btn btn-xs btn-ghost text-teal-600 hover:bg-teal-100 rounded-lg text-[9px] font-bold">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Berkas
                        </button>
                    </div>

                    <div id="dynamicAttachments" class="space-y-3">
                        <!-- JS dynamic fields go here -->
                        <div class="bg-white/50 border border-slate-100 rounded-xl p-3 text-center py-6">
                            <i class="fas fa-spinner animate-spin text-teal-300 text-xl"></i>
                        </div>
                    </div>

                    <p class="mt-3 text-[9px] text-teal-600/70 font-medium px-1">
                        <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, PDF (Maks 5MB/file)
                    </p>
                </div>

                <!-- Anti-Spam Honeypot (Hidden) -->
                <div class="hidden">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group px-1">
                        <input type="checkbox" name="is_agreed" class="checkbox checkbox-teal checkbox-xs rounded-md"
                            checked required>
                        <span
                            class="text-[10px] text-slate-500 font-medium group-hover:text-slate-700 transition-colors">Saya
                            menyatakan data di atas benar & sesuai aslinya.</span>
                    </label>

                    <button type="submit" id="btnSubmitPermohonan"
                        class="btn bg-teal-600 hover:bg-teal-700 border-0 text-white btn-block rounded-xl py-4 h-auto font-black uppercase tracking-widest text-xs shadow-lg hover:shadow-teal-200 transition-all">
                        Kirim Pengajuan Sekarang <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </dialog>


    <!-- Administrative Bot Portal -->
    <dialog id="publicServiceModal" class="modal">
        <div
            class="modal-box max-w-md rounded-3xl bg-white p-0 overflow-hidden shadow-2xl flex flex-col h-[600px] border border-slate-100">
            <!-- Header Bot -->
            <div
                class="bg-gradient-to-r from-teal-600 to-teal-700 p-5 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-robot text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base leading-tight">Asisten Digital Administrasi</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            <p class="text-[10px] text-teal-100 font-medium">Online & Siap Membantu</p>
                        </div>
                    </div>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost text-teal-100 hover:text-white"><i
                            class="fas fa-times"></i></button>
                </form>
            </div>

            <!-- Chat Area -->
            <div id="chatMessages" class="flex-grow p-4 overflow-y-auto bg-slate-50 space-y-4">
                <!-- Welcome Message -->
                <div class="flex items-start gap-2.5">
                    <div
                        class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center shrink-0 shadow-sm border border-teal-200">
                        <i class="fas fa-robot text-teal-600 text-xs"></i>
                    </div>
                    <div class="space-y-3 max-w-[85%]">
                        <div
                            class="bg-white border border-slate-200 text-slate-700 p-4 rounded-2xl rounded-tl-none text-xs leading-relaxed shadow-sm">
                            <p class="font-bold text-teal-700 mb-1">Halo! Saya Asisten Digital Kecamatan.</p>
                            <p>Saya siap membantu Anda memberikan informasi resmi terkait persyaratan administrasi.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="sendQuickChip('KTP')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">🪪
                                Syarat KTP</button>
                            <button onclick="sendQuickChip('KK')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">👨‍👩‍👧‍👦
                                Syarat KK</button>
                            <button onclick="sendQuickChip('Pindah')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">🚚
                                Pindah Domisili</button>
                            <button onclick="sendQuickChip('Akta')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">📄
                                Akta Lahir/Mati</button>
                            <button onclick="sendQuickChip('Jam Layanan')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">⏰
                                Jam Layanan</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white border-t border-slate-100 shrink-0 shadow-[0_-4px_15px_-5px_rgba(0,0,0,0.05)]">
                <form id="publicFaqForm" class="relative">
                    <input type="text" id="botQuery"
                        class="input input-bordered w-full bg-slate-50 border-slate-200 focus:border-teal-500 rounded-2xl pr-20 text-sm text-slate-700 transition-all focus:shadow-md"
                        placeholder="Ketik atau bicara..." autocomplete="off" required>

                    <!-- Mic Button -->
                    <button type="button" id="btnMic" onclick="toggleVoiceInput()"
                        class="absolute right-10 top-1/2 -translate-y-1/2 btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-teal-600 transition-colors"
                        title="Dikte Suara">
                        <i class="fas fa-microphone"></i>
                    </button>

                    <button type="submit" id="btnSendBot"
                        class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-sm btn-circle bg-teal-600 hover:bg-teal-700 border-0 text-white shadow-md transition-all active:scale-95">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </form>
                <div class="flex justify-between items-center mt-3 px-1">
                    <p class="text-[9px] text-slate-400 italic">Informasi Resmi Database FAQ.</p>
                    <button onclick="startClarification()"
                        class="text-[9px] font-bold text-teal-600 hover:underline">Butuh Tindak Lanjut
                        Petugas?</button>
                </div>
            </div>
        </div>
    </dialog>

    <!-- SP4N-LAPOR Style Complaint Modal -->
    <dialog id="complaintModal" class="modal modal-bottom sm:modal-middle">
        <div
            class="modal-box max-w-lg rounded-t-3xl rounded-b-3xl bg-white p-0 shadow-2xl border border-rose-100 h-[80vh] overflow-y-auto">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-rose-500 to-rose-600 p-5 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-exclamation-circle text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base leading-tight">Form Pengaduan</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 bg-white/50 rounded-full"></span>
                            <p class="text-[10px] text-rose-100 font-medium">Sampaikan keluhan Anda dengan jelas</p>
                        </div>
                    </div>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost text-rose-100 hover:text-white"><i
                            class="fas fa-times"></i></button>
                </form>
            </div>

            <!-- Form -->
            <form id="complaintForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="jenis_layanan" value="Pengaduan Publik">
                <input type="hidden" name="category" value="pengaduan">

                <!-- Kategori Pengaduan -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Kategori</label>
                    <select name="jenis_pengaduan" id="jenisPengaduan"
                        class="select select-bordered w-full mt-1 rounded-xl bg-slate-50 border-slate-200 focus:border-rose-500"
                        required>
                        <option value="">Pilih kategori pengaduan...</option>
                        <option value="Pengaduan">📢 Pengaduan (Layanan Tidak Memadai)</option>
                        <option value="Aspirasi">💡 Aspirasi (Saran & Masukan)</option>
                        <option value="Permintaan">📋 Permintaan (Butuh Layanan Khusus)</option>
                    </select>
                </div>

                <!-- Nama -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Nama Pelapor <span
                            class="text-slate-400">(Opsional)</span></label>
                    <input type="text" name="nama_pemohon" id="complaintName"
                        class="input input-bordered w-full mt-1 rounded-xl bg-slate-50 border-slate-200 focus:border-rose-500"
                        placeholder="Nama lengkap Anda">
                </div>

                <!-- No WhatsApp -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">No. WhatsApp <span
                            class="text-rose-500">*</span></label>
                    <input type="tel" name="whatsapp" id="complaintWa"
                        class="input input-bordered w-full mt-1 rounded-xl bg-slate-50 border-slate-200 focus:border-rose-500"
                        placeholder="08xxxxxxxxx" required>
                    <p class="text-[10px] text-slate-400 mt-1">Notifikasi status akan dikirim via WhatsApp</p>
                </div>

                <!-- Judul Pengaduan -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Judul Pengaduan <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="judul_pengaduan" id="complaintTitle"
                        class="input input-bordered w-full mt-1 rounded-xl bg-slate-50 border-slate-200 focus:border-rose-500"
                        placeholder="Ringkasan singkat pengaduan Anda" required maxlength="100">
                </div>

                <!-- Isi Pengaduan -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Isi Pengaduan <span
                            class="text-rose-500">*</span></label>
                    <textarea name="uraian" id="complaintMessage" rows="4"
                        class="textarea textarea-bordered w-full mt-1 rounded-xl bg-slate-50 border-slate-200 focus:border-rose-500"
                        placeholder="Jelaskan kronologi dan detail pengaduan Anda dengan jelas..." required
                        maxlength="1000"></textarea>
                    <div class="flex justify-between mt-1">
                        <p class="text-[10px] text-slate-400">Min. 20 karakter</p>
                        <p class="text-[10px] text-slate-400"><span id="charCount">0</span>/1000</p>
                    </div>
                </div>

                <!-- Lampiran -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Lampiran <span
                            class="text-slate-400">(Opsional)</span></label>
                    <input type="file" name="foto[]" id="complaintAttachment"
                        class="file-input file-input-bordered w-full mt-1 rounded-xl bg-slate-50 border-slate-200 focus:border-rose-500"
                        accept="image/*,.pdf" multiple>
                    <div id="complaintPhotoPreview" class="flex flex-wrap gap-2 mt-2"></div>
                    <p class="text-[10px] text-slate-400 mt-1">JPG, PNG, atau PDF (Maks. 5MB per file)</p>
                </div>

                <!-- Anonim / Rahasia -->
                <div class="flex items-center gap-5">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="is_anonim" class="checkbox checkbox-sm" value="1">
                        <span
                            class="text-sm text-slate-600 font-medium group-hover:text-rose-600 transition-colors">Anonim</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="is_rahasia" class="checkbox checkbox-sm" value="1">
                        <span
                            class="text-sm text-slate-600 font-medium group-hover:text-rose-600 transition-colors">Rahasia</span>
                    </label>
                </div>

                <!-- Disclaimer -->
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                    <p class="text-[10px] text-amber-700 leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i>
                        Dengan menyerahkan pengaduan ini, Anda setuju bahwa pengaduan akan diproses oleh pihak
                        kecamatan. Untuk
                        laporan terkait korupsi atau penyimpangan berat, silakan gunakan kanal resmi <a
                            href="https://lapor.go.id" target="_blank" class="font-bold underline">SP4N-LAPOR</a>.
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="complaintSubmitBtn"
                    class="btn w-full bg-rose-500 hover:bg-rose-600 text-white border-0 rounded-xl h-12 font-black shadow-lg transition-all uppercase tracking-widest text-base">
                    <i class="fas fa-flag mr-2"></i>
                    LAPOR!
                </button>
            </form>
        </div>
    </dialog>

    <!-- Survei Kepuasan Masyarakat (SKM) Modal -->
    <dialog id="surveyModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box max-w-md rounded-3xl bg-white p-0 overflow-hidden shadow-2xl border border-slate-100">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 p-6 text-white text-center relative">
                <form method="dialog">
                    <button
                        class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-teal-100 hover:text-white"><i
                            class="fas fa-times"></i></button>
                </form>
                <div
                    class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                    <i class="fas fa-heart text-2xl text-teal-100 animate-pulse"></i>
                </div>
                <h3 class="font-black text-xl leading-tight">Survei Kepuasan<br>Masyarakat</h3>
                <p class="text-[10px] text-teal-100 font-bold uppercase tracking-[0.2em] mt-2 opacity-80">
                    {{ appProfile()->region_name }}
                </p>
            </div>

            <div class="p-8 text-center bg-slate-50/50">
                <p class="text-sm text-slate-600 leading-relaxed mb-8">
                    Silakan scan QR Code di bawah ini atau klik tombol tombol untuk melakukan penilaian survey kepuasan
                    masyarakat melalui portal <strong>SUKMA JATIM</strong>.
                </p>

                <div class="bg-white p-6 rounded-3xl shadow-xl inline-block mb-8 border border-white mx-auto">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://sukma.jatimprov.go.id/fe/survey?idUser=2710"
                        alt="QR Code Survei" class="w-48 h-48 rounded-lg mx-auto">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-4">Scan QR untuk Mengisi
                    </p>
                </div>

                <div class="space-y-3">
                    <a href="https://sukma.jatimprov.go.id/fe/survey?idUser=2710" target="_blank"
                        class="btn bg-teal-600 hover:bg-teal-700 text-white btn-block rounded-2xl py-4 h-auto font-black uppercase tracking-widest text-xs shadow-lg shadow-teal-100 transition-all">
                        Lanjut ke Portal Survei <i class="fas fa-external-link-alt ml-2"></i>
                    </a>
                    <form method="dialog">
                        <button
                            class="btn btn-ghost text-slate-400 text-[10px] font-bold uppercase tracking-widest">Nanti
                            Saja</button>
                    </form>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-200/60 flex items-center justify-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.1em]">Respon Anda Membantu
                        Kami Berbenah</span>
                </div>
            </div>
        </div>
    </dialog>

    <script>
        // --- CHATBOT FAQ LOGIC (Preserved) ---
        const chatMessages = document.getElementById('chatMessages');
        const botForm = document.getElementById('publicFaqForm');
        const botInput = document.getElementById('botQuery');
        const btnMic = document.getElementById('btnMic');
        let chatState = 'FAQ'; // States: FAQ | CAPTURE_NAMA | CAPTURE_WA | CAPTURE_KEPERLUAN
        let lastUserQuery = '';
        let capturedNama = '';
        let capturedWa = '';
        let isVoiceInteraction = false;

        // Visual Feedback Util
        function showToast(message, type = 'info') {
            const toastId = 'toast-' + Date.now();
            const colors = type === 'success' ? 'bg-emerald-600' : 'bg-slate-700';

            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `fixed top-20 right-5 z-50 ${colors} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-[slideIn_0.3s_ease-out]`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span class="text-sm font-medium">${message}</span>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full', 'transition-all', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        function sendQuickChip(text) {
            botInput.value = text;
            isVoiceInteraction = false;
            botForm.dispatchEvent(new Event('submit'));
        }

        function openBotWithQuery(text) {
            const modal = document.getElementById('publicServiceModal');
            if (modal) {
                modal.showModal();
                sendQuickChip(text);
            }
        }

        function startClarification() {
            chatState = 'CAPTURE_NAMA';
            appendMessage('bot', '📋 *Tindak Lanjut Petugas*\n\nUntuk mencatat permintaan Anda secara resmi, kami memerlukan beberapa data.\n\nSilakan masukkan **Nama Lengkap** Anda.');
            botInput.placeholder = "Nama lengkap Anda...";
            botInput.type = "text";
            botInput.focus();
        }

        function appendMessage(role, text, options = null) {
            const container = document.createElement('div');
            container.className = role === 'user' ? 'flex justify-end' : 'flex items-start gap-2.5 animate-[slideUp_0.3s_ease-out]';

            if (role === 'bot') {
                let messageHtml = '';
                if (options && Array.isArray(options)) {
                    // Multi-choice / Suggestion Card
                    const buttons = options.map(opt => 
                        `<button onclick="sendQuickChip('${opt.question}')" class="btn btn-xs btn-outline btn-teal rounded-lg lowercase text-[9px] block w-full text-left mb-1 truncate">${opt.question}</button>`
                    ).join('');

                    messageHtml = `
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center shrink-0 shadow-sm border border-teal-200">
                            <i class="fas fa-robot text-teal-600 text-xs"></i>
                        </div>
                        <div class="bg-white border border-teal-100 rounded-2xl shadow-md overflow-hidden max-w-[90%]">
                            <div class="bg-teal-600 px-4 py-2 text-white flex justify-between items-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider">Beberapa Hasil Ditemukan</span>
                            </div>
                            <div class="p-4">
                                <p class="text-[10px] text-slate-500 mb-3">Mungkin ini yang Anda cari:</p>
                                ${buttons}
                                <div class="pt-3 border-t border-slate-100 mt-2">
                                    <button onclick="startClarification()" class="text-[9px] font-bold text-teal-600 hover:underline">Tidak ada di atas? Tanya Petugas</button>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (text.includes('**SOP**') || text.includes('**Jam Operasional**')) {
                    // SOP-style Card UI for confirmed answers
                    messageHtml = `
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center shrink-0 shadow-sm border border-teal-200">
                            <i class="fas fa-robot text-teal-600 text-xs"></i>
                        </div>
                        <div class="bg-white border border-teal-100 rounded-2xl shadow-md overflow-hidden max-w-[90%]">
                            <div class="bg-teal-600 px-4 py-2 text-white flex justify-between items-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider">Informasi Layanan Resmi</span>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="text-[11px] text-slate-700 leading-relaxed">
                                    ${text.replace(/\n/g, '<br>')}
                                </div>
                                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                                    <p class="text-[9px] text-slate-400 font-medium italic">Apakah informasi ini membantu?</p>
                                    <div class="flex gap-2">
                                        <button onclick="appendMessage('bot', '✅ Terima kasih! Kami senang bisa membantu.')" class="btn btn-xs btn-outline btn-success rounded-lg lowercase text-[9px]">Ya, Jelas</button>
                                        <button onclick="startClarification()" class="btn btn-xs btn-outline btn-warning rounded-lg lowercase text-[9px]">Tanya Petugas</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    messageHtml = `
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center shrink-0 shadow-sm border border-teal-200">
                            <i class="fas fa-robot text-teal-600 text-xs"></i>
                        </div>
                        <div class="bg-white border border-slate-200 text-slate-700 p-3 rounded-2xl rounded-tl-none text-[11px] leading-relaxed shadow-sm max-w-[85%] font-medium">
                            ${text.replace(/\n/g, '<br>')}
                        </div>
                    `;
                }
                container.innerHTML = messageHtml;
            } else {
                container.innerHTML = `
                    <div class="bg-teal-600 text-white p-3 rounded-2xl rounded-tr-none text-[11px] leading-relaxed shadow-md max-w-[85%] font-medium">
                        ${text}
                    </div>
                `;
            }

            chatMessages.appendChild(container);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        botForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const inputVal = botInput.value.trim();
            if (!inputVal) return;

            if (chatState === 'CAPTURE_NAMA') {
                handleNamaStep(inputVal);
                return;
            }

            if (chatState === 'CAPTURE_WA') {
                handleWaCaptureStep(inputVal);
                return;
            }

            if (chatState === 'CAPTURE_KEPERLUAN') {
                handleWaCapture(capturedWa, inputVal);
                return;
            }

            lastUserQuery = inputVal;
            appendMessage('user', inputVal);
            botInput.value = '';

            try {
                const response = await fetch(`{{ route('api.faq.search', [], false) }}?q=${encodeURIComponent(inputVal)}`);
                const data = await response.json();

                if (data.found) {
                    if (data.multiple) {
                        appendMessage('bot', 'Beberapa topik ditemukan.', data.results);
                    } else {
                        const top = data.results[0];
                        const answerText = top.jawaban || top.answer || data.answer;
                        appendMessage('bot', answerText);

                        // Trigger Voice Guide speak if modular JS is active
                        if (window.VoiceSpeech && window.VoiceState && window.VoiceState.isActive()) {
                            window.VoiceSpeech.speak(answerText);
                        }
                    }
                } else {
                    appendMessage('bot', data.answer || 'Maaf, saya tidak menemukan jawaban pasti. Ingin bertanya langsung pada petugas?');
                }
            } catch (error) {
                appendMessage('bot', 'Sepertinya ada gangguan koneksi. Coba lagi nanti ya.');
            }
        });

        function handleNamaStep(nama) {
            botInput.value = '';
            appendMessage('user', nama);

            if (nama.trim().length < 3) {
                appendMessage('bot', '⚠️ Mohon masukkan nama lengkap yang valid (minimal 3 karakter).');
                return;
            }

            capturedNama = nama.trim();
            chatState = 'CAPTURE_WA';
            appendMessage('bot', `Terima kasih, *${capturedNama}*.\n\nSekarang masukkan **Nomor WhatsApp** yang dapat dihubungi oleh petugas.`);
            botInput.placeholder = "Nomor WA: 08xxxxxxxxx";
            botInput.type = "tel";
            botInput.focus();
        }

        function handleWaCaptureStep(wa) {
            // Step 1: Capture phone number, then ask for keperluan
            botInput.value = '';
            appendMessage('user', wa);

            // Normalize WA
            let cleanWa = wa.replace(/[^0-9]/g, '');
            if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.slice(1);
            else if (!cleanWa.startsWith('62')) cleanWa = '62' + cleanWa;

            if (cleanWa.length < 10) {
                appendMessage('bot', '⚠️ Nomor WhatsApp tidak valid. Mohon masukkan nomor yang benar (minimal 10 digit).');
                return;
            }

            capturedWa = cleanWa;
            chatState = 'CAPTURE_KEPERLUAN';
            appendMessage('bot', `✅ Nomor *${wa}* tercatat.\n\nSekarang, tolong ceritakan secara singkat **keperluan atau pertanyaan** Anda untuk petugas.`);
            botInput.placeholder = "Contoh: Ingin bertanya syarat pindah domisili...";
            botInput.type = "text";
            botInput.focus();
        }

        async function handleWaCapture(wa, keperluan) {
            botInput.value = '';
            appendMessage('user', keperluan);
            appendMessage('bot', '⏳ Sedang mencatat permintaan Anda secara resmi...');

            try {
                const uraianLengkap = `[Chatbot] Nama: ${capturedNama} | Keperluan: ${keperluan}` + (lastUserQuery ? ` | Pertanyaan awal: "${lastUserQuery}"` : '');

                const response = await fetch("{{ route('public.service.submit', [], false) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        nama_pemohon: capturedNama,
                        jenis_layanan: 'Konsultasi via Chatbot',
                        category: 'pelayanan',
                        uraian: uraianLengkap,
                        whatsapp: wa,
                        source: 'chatbox',
                        is_agreed: true
                    })
                });

                let result = {};
                try { result = await response.json(); } catch(e) {}

                if (response.ok && (result.tracking_code || result.uuid || result.message)) {
                    const pin = result.tracking_code ? `\n🔑 *PIN Lacak:* ${result.tracking_code}` : '';
                    appendMessage('bot',
                        `✅ *Permintaan Resmi Tercatat!*\n\n` +
                        `👤 Nama: *${capturedNama}*\n` +
                        `📱 WhatsApp: *${wa}*\n` +
                        `📝 Keperluan: ${keperluan}` +
                        `${pin}\n\n` +
                        `Petugas akan menghubungi Anda melalui WhatsApp dalam *1x24 jam kerja*.`
                    );
                } else if (response.status === 429) {
                    appendMessage('bot', '⏳ Permintaan dari nomor ini sudah tercatat hari ini. Petugas akan segera menghubungi Anda. Terima kasih!');
                } else if (result.errors) {
                    const errMsg = Object.values(result.errors).flat().join(', ');
                    appendMessage('bot', `⚠️ Terjadi kendala validasi: ${errMsg}`);
                } else {
                    appendMessage('bot', `⚠️ ${result.message || 'Gagal menyimpan. Silakan coba lagi atau hubungi petugas langsung.'}`);
                }
            } catch (error) {
                appendMessage('bot', 'Terjadi kendala koneksi. Silakan coba lagi beberapa saat.');
            } finally {
                chatState = 'FAQ';
                capturedNama = '';
                capturedWa = '';
                botInput.placeholder = "Ketik pertanyaan Anda...";
                botInput.type = "text";
            }
        }

        function setJobType(cat, placeholder) {
            const uraianField = document.querySelector('textarea[name="uraian"]');

            // Reset and Highlight button
            document.querySelectorAll('.job-type-btn').forEach(btn => {
                btn.classList.remove('bg-teal-50', 'border-teal-500', 'ring-2', 'ring-teal-200');
                btn.classList.add('bg-white', 'border-slate-200');
            });

            const activeBtn = event.currentTarget;
            activeBtn.classList.add('bg-teal-50', 'border-teal-500', 'ring-2', 'ring-teal-200');
            activeBtn.classList.remove('bg-white', 'border-slate-200');

            uraianField.value = `KATEGORI: ${cat}\nJENIS JASA: ${placeholder}\n\n1. Wilayah: {{ appProfile()->region_level }} {{ appProfile()->region_name }}\n2. Jam Kerja: 08:00 - 17:00\n3. Info Tambahan: `;
            uraianField.focus();

            // Move cursor to end of text
            const len = uraianField.value.length;
            uraianField.setSelectionRange(len, len);
        }

        // --- PERMOHONAN LAYANAN LOGIC ---
        const permohonanModal = document.getElementById('permohonanModal');
        const submissionForm = document.getElementById('submissionForm');
        const dynamicAttachments = document.getElementById('dynamicAttachments');

        function openSubmissionModal(serviceName, requirements = '', attachmentsJson = []) {
            document.getElementById('modalServiceTitle').innerText = 'Ajukan: ' + serviceName;
            document.getElementById('inputJenisLayanan').value = serviceName;

            const jobSelection = document.getElementById('jobSelectionArea');
            const uraianField = document.querySelector('textarea[name="uraian"]');

            if (serviceName === 'Direktori Kerja') {
                if (jobSelection) jobSelection.classList.remove('hidden');
                uraianField.placeholder = "Pilih kategori di atas atau ketik detail jasa Anda di sini...";
            } else {
                if (jobSelection) jobSelection.classList.add('hidden');
                uraianField.placeholder = "Contoh: Mengajukan pembuatan KK baru karena penambahan anggota keluarga...";
            }

            // Clear and Parse Requirements
            if (dynamicAttachments) {
                dynamicAttachments.innerHTML = '';

                let reqList = [];

                // 1. Priority: Use structured JSON if available
                if (Array.isArray(attachmentsJson) && attachmentsJson.length > 0) {
                    reqList = attachmentsJson;
                }
                // 2. Fallback: Parse from text requirements
                else if (requirements) {
                    // Remove common preamble like "Syarat:" or "Persyaratan:"
                    let cleanReqs = requirements.replace(/^(Persyaratan|Syarat|SOP):\s*/i, '');

                    // Split by numeric list (1., 2., ...) or just common document names
                    let splitters = [/\d+[\.\)]\s*/, /,\s*/, /;\s*/];
                    let currentList = [cleanReqs];

                    splitters.forEach(regex => {
                        let newList = [];
                        currentList.forEach(item => {
                            newList = newList.concat(item.split(regex).filter(s => s.trim().length > 3));
                        });
                        currentList = newList;
                    });
                    reqList = currentList.map(s => s.trim()).slice(0, 5); // Limit to first 5 detected
                }

                if (reqList.length > 0) {
                    reqList.forEach(label => addAttachmentField(label));
                } else if (serviceName === 'Direktori Kerja') {
                    addAttachmentField('Foto Diri (Saat Bekerja/Depan Lokasi)');
                    addAttachmentField('Identitas (KTP)');
                } else {
                    // Fallback default fields
                    addAttachmentField('KTP / Identitas');
                    addAttachmentField('Dokumen Pendukung');
                }
            }

            if (permohonanModal) permohonanModal.showModal();
        }

        function addAttachmentField(label = '') {
            if (!dynamicAttachments) return;
            const div = document.createElement('div');
            div.className = 'form-control bg-white/60 p-2 rounded-xl border border-slate-100 flex flex-col gap-1 transition-all hover:border-teal-200';
            div.innerHTML = `
                <div class="flex justify-between items-center px-1">
                    <input type="text" name="foto_labels[]" value="${label}" 
                        class="bg-transparent border-none text-[10px] font-bold text-slate-600 focus:ring-0 p-0 w-full" 
                        placeholder="Nama Berkas (Contoh: KTP)...">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-slate-300 hover:text-rose-500 transition-colors">
                        <i class="fas fa-times-circle text-xs"></i>
                    </button>
                </div>
                <input type="file" name="foto[]" 
                    class="file-input file-input-bordered file-input-xs bg-white border-slate-200 rounded-lg w-full" 
                    accept=".jpg,.jpeg,.png,.pdf" required>
            `;
            dynamicAttachments.appendChild(div);
        }

        if (submissionForm) {
            submissionForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('btnSubmitPermohonan');
                const originalText = btn.innerHTML;

                // Normalize WA
                let waInput = this.whatsapp.value.replace(/[^0-9]/g, '');
                if (waInput.startsWith('0')) waInput = '62' + waInput.substring(1);
                if (waInput.startsWith('8')) waInput = '62' + waInput;
                this.whatsapp.value = waInput;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Sedang Mengirim...';

                try {
                    const formData = new FormData(this);
                    const response = await fetch("{{ route('public.service.submit', [], false) }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const res = await response.json();

                    if (response.ok) {
                        // Auto download receipt
                        if (res.receipt_url) {
                            Swal.fire({
                                icon: 'success',
                                title: '✅ Pengajuan Berhasil!',
                                html: `
                                    <p class="text-slate-700 mb-4">Nomor Pengajuan Anda: <strong class="text-teal-600">${res.uuid}</strong></p>
                                    <p class="text-sm text-slate-600 mb-4">Struk pengajuan sudah siap! Silakan download dan simpan untuk tracking status.</p>
                                    <div class="flex gap-3 justify-center mt-4">
                                        <a href="${res.receipt_url}" 
                                           class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold shadow-lg transition-all">
                                            <i class="fas fa-download"></i> Download Struk
                                        </a>
                                        <a href="${res.tracking_url}" 
                                           class="inline-flex items-center gap-2 px-6 py-3 bg-slate-600 hover:bg-slate-700 text-white rounded-lg font-bold shadow-lg transition-all">
                                            <i class="fas fa-search"></i> Lacak Status
                                        </a>
                                    </div>
                                `,
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#0d9488',
                                showConfirmButton: true,
                                allowOutsideClick: false,
                                width: '600px'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pengajuan Terkirim!',
                                text: 'Terima kasih, berkas Anda akan segera diverifikasi oleh petugas kami.',
                                confirmButtonColor: '#0d9488',
                                timer: 5000
                            });
                        }

                        // 🔊 AUDIO FEEDBACK: Announce success
                        if (window.VoiceSpeech && window.VoiceState && window.VoiceState.isActive()) {
                            window.VoiceSpeech.speak("Pengajuan berhasil dikirim. Nomor pengajuan Anda adalah " + res.uuid);
                        }

                        this.reset();
                        permohonanModal.close();
                    } else {
                        throw new Error(res.message || 'Terjadi kesalahan saat mengirim.');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengirim',
                        text: error.message,
                        confirmButtonColor: '#0d9488'
                    });

                    // 🔊 AUDIO FEEDBACK: Announce error
                    if (window.VoiceSpeech && window.VoiceState && window.VoiceState.isActive()) {
                        window.VoiceSpeech.speak("Gagal mengirim pengajuan. " + error.message);
                    }
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // Slide-up animation
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes slideInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes slideIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        `;
        document.head.appendChild(style);

        // Autofocus on open
        document.getElementById('publicServiceModal').addEventListener('show', () => {
            setTimeout(() => botInput.focus(), 100);
        });

        // --- MODULAR VOICE GUIDE INTEGRATION ---
        window.APP_WILAYAH_NAMA = {!! json_encode(optional(appProfile())->region_name ?? 'Wilayah') !!};
        window.APP_FAQ_KEYWORDS = {!! json_encode($faqKeywords ?? []) !!};

        // --- COMPLAINT FORM LOGIC ---
        // Character counter
        const complaintMessage = document.getElementById('complaintMessage');
        const charCount = document.getElementById('charCount');
        if (complaintMessage && charCount) {
            complaintMessage.addEventListener('input', function () {
                charCount.textContent = this.value.length;
                if (this.value.length < 20) {
                    charCount.classList.add('text-rose-500');
                } else {
                    charCount.classList.remove('text-rose-500');
                }
            });
        }

        // Complaint form submission
        document.getElementById('complaintForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = document.getElementById('complaintSubmitBtn');
            const message = form.uraian.value.trim();
            const wa = form.whatsapp.value.trim();
            const jenisPengaduan = form.jenis_pengaduan.value;
            const title = form.judul_pengaduan.value.trim();

            // Validation
            if (!jenisPengaduan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kategori',
                    text: 'Mohon pilih kategori pengaduan terlebih dahulu',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            if (!title) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Judul Diperlukan',
                    text: 'Mohon masukkan judul pengaduan',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            if (message.length < 20) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pesan Terlalu Pendek',
                    text: 'Mohon jelaskan pengaduan Anda minimal 20 karakter',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            if (!wa.match(/^[0-9]+$/)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor WhatsApp Tidak Valid',
                    text: 'Mohon masukkan nomor WhatsApp yang valid (hanya angka)',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            // Build privacy flags from checkboxes
            const _isAnonim = form.querySelector('[name="is_anonim"]')?.checked;
            const _isRahasia = form.querySelector('[name="is_rahasia"]')?.checked;
            let _privTags = '';
            if (_isAnonim) _privTags += '[ANONIM]';
            if (_isRahasia) _privTags += '[RAHASIA]';

            // Build combined uraian string WITHOUT touching the visible textarea
            // This prevents [Aspirasi] from appearing in the textarea on any click
            const combinedUraian = `[${jenisPengaduan}]${_privTags ? ' ' + _privTags : ''} ${title}\n\n${message}`;

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';

            try {
                // Use FormData and override uraian & jenis_layanan directly
                const formData = new FormData(form);
                formData.set('uraian', combinedUraian);
                formData.set('jenis_layanan', `Pengaduan - ${jenisPengaduan}`);

                // PENTING: Gunakan URL relatif (bukan absolut) agar cookie sesi
                // dikirim dengan benar oleh Chrome Android (mirip pola submissionForm)
                const response = await fetch("{{ route('public.service.submit', [], false) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Cek Content-Type sebelum parse JSON untuk mencegah SyntaxError
                let result = {};
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    // Server mengembalikan non-JSON (misal: halaman error 419/500)
                    const rawText = await response.text();
                    console.error('Non-JSON response:', response.status, rawText.substring(0, 200));
                    throw new Error(`Server error ${response.status}. Coba refresh halaman dan kirim ulang.`);
                }

                if (response.ok && (result.success || result.tracking_code || result.uuid)) {
                    // Success
                    const trackingId = result.tracking_code || result.uuid || 'baru';
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaduan Terkirim! 🎉',
                        html: `<p class="text-sm text-slate-600 mb-3">Terima kasih telah menyampaikan pengaduan. Kami akan memprosesnya segera.</p>
                               <div class="bg-rose-50 p-3 rounded-lg">
                                   <p class="text-xs text-rose-600 font-bold">NO. PENGADUAN</p>
                                   <p class="text-lg font-black text-rose-600">${trackingId}</p>
                               </div>
                               <p class="text-xs text-slate-400 mt-2">Notifikasi akan dikirim via WhatsApp</p>`,
                        confirmButtonColor: '#f43f5e',
                        confirmButtonText: 'Tutup'
                    });

                    // Close modal and reset form
                    document.getElementById('complaintModal').close();
                    form.reset();
                    if (charCount) charCount.textContent = '0';

                } else if (result.type === 'security_referral') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        html: `<p class="text-sm text-slate-600">${result.message}</p>
                               <a href="${result.link}" target="_blank" class="btn btn-sm btn-warning mt-3">Buka SP4N-LAPOR</a>`,
                        confirmButtonColor: '#f59e0b'
                    });
                } else if (result.type === 'siak_referral') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi Layanan',
                        html: `<p class="text-sm text-slate-600">${result.message}</p>
                               <a href="${result.link}" target="_blank" class="btn btn-sm btn-info mt-3">Buka Portal SIAK</a>`,
                        confirmButtonColor: '#0ea5e9'
                    });
                } else if (result.errors) {
                    const errors = Object.values(result.errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errors,
                        confirmButtonColor: '#f43f5e'
                    });
                } else {
                    throw new Error(result.message || 'Gagal menyimpan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Complaint submission error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: error.message || 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                    confirmButtonColor: '#f43f5e'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Kirim Pengaduan';
            }
        });

        // Pre-fill WhatsApp from stored value if available
        const storedWa = localStorage.getItem('user_wa');

        // Check for WhatsApp in URL parameter (from WhatsApp bot)
        // Parse standard query parameters
        const urlParams = new URLSearchParams(window.location.search);
        
        // Also parse query parameters hidden behind a hash e.g. #pengaduan?nama=Panda&no_hp=123
        const hashParts = window.location.hash.split('?');
        const hashParams = new URLSearchParams(hashParts.length > 1 ? hashParts[1] : '');

        const waFromUrl = urlParams.get('wa') || hashParams.get('wa');
        const namaFromUrl = urlParams.get('nama') || hashParams.get('nama') || urlParams.get('name') || hashParams.get('name');
        const noHpFromUrl = urlParams.get('no_hp') || hashParams.get('no_hp') || waFromUrl;
        const kategoriFromUrl = urlParams.get('kategori') || hashParams.get('kategori');

        // Priority: URL parameter > localStorage > empty
        const defaultWa = noHpFromUrl || storedWa || '';

        // Auto-fill name from URL parameter
        if (namaFromUrl) {
            const nameFields = ['inlineComplaintName', 'complaintName', 'nama_pemohon'];
            nameFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = decodeURIComponent(namaFromUrl);
            });
        }

        // Auto-fill WhatsApp from URL parameter (no_hp)
        if (noHpFromUrl) {
            const waFields = ['inlineComplaintWa', 'complaintWa', 'whatsapp'];
            waFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = decodeURIComponent(noHpFromUrl);
            });
        }
        
        // Auto-fill Kategori from URL parameter
        if (kategoriFromUrl) {
            const targetVal = decodeURIComponent(kategoriFromUrl).toLowerCase();
            document.querySelectorAll('[name="jenis_pengaduan"]').forEach(el => {
                if (el.tagName === 'SELECT') {
                    for(let i = 0; i < el.options.length; i++) {
                        if(el.options[i].value.toLowerCase().includes(targetVal) || el.options[i].text.toLowerCase().includes(targetVal)) {
                            el.selectedIndex = i;
                            break;
                        }
                    }
                } else {
                    el.value = decodeURIComponent(kategoriFromUrl);
                }
            });
        }

        if (document.getElementById('complaintWa') && !document.getElementById('complaintWa').value) {
            document.getElementById('complaintWa').value = defaultWa;
        }

        // Trigger the modal if the hash is exactly or starts with #pengaduan
        if (window.location.hash.startsWith('#pengaduan')) {
            const complaintModal = document.getElementById('complaintModal');
            if (complaintModal) {
                // Short delay ensures rendering is complete before opening dialog
                setTimeout(() => {
                    complaintModal.showModal();
                    // Optionally focus on the message field since name & phone are already filled
                    if (namaFromUrl && noHpFromUrl) {
                        const uraianEl = document.getElementById('complaintMessage');
                        if(uraianEl) uraianEl.focus();
                    }
                }, 400);
            }
        }

        // --- INLINE COMPLAINT FORM LOGIC ---
        // Character count for inline form
        const inlineComplaintMessage = document.getElementById('inlineComplaintMessage');
        const inlineCharCount = document.getElementById('inlineCharCount');
        const inlineCharCountLabel = document.getElementById('inlineCharCountLabel');
        if (inlineComplaintMessage && inlineCharCount) {
            inlineComplaintMessage.addEventListener('input', function () {
                const len = this.value.length;
                inlineCharCount.textContent = len;

                // Visual feedback for validation
                if (len > 0 && len < 20) {
                    inlineCharCount.classList.add('text-rose-500', 'font-bold');
                    inlineCharCountLabel.classList.add('text-rose-500');
                    inlineCharCountLabel.textContent = 'Min. 20 karakter';
                } else if (len >= 20) {
                    inlineCharCount.classList.remove('text-rose-500', 'font-bold');
                    inlineCharCount.classList.add('text-teal-500', 'font-bold');
                    inlineCharCountLabel.classList.remove('text-rose-500');
                    inlineCharCountLabel.classList.add('text-teal-600');
                    inlineCharCountLabel.textContent = '✓ Valid';
                } else {
                    inlineCharCount.classList.remove('text-rose-500', 'font-bold', 'text-teal-500');
                    inlineCharCountLabel.classList.remove('text-rose-500', 'text-teal-600');
                    inlineCharCountLabel.textContent = 'Min. 20 karakter';
                }
            });
        }

        // === FILE PREVIEW LOGIC ===
        const inlineFileInput = document.getElementById('inlineComplaintFile');
        if (inlineFileInput) {
            inlineFileInput.addEventListener('change', function () {
                const preview = document.getElementById('inlinePhotoPreview');
                const fileCount = document.getElementById('inlineFileCount');
                const fileLabel = document.getElementById('inlineFileLabel');
                if (!preview) return;
                preview.innerHTML = '';

                const files = Array.from(this.files);

                if (fileCount) {
                    fileCount.textContent = files.length;
                    if (files.length > 0) {
                        fileCount.classList.remove('hidden');
                        fileCount.classList.add('flex');
                    } else {
                        fileCount.classList.add('hidden');
                        fileCount.classList.remove('flex');
                    }
                }
                if (fileLabel) {
                    fileLabel.textContent = files.length > 0 ? `${files.length} file dipilih` : 'Upload Lampiran';
                }

                files.forEach((file) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative group';
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            wrapper.innerHTML = `<img src="${ev.target.result}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shadow-sm" alt="preview">`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        wrapper.innerHTML = `
                            <div class="w-16 h-16 bg-slate-100 rounded-xl border border-slate-200 flex flex-col items-center justify-center shadow-sm">
                                <i class="fas fa-file-pdf text-rose-500 text-xl mb-1"></i>
                                <span class="text-[8px] text-slate-500 font-bold uppercase">PDF</span>
                            </div>`;
                    }
                    preview.appendChild(wrapper);
                });
            });
        }

        const complaintFileInput = document.getElementById('complaintAttachment');
        if (complaintFileInput) {
            complaintFileInput.addEventListener('change', function () {
                const preview = document.getElementById('complaintPhotoPreview');
                if (!preview) return;
                preview.innerHTML = '';

                const files = Array.from(this.files);
                files.forEach((file) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative group';
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            wrapper.innerHTML = `<img src="${ev.target.result}" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shadow-sm" alt="preview">`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        wrapper.innerHTML = `
                            <div class="w-14 h-14 bg-slate-100 rounded-xl border border-slate-200 flex flex-col items-center justify-center shadow-sm">
                                <i class="fas fa-file-pdf text-rose-500 text-lg mb-1"></i>
                                <span class="text-[8px] text-slate-500 font-bold uppercase">PDF</span>
                            </div>`;
                    }
                    preview.appendChild(wrapper);
                });
            });
        }
        // === END FILE PREVIEW LOGIC ===

        // Inline complaint form submission
        document.getElementById('inlineComplaintForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = document.getElementById('inlineComplaintSubmitBtn');
            const message = form.uraian.value.trim();
            const wa = form.whatsapp.value.trim();
            const jenisPengaduan = form.jenis_pengaduan.value;
            const title = form.judul_pengaduan.value.trim();

            // Validation
            if (!jenisPengaduan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kategori',
                    text: 'Mohon pilih kategori pengaduan terlebih dahulu',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            if (!title) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Judul Diperlukan',
                    text: 'Mohon masukkan judul pengaduan',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            if (message.length < 20) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pesan Terlalu Pendek',
                    text: 'Mohon jelaskan pengaduan Anda minimal 20 karakter',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            if (!wa.match(/^[0-9]+$/)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor WhatsApp Tidak Valid',
                    text: 'Mohon masukkan nomor WhatsApp yang valid (hanya angka)',
                    confirmButtonColor: '#f43f5e'
                });
                return;
            }

            // Build privacy flags from checkboxes (inline form)
            const _isAnonim2 = form.querySelector('[name="is_anonim"]')?.checked;
            const _isRahasia2 = form.querySelector('[name="is_rahasia"]')?.checked;
            let _privTags2 = '';
            if (_isAnonim2) _privTags2 += '[ANONIM]';
            if (_isRahasia2) _privTags2 += '[RAHASIA]';

            // Combine title and message, set jenis_layanan
            form.uraian.value = `[${jenisPengaduan}]${_privTags2 ? ' ' + _privTags2 : ''} ${title}\n\n${message}`;
            form.jenis_layanan.value = `Pengaduan - ${jenisPengaduan}`;

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';

            try {
                const formData = new FormData(form);

                // PENTING: Gunakan URL relatif (bukan absolut) agar cookie sesi
                // dikirim dengan benar oleh Chrome Android (mirip pola submissionForm)
                const response = await fetch("{{ route('public.service.submit', [], false) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Cek Content-Type sebelum parse JSON untuk mencegah SyntaxError
                let result = {};
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    const rawText = await response.text();
                    console.error('Non-JSON response:', response.status, rawText.substring(0, 200));
                    throw new Error(`Server error ${response.status}. Coba refresh halaman dan kirim ulang.`);
                }

                if (response.ok && (result.success || result.tracking_code || result.uuid)) {
                    // Success
                    const trackingId = result.tracking_code || result.uuid || 'baru';

                    // Store WhatsApp for future use
                    localStorage.setItem('user_wa', wa);

                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaduan Terkirim! 🎉',
                        html: `<p class="text-sm text-slate-600 mb-3">Terima kasih telah menyampaikan pengaduan. Kami akan memprosesnya segera.</p>
                               <div class="bg-rose-50 p-3 rounded-lg">
                                   <p class="text-xs text-rose-600 font-bold">NO. PENGADUAN</p>
                                   <p class="text-lg font-black text-rose-600">${trackingId}</p>
                               </div>
                               <p class="text-xs text-slate-400 mt-2">Notifikasi akan dikirim via WhatsApp</p>`,
                        confirmButtonColor: '#f43f5e',
                        confirmButtonText: 'Tutup'
                    });

                    // Reset form
                    form.reset();
                    if (inlineCharCount) inlineCharCount.textContent = '0';

                } else if (result.type === 'security_referral') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        html: `<p class="text-sm text-slate-600">${result.message}</p>
                               <a href="${result.link}" target="_blank" class="btn btn-sm btn-warning mt-3">Buka SP4N-LAPOR</a>`,
                        confirmButtonColor: '#f59e0b'
                    });
                } else if (result.type === 'siak_referral') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi Layanan',
                        html: `<p class="text-sm text-slate-600">${result.message}</p>
                               <a href="${result.link}" target="_blank" class="btn btn-sm btn-info mt-3">Buka Portal SIAK</a>`,
                        confirmButtonColor: '#0ea5e9'
                    });
                } else if (result.errors) {
                    const errors = Object.values(result.errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errors,
                        confirmButtonColor: '#f43f5e'
                    });
                } else {
                    throw new Error(result.message || 'Gagal menyimpan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Inline complaint submission error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: error.message || 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                    confirmButtonColor: '#f43f5e'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Kirim Pengaduan';
            }
        });

        // Pre-fill inline form with stored WhatsApp or URL parameter
        if (!document.getElementById('inlineComplaintWa').value) {
            document.getElementById('inlineComplaintWa').value = defaultWa;
        }

        // Scroll to pengaduan section if wa parameter exists
        if (waFromUrl) {
            const pengaduanSection = document.getElementById('pengaduan');
            if (pengaduanSection) {
                setTimeout(() => {
                    pengaduanSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 500);
            }
        }

        // --- TRACKING LOGIC ---
        function handleTracking() {
            const input = document.getElementById('trackingInput').value.trim();
            if (!input) {
                showToast('Mohon masukkan nomor WA atau ID Berkas', 'info');
                return;
            }
            // Redirect to tracking page
            window.location.href = '{{ route('public.tracking') }}?q=' + encodeURIComponent(input);
        }

        // --- MOBILE BOTTOM BAR LOGIC ---
        function toggleMobileMenu() {
            // Placeholder for any mobile menu logic if needed
        }

        // --- SURVEY MODAL LOGIC ---
        function openSurveyModal() {
            const modal = document.getElementById('surveyModal');
            if (modal) {
                modal.showModal();

                // 🔊 AUDIO FEEDBACK: Option for Voice Guide
                if (window.VoiceSpeech && window.VoiceState && window.VoiceState.isActive()) {
                    window.VoiceSpeech.speak("Silakan sampaikan kepuasan Anda melalui survei kami. Anda dapat memindai kode QR yang muncul atau mengeklik tombol untuk lanjut ke portal survei.");
                }
            }
        }
    </script>

    @include('layouts.partials.public.bottom-bar')

    <script>
        const desasData = {
            @foreach($desas as $desa)
                @php 
                    $slug = strtolower(str_replace(' ', '', $desa->nama_desa));
                @endphp
                "{{ strtoupper($desa->nama_desa) }}": "https://{{ $slug }}.tatadesa.com",
            @endforeach
        };

        // Only initialize map if container exists (not all pages have the map)
        document.addEventListener('DOMContentLoaded', function () {
            const mapContainer = document.getElementById('mapContainer');
            if (!mapContainer) {
                console.log('Map container not found on this page - skipping map initialization');
                return;
            }

            // Harmonic Professional Palette (Earthy & Teal Govt Tones)
            const villageColors = [
                '#0f766e', '#0369a1', '#1d4ed8', '#4338ca', '#6d28d9',
                '#7e22ce', '#a21caf', '#be185d', '#b91c1c', '#c2410c',
                '#b45309', '#a16207', '#4d7c0f', '#15803d', '#166534',
                '#3f6212', '#115e59'
            ];

            // Initialize Map with smooth motion
            const map = L.map('mapContainer', {
                center: [{{ appProfile()->map_latitude ?? -7.78 }}, {{ appProfile()->map_longitude ?? 113.47 }}],
                zoom: 13,
                scrollWheelZoom: false,
                attributionControl: false,
                zoomControl: false,
                doubleClickZoom: false, // Disabled to prevent zooming when opening portal
                zoomSnap: 0.5,
                zoomDelta: 0.5
            });

            L.control.zoom({
                position: 'topright'
            }).addTo(map);

            // --- RESET VIEW CONTROL ---
            let initialBounds = null;

            const ResetControl = L.Control.extend({
                options: {
                    position: 'topright'
                },
                onAdd: function (map) {
                    const container = L.DomUtil.create('div', 'leaflet-control-reset');
                    container.title = "Reset Zoom & Posisi";
                    container.innerHTML = '<i class="fas fa-sync-alt"></i>';

                    L.DomEvent.on(container, 'click', function (e) {
                        L.DomEvent.stopPropagation(e);
                        if (initialBounds) {
                            map.flyToBounds(initialBounds, {
                                padding: [100, 100],
                                duration: 1.5,
                                easeLinearity: 0.25
                            });
                        } else {
                            map.flyTo([{{ appProfile()->map_latitude ?? -7.78 }}, {{ appProfile()->map_longitude ?? 113.47 }}], 13.5, {
                                duration: 1.5
                            });
                        }
                    });
                    return container;
                }
            });
            map.addControl(new ResetControl());

            // Cleanest Basemap (Voyager style)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(map);

            // Overlay labels for context at higher zoom
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                opacity: 0.6
            }).addTo(map);

            // --- CONFIG & UTILS ---
            const activeRegionName = "{{ strtoupper(appProfile()->region_name) }}";
            const geoBaseDir = "/data/geo";

            // Helper to find name in various GeoJSON property schemes
            const getGeoName = (props) => {
                return (props.NM_KEC || props.nm_kecamatan || props.name || props.NAMOBJ || props.village_name || "").toUpperCase();
            };

            // --- LAYER 1: KECAMATAN OUTER GLOW (Dynamic Filtering) ---
            fetch(`${geoBaseDir}/layer_kecamatan.geojson`)
                .then(res => res.json())
                .then(data => {
                    // Filter features to match the active region name from settings
                    const filteredFeatures = data.features.filter(f => {
                        const featName = getGeoName(f.properties);
                        return featName.includes(activeRegionName) || activeRegionName.includes(featName);
                    });

                    // Use filtered data if found, otherwise use original as fallback
                    const renderData = filteredFeatures.length > 0 ? { ...data, features: filteredFeatures } : data;

                    // Shadow/Glow Layer
                    L.geoJSON(renderData, {
                        style: {
                            color: '#0d9488',
                            weight: 15,
                            opacity: 0.05,
                            fill: false,
                            interactive: false
                        }
                    }).addTo(map);

                    // Main Boundary
                    L.geoJSON(renderData, {
                        style: {
                            color: '#1e293b',
                            weight: 4,
                            opacity: 0.8,
                            dashArray: '1, 12',
                            lineCap: 'round',
                            fill: false,
                            interactive: false
                        }
                    }).addTo(map);

                    // If we have a specific region, adjust initial zoom to it
                    if (filteredFeatures.length > 0) {
                        const bounds = L.geoJSON(renderData).getBounds();
                        initialBounds = bounds;
                        map.fitBounds(bounds, {
                            padding: [100, 100]
                        });
                    }
                })
                .catch(err => console.error("Error loading Kecamatan layer:", err));

            // --- LAYER 2: VILLAGES / DESA (Interactive) ---
            let desaLayer;
            fetch(`${geoBaseDir}/layer_desa.geojson`)
                .then(res => res.json())
                .then(data => {
                    desaLayer = L.geoJSON(data, {
                        style: function (feature) {
                            const colorIndex = data.features.indexOf(feature) % villageColors.length;
                            return {
                                fillColor: villageColors[colorIndex],
                                fillOpacity: 0.25,
                                color: 'white',
                                weight: 1.5,
                                className: 'premium-desa-path'
                            };
                        },
                        onEachFeature: function (feature, layer) {
                            const nama = getGeoName(feature.properties);
                            const slug = nama.toLowerCase().replace(/\s+/g, '');
                            const url = `https://${slug}.tatadesa.com`;

                            // Ensure no popup binds to this layer
                            layer.unbindPopup();

                            layer.on({
                                mouseover: function (e) {
                                    const l = e.target;
                                    l.setStyle({
                                        fillOpacity: 0.6,
                                        weight: 3,
                                        color: '#fff',
                                        fillColor: '#0d9488'
                                    });

                                    layer.bindTooltip(`
                                    <div class="px-3 py-2 text-center">
                                        <p class="text-[8px] font-bold text-teal-400 uppercase tracking-widest mb-0.5">Wilayah Desa</p>
                                        <p class="text-sm font-black text-white mb-1.5">${nama}</p>
                                        <div class="bg-slate-800/80 rounded-lg p-1.5 border border-slate-600/50 flex flex-col gap-1 text-left">
                                            <p class="text-[8px] text-slate-300 flex items-center gap-1.5"><i class="fas fa-hand-pointer text-teal-400"></i> Single-Klik = Zoom</p>
                                            <p class="text-[8px] text-slate-300 flex items-center gap-1.5"><i class="fas fa-mouse text-amber-400"></i> Double-Klik = Portal Desa</p>
                                        </div>
                                    </div>
                                `, {
                                        sticky: true,
                                        className: 'premium-tooltip',
                                        direction: 'top',
                                        offset: [0, -10]
                                    }).openTooltip();

                                    l.bringToFront();
                                },
                                mouseout: function (e) {
                                    desaLayer.resetStyle(e.target);
                                },
                                click: function (e) {
                                    e.originalEvent.stopPropagation();
                                    map.flyToBounds(e.target.getBounds(), {
                                        padding: [80, 80],
                                        duration: 1.2
                                    });
                                },
                                dblclick: function (e) {
                                    e.originalEvent.stopPropagation();
                                    window.open(url, '_blank');
                                }
                            });
                        }
                    }).addTo(map);

                    // Close any stray popups
                    map.on('popupopen', function() {
                        map.closePopup();
                    });

                    // Initial fit to villages if no kecamatan bounds set yet
                    if (!desaLayer.getBounds().isEmpty()) {
                        if (!initialBounds) initialBounds = desaLayer.getBounds();
                        map.fitBounds(desaLayer.getBounds(), {
                            padding: [60, 60]
                        });
                    }
                })
                .catch(err => console.error("Error loading Desa layer:", err));

            // --- LAYER 3: PULSING POI ---
            fetch(`${geoBaseDir}/layer_poi.geojson`)
                .then(res => res.json())
                .then(data => {
                    L.geoJSON(data, {
                        pointToLayer: function (feature, latlng) {
                            const icon = L.divIcon({
                                className: 'poi-pulse-wrapper',
                                html: `
                                <div class="pulse-ring"></div>
                                <div class="pulse-core shadow-2xl">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="poi-label">${feature.properties.name}</div>
                            `,
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            });
                            return L.marker(latlng, { icon: icon });
                        },
                        onEachFeature: function (feature, layer) {
                            layer.on('click', () => {
                                window.open(feature.properties.map_url, '_blank');
                            });
                        }
                    }).addTo(map);
                });
        }); // End DOMContentLoaded wrapper
    </script>

    <style>
        /* Pulse Animation */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.33);
                opacity: 0.8;
            }

            80%,
            100% {
                opacity: 0;
                transform: scale(2.5);
            }
        }

        .poi-pulse-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pulse-ring {
            position: absolute;
            width: 40px;
            height: 40px;
            background: #0d9488;
            border-radius: 50%;
            animation: pulse-ring 2s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
        }

        .pulse-core {
            position: relative;
            width: 32px;
            height: 32px;
            background: #0d9488;
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            z-index: 2;
        }

        .poi-label {
            position: absolute;
            top: 40px;
            background: rgba(15, 23, 42, 0.9);
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Tooltip & Popup Stylings */
        .premium-tooltip {
            background: #0f172a !important;
            border: none !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
            border-radius: 12px !important;
            padding: 0 !important;
        }

        .premium-tooltip:before {
            border-top-color: #0f172a !important;
        }

        .premium-leaflet-popup .leaflet-popup-content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 28px !important;
            padding: 0 !important;
            overflow: hidden;
            border: 1px solid white;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25) !important;
        }

        .premium-leaflet-popup .leaflet-popup-content {
            margin: 0 !important;
            width: 280px !important;
        }

        .premium-popup-card {
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        .popup-header {
            background: linear-gradient(135deg, #0d9488 0%, #0f172a 100%);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .text-box h4 {
            font-weight: 900;
            font-size: 16px;
            margin: 0;
            line-height: 1.2;
        }

        .text-box span {
            font-size: 10px;
            font-weight: 600;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .popup-body {
            padding: 20px;
        }

        .popup-body p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .popup-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: #0d9488;
            color: white !important;
            text-decoration: none !important;
            padding: 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 800;
            transition: all 0.3s;
        }

        .popup-btn:hover {
            background: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 148, 136, 0.2);
        }

        .premium-desa-path {
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            outline: none !important;
        }

        /* Remove focus outline around map elements */
        .leaflet-container :focus {
            outline: none !important;
        }

        path.leaflet-interactive:focus {
            outline: none !important;
        }
    </style>

    <script src="{{ asset('voice-guide/min/voice.bundle.min.js') }}?v=3.3"></script>

    {{-- Fix: Reset dark mode and close modals on page load --}}
    <script>
        (function () {
            // Reset accessibility theme to light on landing page
            const html = document.documentElement;
            const savedTheme = html.getAttribute('data-theme');
            if (savedTheme === 'dark' || savedTheme === 'high-contrast') {
                html.removeAttribute('data-theme');
                html.setAttribute('data-theme', 'light');
            }

            // Clear problematic a11y settings from localStorage
            const a11y = localStorage.getItem('a11y');
            if (a11y) {
                try {
                    const parsed = JSON.parse(a11y);
                    if (parsed.theme === 'dark' || parsed.theme === 'high-contrast') {
                        parsed.theme = 'light';
                        localStorage.setItem('a11y', JSON.stringify(parsed));
                    }
                } catch (e) {
                    // Invalid JSON, ignore
                }
            }

            // Close any open native dialog modals
            document.querySelectorAll('dialog[open]').forEach(function (dialog) {
                dialog.close();
            });

            // CRITICAL: Remove DaisyUI modal backdrop elements
            document.querySelectorAll('.modal-backdrop, [class*="modal-"][class*="backdrop"]').forEach(function (backdrop) {
                backdrop.remove();
            });

            // Also remove any fixed position overlays that might be blocking
            document.querySelectorAll('.fixed.inset-0, .fixed.top-0.left-0.right-0.bottom-0').forEach(function (overlay) {
                if (overlay.style.zIndex > 999 || overlay.classList.contains('backdrop')) {
                    overlay.remove();
                }
            });
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.hero-swiper', {
                loop: true,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 7000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
</body>

</html>