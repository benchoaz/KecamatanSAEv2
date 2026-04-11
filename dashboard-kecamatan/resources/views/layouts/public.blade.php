<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ appProfile()->logo_path ? asset('storage/' . appProfile()->logo_path) : '' }}"
        type="image/png">

    <title>@yield('title', appProfile()->full_region_name)</title>

    @yield('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900">

    {{-- Header / Navbar --}}
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
        <div class="container mx-auto px-4 md:px-6 h-16 md:h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ appProfile()->logo_path ? asset('storage/' . appProfile()->logo_path) : '' }}" alt="Logo"
                    style="height: 32px; width: auto; object-fit: contain;"
                    class="md:h-12 flex-shrink-0 group-hover:scale-105 transition-transform">
                <div>
                    <div class="font-black text-slate-800 leading-none text-xs md:text-sm">
                        {{ strtoupper(appProfile()->full_region_name) }}
                    </div>
                    <div class="text-[8px] md:text-[10px] text-slate-500 uppercase tracking-widest mt-0.5 md:mt-1">
                        {{ appProfile()->region_parent ?? 'Kabupaten Probolinggo' }}
                    </div>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-6">
                <a href="/"
                    class="text-xs font-bold {{ request()->is('/') ? 'text-teal-600' : 'text-slate-600 hover:text-teal-600' }} transition-colors">Beranda</a>
                <a href="{{ route('economy.index') }}"
                    class="text-xs font-bold {{ request()->is('ekonomi*') ? 'text-teal-600' : 'text-slate-600 hover:text-teal-600' }} transition-colors">Pusat
                    Ekonomi</a>
                <a href="/login"
                    class="px-4 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-700 transition-all shadow-md">Login
                    Admin</a>
            </div>
        </div>
    </nav>

    {{-- Content Area --}}
    <main>
        @yield('content')
    </main>

    <!-- Premium Footer Section - Compact -->
    <footer class="bg-[#0f172a] text-slate-400 pt-12 pb-8 border-t border-slate-800/60 relative overflow-hidden">
        {{-- Background Accents --}}
        <div
            class="absolute top-0 right-0 w-96 h-96 bg-teal-500/5 blur-[100px] rounded-full pointer-events-none -translate-y-1/2 translate-x-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-72 h-72 bg-blue-500/5 blur-[100px] rounded-full pointer-events-none translate-y-1/2 -translate-x-1/2">
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Branding -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="group hover:scale-105 transition-transform duration-500 flex-shrink-0">
                            <img src="{{ appProfile()->logo_path ? asset('storage/' . appProfile()->logo_path) : asset('img/logo-default.png') }}"
                                alt="Logo" style="height: 45px; width: auto; object-fit: contain;"
                                class="brightness-110 drop-shadow-xl">
                        </div>
                        <div>
                            <h5 class="text-white font-black text-lg uppercase tracking-tight">
                                {{ appProfile()->full_region_name }}
                            </h5>
                            <p class="text-[9px] text-teal-500 font-bold uppercase tracking-[0.3em]">
                                {{ appProfile()->tagline }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed max-w-xs">
                        Portal informasi dan pelayanan publik digital resmi {{ appProfile()->full_region_name }}.
                        Membangun tata kelola mandiri dan transparan.
                    </p>
                </div>

                <!-- Navigation -->
                <div class="space-y-4">
                    <h6 class="text-white font-black text-[9px] uppercase tracking-[0.3em] flex items-center gap-3">
                        Navigasi Cepat
                        <span class="flex-grow h-px bg-white/5"></span>
                    </h6>
                    <ul class="space-y-2.5 text-xs text-slate-500">
                        <li><a href="/" class="hover:text-teal-400 transition-colors flex items-center gap-2 group"><i
                                    class="fas fa-angle-right text-[10px] text-teal-400 group-hover:translate-x-1 transition-transform"></i>
                                Beranda Utama</a></li>
                        <li><a href="/layanan"
                                class="hover:text-teal-400 transition-colors flex items-center gap-2 group"><i
                                    class="fas fa-angle-right text-[10px] text-teal-400 group-hover:translate-x-1 transition-transform"></i>
                                Portal Layanan</a></li>
                        <li><a href="{{ route('economy.index') }}"
                                class="hover:text-teal-400 transition-colors flex items-center gap-2 group"><i
                                    class="fas fa-angle-right text-[10px] text-teal-400 group-hover:translate-x-1 transition-transform"></i>
                                Pusat Ekonomi</a></li>
                        <li><a href="/lacak-berkas"
                                class="hover:text-teal-400 transition-colors flex items-center gap-2 group"><i
                                    class="fas fa-angle-right text-[10px] text-teal-400 group-hover:translate-x-1 transition-transform"></i>
                                Tracking Berkas</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="space-y-4">
                    <h6 class="text-white font-black text-[9px] uppercase tracking-[0.3em] flex items-center gap-3">
                        Hubungi Kami
                        <span class="flex-grow h-px bg-white/5"></span>
                    </h6>
                    <ul class="space-y-3 text-xs text-slate-500">
                        <li class="flex gap-3">
                            <i class="fas fa-map-marker-alt text-teal-500 mt-1"></i>
                            <span class="leading-relaxed">{{ appProfile()->address ?? 'Alamat Belum Diatur' }}</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-phone-alt text-teal-500"></i>
                            <span
                                class="font-bold text-white tracking-wide">{{ appProfile()->phone ?? '(0335) 123456' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Hours -->
                <div class="space-y-4">
                    <h6 class="text-white font-black text-[9px] uppercase tracking-[0.3em] flex items-center gap-3">
                        Jam Pelayanan
                        <span class="flex-grow h-px bg-white/5"></span>
                    </h6>
                    <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800/80">
                        <ul class="space-y-2 text-xs">
                            <li class="flex justify-between items-center pb-3 border-b border-white/5">
                                <span class="text-slate-500 font-medium">Senin - Kamis:</span>
                                <span
                                    class="text-white font-bold">{{ appProfile()->office_hours_mon_thu ?? '08:00 - 15:30' }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Jumat:</span>
                                <span
                                    class="text-white font-bold">{{ appProfile()->office_hours_fri ?? '08:00 - 11:30' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Social & Bottom Bar -->
            <div class="pt-8 border-t border-slate-800/80 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[11px] font-bold text-slate-600 uppercase tracking-widest text-center md:text-left">
                    &copy; {{ date('Y') }} {{ appProfile()->full_region_name }}. Seluruh Hak Cipta Dilindungi.
                </p>
                <div class="flex items-center gap-3">
                    @if(appProfile()->facebook_url)
                        <a href="{{ appProfile()->facebook_url }}"
                            class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:bg-white hover:text-black hover:-translate-y-1 transition-all duration-300"><i
                                class="fab fa-facebook-f text-xs"></i></a>
                    @endif
                    @if(appProfile()->instagram_url)
                        <a href="{{ appProfile()->instagram_url }}"
                            class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:bg-white hover:text-black hover:-translate-y-1 transition-all duration-300"><i
                                class="fab fa-instagram text-xs"></i></a>
                    @endif
                    @if(appProfile()->youtube_url)
                        <a href="{{ appProfile()->youtube_url }}"
                            class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:bg-white hover:text-black hover:-translate-y-1 transition-all duration-300"><i
                                class="fab fa-youtube text-xs"></i></a>
                    @endif
                    @if(appProfile()->x_url)
                        <a href="{{ appProfile()->x_url }}"
                            class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:bg-white hover:text-black hover:-translate-y-1 transition-all duration-300"><i
                                class="fab fa-x-twitter text-xs"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </footer>


    @stack('scripts')
</body>

</html>