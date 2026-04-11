<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Informasi - Kecamatan Official</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-berita.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Font Resizing Classes */
        .text-size-sm { font-size: 0.875rem !important; }
        .text-size-base { font-size: 1rem !important; }
        .text-size-lg { font-size: 1.125rem !important; }
        .text-size-xl { font-size: 1.25rem !important; }

        /* Custom Scrollbar Hide */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Ticker Animation */
        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .ticker-animate {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker 30s linear infinite;
        }
        .ticker-container:hover .ticker-animate {
            animation-play-state: paused;
        }

        /* Sticky Sidebar */
        @media (min-width: 1024px) {
            .sticky-sidebar {
                position: sticky;
                top: 5rem;
                height: calc(100vh - 6rem);
                overflow-y: auto;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- Header Section -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <div class="flex items-center">
                        <span class="text-xl font-extrabold text-blue-600">KECAMATAN</span>
                        <span class="ml-2 text-slate-400 font-medium">NEWS</span>
                    </div>
                    <!-- Accessibility Toolbar -->
                    <div class="hidden sm:flex items-center bg-slate-50 rounded-full px-3 py-1 gap-2 border border-slate-100 ml-4">
                        <button onclick="changeFontSize(-1)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-white rounded-full transition" title="Kecilkan Teks">A-</button>
                        <span class="w-[1px] h-3 bg-slate-200"></span>
                        <button onclick="changeFontSize(1)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-white rounded-full transition" title="Besarkan Teks">A+</button>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="hidden md:block">
                        <a href="/" class="text-slate-600 hover:text-blue-600 font-medium transition flex items-center gap-2">
                            <i class="fas fa-arrow-left text-xs"></i> Ke Beranda
                        </a>
                    </div>
                    <button class="md:hidden text-slate-600"><i class="fas fa-bars"></i></button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breaking News Ticker -->
    <div class="bg-blue-600 text-white py-2 overflow-hidden relative z-40">
        <div class="max-w-7xl mx-auto px-4 flex items-center">
            <div class="bg-white text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-tighter mr-4 flex-none">Breaking</div>
            <div class="ticker-container overflow-hidden w-full">
                <div class="ticker-animate flex gap-12 text-sm font-medium">
                    @forelse($popularBerita as $item)
                        <a href="{{ route('public.berita.show', $item->slug) }}" class="hover:underline">{{ $item->judul }}</a>
                    @empty
                        <span>Selamat Datang di Portal Warta Digital Resmi Kecamatan. Dapatkan informasi terkini dari 17 Desa binaan.</span>
                    @endforelse
                    
                    @if($popularBerita->count() > 0)
                        <!-- Repeat for smooth loop -->
                        @foreach($popularBerita as $item)
                            <a href="{{ route('public.berita.show', $item->slug) }}" class="hover:underline">{{ $item->judul }}</a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-size-base">

        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            <!-- Left Sidebar / Main Content Area -->
            <div class="lg:col-span-8">
                <!-- Section Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Warta Digital</h1>
                    <p class="text-slate-500 max-w-2xl">Integrasi informasi resmi dari Kecamatan dan 17 Desa binaan.</p>
                </div>

        <!-- Village Switcher: Modern News Pills -->
        <div class="mb-12 relative">
            <div class="flex items-center gap-3 overflow-x-auto pb-4 scrollbar-hide px-2">
                <!-- Kecamatan Filter -->
                <a href="{{ route('public.berita.index') }}" 
                   class="flex-none flex items-center gap-2 px-6 py-2.5 rounded-full transition-all duration-300 font-bold whitespace-nowrap {{ !request('desa_id') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-200 scale-105 z-10' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                    <i class="fas fa-landmark text-xs {{ !request('desa_id') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Tingkat Kecamatan</span>
                </a>

                @foreach($desas as $desa)
                    <a href="{{ route('public.berita.index', ['desa_id' => $desa->id]) }}" 
                       class="relative flex-none flex items-center gap-2 px-6 py-2.5 rounded-full transition-all duration-300 font-bold whitespace-nowrap {{ request('desa_id') == $desa->id ? 'bg-gradient-to-r from-teal-500 to-emerald-600 text-white shadow-lg shadow-teal-200 scale-105 z-10' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                        <i class="fas fa-home-alt text-xs {{ request('desa_id') == $desa->id ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Desa {{ $desa->nama_desa }}</span>
                        
                        @if(isset($counts[$desa->id]) && $counts[$desa->id] > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[8px] text-white items-center justify-center font-black">
                                    {{ $counts[$desa->id] }}
                                </span>
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
            <!-- Gradient fade on edges for scroll indicator -->
            <div class="absolute right-0 top-0 bottom-4 w-12 bg-gradient-to-l from-slate-50 to-transparent pointer-events-none md:hidden"></div>
        </div>

        @if($berita->count() > 0)
            <!-- Highlight News -->
            @php $highlight = $berita->first(); @endphp
            <div class="mb-16">
                <div
                    class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden md:flex flex-row-reverse group cursor-pointer hover:shadow-xl transition-all duration-300">
                    <div class="md:w-3/5 overflow-hidden relative">
                        <img src="{{ $highlight->thumbnail ? (Str::startsWith($highlight->thumbnail, 'http') ? $highlight->thumbnail : asset('storage/' . $highlight->thumbnail)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=1000' }}"
                            alt="{{ $highlight->judul }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @if($highlight->desa)
                            <div class="absolute top-6 left-6">
                                <span class="bg-teal-500 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-lg">BERITA DESA {{ strtoupper($highlight->desa->nama_desa) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="md:w-2/5 p-8 md:p-12 flex flex-col justify-center">
                        <span
                            class="inline-block bg-blue-100 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-6 w-fit">Berita
                            Utama</span>
                        <h2
                            class="text-3xl font-bold text-slate-900 mb-4 leading-tight group-hover:text-blue-600 transition">
                            <a href="{{ $highlight->external_url ?: route('public.berita.show', $highlight->slug) }}" {{ $highlight->external_url ? 'target="_blank"' : '' }}>{{ $highlight->judul }}</a>
                        </h2>
                        <p class="text-slate-600 mb-8 line-clamp-3">
                            {{ $highlight->ringkasan ?: Str::limit(strip_tags($highlight->konten), 160) }}
                        </p>
                        <div class="flex items-center text-sm text-slate-500">
                            <span class="font-semibold text-slate-900">{{ $highlight->author->nama_lengkap }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $highlight->published_at->isoFormat('D MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest News Grid -->
            <div class="mb-12">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-slate-900">Kabar Terbaru</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($berita->skip(1) as $item)
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-md transition-all">
                            <div class="relative aspect-video overflow-hidden">
                                <img src="{{ $item->thumbnail ? (Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset('storage/' . $item->thumbnail)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=1000' }}"
                                    alt="{{ $item->judul }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="{{ $item->desa ? 'bg-teal-600/90' : 'bg-blue-600/90' }} backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg uppercase">
                                        {{ $item->desa ? $item->desa->nama_desa : 'Kecamatan' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h4
                                    class="text-xl font-bold text-slate-900 mb-3 leading-snug group-hover:text-blue-600 transition">
                                    <a href="{{ $item->external_url ?: route('public.berita.show', $item->slug) }}" {{ $item->external_url ? 'target="_blank"' : '' }}>{{ $item->judul }}</a>
                                </h4>
                                <p class="text-slate-500 text-sm mb-6 line-clamp-2">
                                    {{ $item->ringkasan ?: Str::limit(strip_tags($item->konten), 100) }}
                                </p>
                                <div
                                    class="flex items-center justify-between text-[11px] text-slate-400 font-medium uppercase tracking-wider">
                                    <span>{{ $item->author->nama_lengkap }}</span>
                                    <span>{{ $item->published_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-16">
                {{ $berita->links() }}
            </div>

        @else
            <div class="text-center py-24 bg-white rounded-3xl border border-dashed border-slate-200">
                <div class="mb-6 opacity-20">
                    <svg class="mx-auto h-24 w-24 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M14 2v4h4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h8M8 16h8M8 8h2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Belum ada berita dipublikasikan</h3>
                <p class="text-slate-500">Silakan cek kembali nanti untuk informasi terbaru.</p>
            </div>
        @endif

            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-4 mt-12 lg:mt-0">
                <div class="sticky-sidebar space-y-12">
                    
                    <!-- Search Widget -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-lg font-bold text-slate-900 mb-4">Cari Info</h4>
                        <form action="{{ route('public.berita.index') }}" method="GET" class="relative group">
                            <input type="text" name="search" placeholder="Cari berita..." 
                                class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-4 pr-12 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                            <button type="submit" class="absolute right-3 top-2.5 text-slate-400 group-hover:text-blue-600 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Popular News Widget -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-lg font-bold text-slate-900 mb-6 flex items-center border-b border-slate-50 pb-4">
                            <i class="fas fa-fire text-orange-500 mr-3"></i> Terpopuler
                        </h4>
                        <div class="space-y-6">
                            @foreach($popularBerita as $index => $pop)
                                <div class="flex gap-4 group cursor-pointer">
                                    <div class="flex-none text-3xl font-black text-slate-100 group-hover:text-blue-100 transition duration-300">0{{ $index + 1 }}</div>
                                    <div class="space-y-1">
                                        <h5 class="text-sm font-bold text-slate-800 leading-tight group-hover:text-blue-600 transition tracking-tight">
                                            <a href="{{ route('public.berita.show', $pop->slug) }}">{{ $pop->judul }}</a>
                                        </h5>
                                        <div class="flex items-center text-[10px] text-slate-400 uppercase font-bold tracking-widest">
                                            <span>{{ $pop->view_count }} Pembaca</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- News Banner Ad -->
                    @if($banners->count() > 0)
                        @foreach($banners as $banner)
                            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 mb-6 group">
                                <a href="{{ $banner->link_url ?: '#' }}" {{ $banner->link_url ? 'target="_blank"' : '' }}>
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                        alt="{{ $banner->title }}" 
                                        class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-500">
                                </a>
                            </div>
                        @endforeach
                    @endif

                    <!-- Quick Layanan Tracker -->
                    <div class="bg-slate-900 rounded-3xl p-8 text-white">
                        <h4 class="text-xl font-bold mb-6">Lacak Layanan</h4>
                        <p class="text-slate-400 text-sm mb-6">Masukkan nomor registrasi untuk cek status dokumen Anda.</p>
                        <form action="{{ route('public.tracking') }}" method="GET">
                            <input type="text" name="ticket_id" placeholder="T-2024-XXXX" 
                                class="w-full bg-white/10 border-white/20 rounded-2xl py-4 px-5 text-sm focus:ring-blue-500 focus:bg-white/20 placeholder-slate-500 transition-all mb-4">
                            <button class="w-full bg-blue-600 hover:bg-blue-500 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/40">Cek Sekarang</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-100 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center flex-wrap gap-8 mb-8">
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Tentang Kami</a>
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Syarat & Ketentuan</a>
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Kontak</a>
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Sitemap</a>
            </div>
            <p class="text-slate-400 text-sm">© 2026 Pemerintah Kecamatan. Informasi Publik bersifat Terbuka.</p>
        </div>
    </footer>


    <!-- Scripts -->
    <script>
        // Accessibility: Font Resizing
        let currentSizeIndex = 1;
        const sizeClasses = ['text-size-sm', 'text-size-base', 'text-size-lg', 'text-size-xl'];
        
        function changeFontSize(direction) {
            const main = document.getElementById('main-content');
            main.classList.remove(sizeClasses[currentSizeIndex]);
            
            currentSizeIndex += direction;
            if(currentSizeIndex < 0) currentSizeIndex = 0;
            if(currentSizeIndex >= sizeClasses.length) currentSizeIndex = sizeClasses.length - 1;
            
            main.classList.add(sizeClasses[currentSizeIndex]);
            localStorage.setItem('preferred-news-font-size', currentSizeIndex);
        }

        // Restore preference
        document.addEventListener('DOMContentLoaded', () => {
            const pref = localStorage.getItem('preferred-news-font-size');
            if(pref !== null) {
                const main = document.getElementById('main-content');
                main.classList.remove('text-size-base');
                currentSizeIndex = parseInt(pref);
                main.classList.add(sizeClasses[currentSizeIndex]);
            }
        });
    </script>

    <!-- Voice Guide Button (Floating) -->
    <button id="voice-guide-btn" aria-label="Aktifkan Pemandu Suara">
        <i class="fas fa-microphone"></i>
    </button>

    <!-- Voice Guide Scripts -->
    <script>
        window.APP_WILAYAH_NAMA = "{{ appProfile()->region_name }}";
    </script>
    <script src="{{ asset('voice-guide/voice.bundle.js') }}?v=2.9.2"></script>

</body>

</html>