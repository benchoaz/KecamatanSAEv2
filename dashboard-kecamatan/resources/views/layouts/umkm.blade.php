<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seller Center - {{ $umkm->nama_usaha }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Script Tailwind (JIT) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        premium: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        seller: {
                            primary: '#0ea5e9',
                            secondary: '#6366f1',
                            dark: '#0f172a'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-item-active {
            @apply bg-seller-primary/10 text-seller-primary font-bold border-r-4 border-seller-primary;
        }

        body {
            background-color: #f8fafc;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans text-slate-700">

    <!-- Sidebar (Desktop) -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 hidden lg:flex flex-col">
        <!-- Logo Section -->
        <div class="p-8 border-b border-slate-50">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-tr from-seller-primary to-seller-secondary rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-store text-lg"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 tracking-tight leading-none text-lg">Seller Center</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">UMKM Rakyat</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-6 space-y-2 overflow-y-auto">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 px-4">Menu Utama</p>

            <a href="{{ route('umkm_rakyat.manage', $umkm->manage_token) }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all {{ request()->routeIs('umkm_rakyat.manage') ? 'bg-sky-50 text-sky-600 font-bold border-r-4 border-sky-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-chart-line w-5 text-center"></i>
                <span class="text-sm">Dashboard Saya</span>
            </a>

            <a href="{{ route('umkm_rakyat.manage.products', $umkm->manage_token) }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all {{ request()->routeIs('umkm_rakyat.manage.products') ? 'bg-sky-50 text-sky-600 font-bold border-r-4 border-sky-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-box w-5 text-center"></i>
                <span class="text-sm">Kelola Produk</span>
            </a>

            <a href="{{ route('umkm_rakyat.manage.settings', $umkm->manage_token) }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all {{ request()->routeIs('umkm_rakyat.manage.settings') ? 'bg-sky-50 text-sky-600 font-bold border-r-4 border-sky-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-user-edit w-5 text-center"></i>
                <span class="text-sm">Profil Usaha</span>
            </a>

            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-8 mb-4 px-4">Toko Saya</p>

            <a href="{{ route('umkm_rakyat.show', $umkm->slug) }}" target="_blank"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all">
                <i class="fas fa-external-link-alt w-5 text-center"></i>
                <span class="text-sm">Lihat Etalase Publik</span>
            </a>
        </nav>

        <!-- Footer Profile -->
        <div class="p-6 border-t border-slate-50">
            <div class="bg-slate-50 p-4 rounded-2xl flex items-center gap-3 border border-slate-100">
                <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm">
                    @if($umkm->foto_usaha)
                        <img src="{{ asset('storage/' . $umkm->foto_usaha) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-sky-200 flex items-center justify-center text-sky-600 font-bold">
                            {{ substr($umkm->nama_usaha, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black text-slate-800 truncate">{{ $umkm->nama_usaha }}</p>
                    <p class="text-[9px] font-bold text-slate-400 truncate uppercase mt-0.5">{{ $umkm->nama_pemilik }}
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Header -->
    <header
        class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center text-white text-xs shadow-md">
                <i class="fas fa-store"></i>
            </div>
            <h1 class="font-black text-slate-800 text-sm">Seller Center</h1>
        </div>
        <button onclick="document.getElementById('mobile-nav').classList.toggle('hidden')" class="text-slate-500 p-2">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </header>

    <!-- Mobile Nav Overlay -->
    <div id="mobile-nav" class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-sm hidden lg:hidden">
        <div class="bg-white w-72 h-full shadow-2xl flex flex-col">
            <div class="p-8 flex justify-between items-center border-b border-slate-50">
                <h2 class="font-black text-slate-800">Menu Seller</h2>
                <button onclick="document.getElementById('mobile-nav').classList.add('hidden')" class="text-slate-400">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex-1 p-6 space-y-4">
                <a href="{{ route('umkm_rakyat.manage', $umkm->manage_token) }}"
                    class="flex items-center gap-4 text-slate-600 font-bold px-4 py-2">
                    <i class="fas fa-chart-line text-sky-500"></i> Dashboard
                </a>
                <a href="{{ route('umkm_rakyat.manage.products', $umkm->manage_token) }}"
                    class="flex items-center gap-4 text-slate-600 font-bold px-4 py-2">
                    <i class="fas fa-box text-sky-500"></i> Produk
                </a>
                <a href="{{ route('umkm_rakyat.manage.settings', $umkm->manage_token) }}"
                    class="flex items-center gap-4 text-slate-600 font-bold px-4 py-2">
                    <i class="fas fa-user-edit text-sky-500"></i> Profil
                </a>
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <a href="{{ route('umkm_rakyat.show', $umkm->slug) }}"
                        class="flex items-center gap-4 text-slate-600 font-bold px-4 py-2 text-sm italic">
                        <i class="fas fa-eye text-sky-500"></i> Lihat Toko
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="lg:ml-72 min-h-screen pt-20 lg:pt-0 pb-12">
        <div class="p-6 md:p-12 max-w-7xl mx-auto">
            <!-- Header Info (Desktop) -->
            <div class="hidden lg:flex items-center justify-between mb-12">
                <div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-slate-500 font-medium mt-1">Selamat datang kembali, {{ $umkm->nama_pemilik }}!</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Toko</p>
                        <div class="flex items-center justify-end gap-2 mt-1">
                            @if($umkm->status == 'aktif')
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-black text-emerald-600 uppercase">Aktif & Publik</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span
                                    class="text-xs font-black text-amber-600 uppercase">{{ strtoupper($umkm->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>

</html>