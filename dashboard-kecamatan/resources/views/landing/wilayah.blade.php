<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
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
    <meta name="geo.position" content="-7.8;113.3">
    <meta name="ICBM" content="-7.8, 113.3">

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

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Minified Shared UI Styles -->
    <link rel="stylesheet" href="{{ asset('css/min/common-map.min.css') }}">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
        "latitude": "-7.8",
        "longitude": "113.3"
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
</head>

<body class="bg-slate-50">

    @include('layouts.partials.public.navbar')

    @include('layouts.partials.public.announcements')

    <!-- Hero Section Settings Integration -->
    @if($heroBg)
        <style>
            .hero-dynamic-bg {
                position: absolute;
                inset: 0;
                background-image: url('{{ $heroBg }}');
                background-size: cover;
                background-position: center;
                /* Logic: 14% Transparansi = 86% Intensity */
                opacity:
                    {{ 1 - (($bgOpacity ?? 0) / 100) }}
                ;
                filter: blur({{ $bgBlur ?? 0 }}px);
                z-index: 10;
                /* Fade effect: Scenery strictly on the left (behind text), fully clean on the right */
                -webkit-mask-image: linear-gradient(to right, black 5%, rgba(0, 0, 0, 0.4) 35%, transparent 55%);
                mask-image: linear-gradient(to right, black 5%, rgba(0, 0, 0, 0.4) 35%, transparent 55%);
            }

            .hero-text-reveal {
                animation: heroFocusReveal 1.5s cubic-bezier(0.16, 1, 0.3, 1) both;
                animation-delay: 0.8s;
            }

            @keyframes heroFocusReveal {
                from {
                    opacity: 0;
                    filter: blur(10px);
                }

                to {
                    opacity: 1;
                    filter: blur(0);
                }
            }
        </style>
    @endif

    <!-- Page Header -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 bg-slate-900">
            @if($isHeroActive && $heroImage)
                <img src="{{ asset('storage/' . $heroImage) }}" alt="{{ $heroImageAlt }}"
                    class="w-full h-full object-cover opacity-30">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <div
                class="inline-flex items-center gap-2 bg-teal-500/10 border border-teal-500/20 rounded-full px-4 py-1.5 mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                <span class="text-teal-300 text-[10px] font-bold uppercase tracking-widest">Jelajah Potensi Desa</span>
            </div>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 tracking-tight">
                Wilayah Administrator <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">
                    {{ appProfile()->region_name }}
                </span>
            </h1>

            <p class="text-slate-400 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                Menelusuri batas wilayah administrasi, data demografi desa, serta potensi ekonomi kreatif warga dalam
                satu peta terintegrasi.
            </p>
        </div>
    </section>



    <!-- Section: Jelajah Wilayah (NEW) -->
    <div class="w-full h-24 bg-gradient-to-b from-white to-slate-50"></div>
    <div id="jelajah" class="py-24 bg-slate-50 overflow-hidden relative">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 bg-teal-50 text-teal-700 px-4 py-2 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Eksplorasi Wilayah</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-slate-800 mb-4">Jelajah Desa di Kecamatan</h2>
                <p class="text-slate-500 max-w-2xl mx-auto font-medium leading-relaxed">
                    Klik pada area desa untuk mengunjungi portal resmi masing-masing wilayah.
                </p>
            </div>

            <div class="relative group mt-8">
                <!-- Map Container -->
                <div id="mapContainer"
                    class="w-full h-[500px] md:h-[650px] rounded-[3rem] shadow-2xl border-8 border-white overflow-hidden relative">
                </div>

                <!-- Map Legend/Overlay -->
                <div class="absolute bottom-10 left-10 z-30 hidden md:block">
                    <div
                        class="bg-white/90 backdrop-blur-xl p-6 rounded-[2rem] shadow-2xl border border-white max-w-xs">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-sm">Peta Interaktif</h4>
                                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">
                                    {{ appProfile()->region_level }} {{ appProfile()->region_name }}
                                </p>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed mb-4">
                            Warna pada peta menunjukkan batasan wilayah masing-masing desa. Klik area desa untuk info
                            lebih lanjut.
                        </p>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-700">
                                <span class="w-3 h-3 rounded-sm bg-teal-500/50 border border-teal-600"></span> Batas
                                Desa
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-700">
                                <span class="w-3 h-3 rounded-full bg-teal-600"></span> Pusat Desa
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fallback for Small Mobile / Data Loading -->
            <div id="mapFallback" class="hidden mt-8 grid grid-cols-2 gap-3">
                @foreach($desas as $desa)
                    <a href="{{ $desa->website ?? '#' }}" target="_blank"
                        class="bg-white p-4 rounded-2xl border border-slate-200 text-center">
                        <span class="text-[10px] font-black text-slate-700">{{ $desa->nama_desa }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Background Ornaments -->
        <div
            class="absolute top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 w-64 h-64 bg-teal-200/20 rounded-full blur-3xl">
        </div>
        <div
            class="absolute bottom-0 right-0 translate-y-1/2 translate-x-1/2 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl">
        </div>
    </div>










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

    <!-- Floating Action Button -->
    <div class="fixed bottom-5 right-5 z-[60] group">
        <div class="absolute bottom-full right-0 mb-3 hidden group-hover:block transition-all animate-bounce">
            <span
                class="bg-teal-600 text-white text-xs px-3 py-1 rounded-full shadow-lg whitespace-nowrap italic">Hubungi
                Kami</span>
        </div>
        <button onclick="document.getElementById('publicServiceModal').showModal()"
            class="btn btn-circle bg-teal-600 hover:bg-teal-700 border-0 shadow-xl w-16 h-16 transform transition-transform hover:scale-110">
            <i class="fas fa-message text-white text-2xl"></i>
        </button>
    </div>

    <!-- Service Submission Modal (PERFECTED & COMPACT) -->
    <dialog id="permohonanModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box max-w-2xl rounded-3xl bg-white p-0 overflow-hidden shadow-2xl border border-slate-100">
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

            <form id="submissionForm" class="p-6 space-y-5 bg-slate-50/30">
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
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">📦
                                Cek Syarat KTP</button>
                            <button onclick="sendQuickChip('KK')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">👨‍👩‍👧‍👦
                                Cek Syarat KK</button>
                            <button onclick="sendQuickChip('Akte')"
                                class="btn btn-xs bg-white hover:bg-teal-50 text-teal-600 border-teal-200 rounded-full font-medium px-3 normal-case shadow-sm">📄
                                Syarat Akte</button>
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
        let chatState = 'FAQ'; // 'FAQ' or 'CAPTURE_WA'
        let lastUserQuery = '';
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
            chatState = 'CAPTURE_WA';
            appendMessage('bot', 'Baik, untuk bantuan lebih lanjut atau klarifikasi petugas, mohon masukkan **Nomor WhatsApp** Anda di bawah ini.');
            botInput.placeholder = "Contoh: 08123456789";
            botInput.type = "tel";
            botInput.focus();
        }

        function appendMessage(role, text, isSOP = false) {
            const container = document.createElement('div');
            container.className = role === 'user' ? 'flex justify-end' : 'flex items-start gap-2.5 animate-[slideUp_0.3s_ease-out]';

            if (role === 'bot') {
                let messageHtml = '';
                if (isSOP) {
                    // SOP-style Card UI
                    messageHtml = `
                        <div class="bg-white border border-teal-100 rounded-2xl shadow-md overflow-hidden max-w-[90%]">
                            <div class="bg-teal-600 px-4 py-2 text-white flex justify-between items-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider">Informasi Layanan Resmi</span>
                                <i class="fas fa-check-circle text-xs text-teal-200"></i>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="text-xs text-slate-700 leading-relaxed">
                                    ${text.replace(/\n/g, '<br>')}
                                </div>
                                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                                    <p class="text-[10px] text-slate-400 font-medium">Apakah informasi ini membantu?</p>
                                    <div class="flex gap-2">
                                        <button onclick="appendMessage('bot', 'Terima kasih atas feedback Anda! Terus tingkatkan pelayanan kami.')" class="btn btn-xs btn-outline btn-success rounded-lg lowercase text-[9px]">Ya, Jelas</button>
                                        <button onclick="startClarification()" class="btn btn-xs btn-outline btn-warning rounded-lg lowercase text-[9px]">Ingin Bertanya Petugas</button>
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
                        <div class="bg-white border border-slate-200 text-slate-700 p-3 rounded-2xl rounded-tl-none text-xs leading-relaxed shadow-sm max-w-[85%] font-medium">
                            ${text.replace(/\n/g, '<br>')}
                        </div>
                    `;
                }
                container.innerHTML = messageHtml;
            } else {
                container.innerHTML = `
                    <div class="bg-teal-600 text-white p-3 rounded-2xl rounded-tr-none text-xs leading-relaxed shadow-md max-w-[85%] font-medium">
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

            if (chatState === 'CAPTURE_WA') {
                handleWaCapture(inputVal);
                return;
            }

            lastUserQuery = inputVal;
            appendMessage('user', inputVal);
            botInput.value = '';

            try {
                const response = await fetch(`{{ route('api.faq.search') }}?q=${encodeURIComponent(inputVal)}`);
                const data = await response.json();

                if (data.found && data.results && data.results.length > 0) {
                    const top = data.results[0];
                    const answerText = top.jawaban || top.answer || data.answer;
                    appendMessage('bot', answerText, !data.is_emergency);

                    // Trigger Voice Guide speak if modular JS is active
                    if (window.VoiceSpeech && window.VoiceState && window.VoiceState.isActive()) {
                        window.VoiceSpeech.speak(answerText);
                    }
                } else if (data.answer) {
                    // Fallback for direct answer field if results missing
                    appendMessage('bot', data.answer, !data.is_emergency);
                    if (window.VoiceSpeech && window.VoiceState && window.VoiceState.isActive()) {
                        window.VoiceSpeech.speak(data.answer);
                    }
                } else {
                    appendMessage('bot', 'Maaf, saya tidak menemukan jawaban pasti. Ingin bertanya langsung pada petugas?');
                }
            } catch (error) {
                appendMessage('bot', 'Sepertinya ada gangguan koneksi. Coba lagi nanti ya.');
            }
        });

        async function handleWaCapture(wa) {
            botInput.value = '';
            appendMessage('user', wa);
            appendMessage('bot', 'Sedang mencatat permintaan Anda untuk petugas...');

            try {
                const response = await fetch("{{ route('public.service.submit') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        nama_pemohon: 'Warga (Via Chatbot)',
                        jenis_layanan: 'Konsultasi Administratif',
                        uraian: `[Diteruskan dari Bot FAQ] Pertanyaan: "${lastUserQuery}"`,
                        whatsapp: wa,
                        source: 'chatbox',
                        is_agreed: true
                    })
                });

                if (response.ok) {
                    appendMessage('bot', '✅ **Permintaan Berhasil Dicatat!**\n\nNomor Anda sudah tersimpan. Petugas akan menghubungi Anda maksimal dalam 1x24 jam kerja.');
                } else {
                    appendMessage('bot', 'Gagal menyimpan data. Pastikan nomor WhatsApp benar.');
                }
            } catch (error) {
                appendMessage('bot', 'Terjadi kendala saat mengirim data ke petugas.');
            } finally {
                chatState = 'FAQ';
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

        function openSubmissionModal(serviceName, requirements = '') {
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

                // Try to split requirements by numbers (1., 2., etc.) or bullets or commas
                let reqList = [];
                if (requirements) {
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
                    const response = await fetch("{{ route('public.service.submit') }}", {
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

        // Harmonic Professional Palette (Earthy & Teal Govt Tones)
        const villageColors = [
            '#0f766e', '#0369a1', '#1d4ed8', '#4338ca', '#6d28d9',
            '#7e22ce', '#a21caf', '#be185d', '#b91c1c', '#c2410c',
            '#b45309', '#a16207', '#4d7c0f', '#15803d', '#166534',
            '#3f6212', '#115e59'
        ];

        // Initialize Map with smooth motion
        const map = L.map('mapContainer', {
            center: [-7.78, 113.47],
            zoom: 13,
            scrollWheelZoom: false,
            attributionControl: false,
            zoomControl: false,
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
                        map.flyTo([-7.78, 113.47], 13.5, {
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
                                    <div class="px-2 py-1 text-center">
                                        <p class="text-[8px] font-bold text-teal-400 uppercase tracking-widest mb-0.5">Wilayah Desa</p>
                                        <p class="text-xs font-black text-white">${nama}</p>
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
                                map.flyToBounds(e.target.getBounds(), {
                                    padding: [80, 80],
                                    duration: 1.2
                                });

                                const popupContent = `
                                    <div class="premium-popup-card">
                                        <div class="popup-header">
                                            <div class="icon-box">
                                                <i class="fas fa-landmark"></i>
                                            </div>
                                            <div class="text-box">
                                                <h4>${nama}</h4>
                                                <span>Portal Resmi Desa</span>
                                            </div>
                                        </div>
                                        <div class="popup-body">
                                            <p>Akses layanan mandiri dan informasi publik Desa ${nama} secara digital.</p>
                                            <a href="${url}" target="_blank" rel="noopener noreferrer" class="popup-btn">
                                                Kunjungi Website <i class="fas fa-external-link-alt ml-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                `;

                                layer.bindPopup(popupContent, {
                                    className: 'premium-leaflet-popup',
                                    closeButton: false,
                                    maxWidth: 280
                                }).openPopup();
                            }
                        });
                    }
                }).addTo(map);

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
    </script>


    <script src="{{ asset('voice-guide/min/voice.bundle.min.js') }}?v=3.3"></script>

</body>

</html>