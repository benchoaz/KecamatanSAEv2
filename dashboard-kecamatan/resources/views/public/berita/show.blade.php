<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $berita->judul }} - Warta Kecamatan</title>
    <meta name="description" content="{{ $berita->ringkasan ?: Str::limit(strip_tags($berita->konten), 160) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-berita.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .prose p {
            margin-bottom: 1.5em;
            line-height: 1.8;
        }

        .prose h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .prose h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #334155;
        }

        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1.5em;
        }

        .prose blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            font-style: italic;
            color: #64748b;
            margin-bottom: 1.5em;
        }

        .article-content {
            font-family: 'Merriweather', serif;
            font-size: 1.125rem;
            color: #334155;
        }

        /* Voice Guide Button (Floating) */
        #voice-guide-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            box-shadow: 0 10px 25px rgba(249, 115, 22, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        #voice-guide-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 35px rgba(249, 115, 22, 0.5);
        }

        #voice-guide-btn.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Font Resizing Classes */
        .text-size-sm { font-size: 0.875rem !important; }
        .text-size-base { font-size: 1rem !important; }
        .text-size-lg { font-size: 1.125rem !important; }
        .text-size-xl { font-size: 1.25rem !important; }

        .article-content {
            font-family: 'Merriweather', serif;
            color: #334155;
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

    <!-- Style Overrides -->
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- Header Section -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('public.berita.index') }}" class="flex items-center">
                        <span class="text-xl font-extrabold text-blue-600">KECAMATAN</span>
                        <span class="ml-2 text-slate-400 font-medium">NEWS</span>
                    </a>
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
                </div>
            </div>
        </div>
    </nav>

    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-size-base">

        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            <!-- Left Column: Article -->
            <div class="lg:col-span-8">
                
                <!-- Breadcrumbs -->
                <nav class="flex mb-8 text-sm font-medium text-slate-400" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('public.berita.index') }}" class="hover:text-blue-600 transition">Berita</a></li>
                        <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                        <li class="text-slate-600 truncate max-w-[200px]">{{ $berita->judul }}</li>
                    </ol>
                </nav>

        <!-- Article Header -->
        <div class="mb-10 text-center">
            <div class="flex justify-center mb-6">
                <span
                    class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ $berita->kategori }}
                </span>
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 leading-tight mb-6">
                {{ $berita->judul }}
            </h1>
            <div class="flex items-center justify-center text-slate-500 text-sm">
                <div class="flex items-center">
                    <div
                        class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center mr-3 text-xs font-bold text-slate-500">
                        {{ substr($berita->author->nama_lengkap ?? 'A', 0, 1) }}
                    </div>
                    <span class="font-medium text-slate-900">{{ $berita->author->nama_lengkap ?? 'Admin' }}</span>
                </div>
                <span class="mx-3">•</span>
                <time
                    datetime="{{ $berita->published_at }}">{{ $berita->published_at->isoFormat('dddd, D MMMM YYYY') }}</time>
                <span class="mx-3">•</span>
                <span>{{ ceil(str_word_count(strip_tags($berita->konten)) / 200) }} menit baca</span>
            </div>
        </div>

        <!-- Featured Image -->
        @if($berita->thumbnail)
            <div class="mb-12 rounded-3xl overflow-hidden shadow-lg">
                <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}"
                    class="w-full h-auto object-cover max-h-[500px]">
            </div>
        @endif

        <!-- Content -->
        <article
            class="prose prose-lg prose-slate mx-auto article-content bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-slate-100">
            {!! nl2br(e($berita->konten)) !!}
                </article>

                <!-- Share & Tags -->
                <div class="max-w-3xl mx-auto mt-12 border-t border-slate-200 pt-8 flex flex-col sm:flex-row justify-between items-center gap-6">
                    <div class="text-slate-500 text-sm font-medium">
                        Dipublikasikan oleh <span class="text-slate-900 font-bold">{{ $berita->author->nama_lengkap }}</span> 
                        • {{ $berita->published_at->isoFormat('D MMMM YYYY') }}
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bagikan</span>
                        <div class="flex gap-2">
                             <button class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition shadow-sm">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-green-500 hover:text-white transition shadow-sm">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-blue-400 hover:text-white transition shadow-sm">
                                <i class="fab fa-twitter"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation between articles (Optional but professional) -->
            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-4 mt-12 lg:mt-0">
                <div class="sticky-sidebar space-y-12">
                    
                    <!-- Search Widget -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-lg font-bold text-slate-900 mb-4">Cari Berita</h4>
                        <form action="{{ route('public.berita.index') }}" method="GET" class="relative group">
                            <input type="text" name="search" placeholder="Contoh: UMKM, Desa..." 
                                class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-4 pr-12 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                            <button type="submit" class="absolute right-3 top-2.5 text-slate-400 group-hover:text-blue-600 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Popular News Widget -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-lg font-bold text-slate-900 mb-6 flex items-center border-b border-slate-50 pb-4">
                            <i class="fas fa-fire text-orange-500 mr-3"></i> Berita Populer
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
                                            <span>{{ number_format($pop->view_count) }} Penayangan</span>
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

                    <!-- Back to Home -->
                    <div class="bg-slate-900 rounded-3xl p-8 text-white text-center">
                        <i class="fas fa-arrow-left text-2xl mb-4 text-blue-500"></i>
                        <h4 class="text-xl font-bold mb-2">Pusat Informasi</h4>
                        <p class="text-slate-400 text-sm mb-6">Kembali ke beranda utama untuk melihat layanan lainnya.</p>
                        <a href="/" class="w-full inline-block bg-blue-600 hover:bg-blue-500 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/40">Kembali Sekarang</a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-100 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center flex-wrap gap-8 mb-8">
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Tentang Portal</a>
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Kontak Kami</a>
                <a href="#" class="text-slate-400 hover:text-blue-600 text-sm font-medium transition">Sitemap</a>
            </div>
            <p class="text-slate-400 text-sm">© 2026 Pemerintah Kecamatan. Dikembangkan oleh Tim IT Kecamatan.</p>
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

    <!-- Voice Guide Scripts -->
    <script>
        window.APP_WILAYAH_NAMA = "{{ appProfile()->region_name }}";
    </script>
    <script src="{{ asset('voice-guide/voice.bundle.js') }}?v=2.9.2"></script>
</body>

</html>