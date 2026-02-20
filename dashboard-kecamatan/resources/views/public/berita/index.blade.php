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
        body {
            font-family: 'Inter', sans-serif;
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
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- Header Section -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-xl font-extrabold text-blue-600">KECAMATAN</span>
                    <span class="ml-2 text-slate-400 font-medium">NEWS</span>
                </div>
                <div class="hidden md:block">
                    <a href="/" class="text-slate-600 hover:text-blue-600 font-medium transition">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Section Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Warta Kecamatan</h1>
            <p class="text-slate-500 max-w-2xl">Informasi terkini mengenai program pembangunan, kegiatan sosial, dan
                pengumuman resmi pemerintah kecamatan.</p>
        </div>

        @if($berita->count() > 0)
            <!-- Highlight News -->
            @php $highlight = $berita->first(); @endphp
            <div class="mb-16">
                <div
                    class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden md:flex flex-row-reverse group cursor-pointer hover:shadow-xl transition-all duration-300">
                    <div class="md:w-3/5 overflow-hidden">
                        <img src="{{ $highlight->thumbnail ? asset('storage/' . $highlight->thumbnail) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=1000' }}"
                            alt="{{ $highlight->judul }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="md:w-2/5 p-8 md:p-12 flex flex-col justify-center">
                        <span
                            class="inline-block bg-blue-100 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-6 w-fit">Berita
                            Utama</span>
                        <h2
                            class="text-3xl font-bold text-slate-900 mb-4 leading-tight group-hover:text-blue-600 transition">
                            <a href="{{ route('public.berita.show', $highlight->slug) }}">{{ $highlight->judul }}</a>
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
                                <img src="{{ $item->thumbnail ? asset('storage/' . $item->thumbnail) : 'https://via.placeholder.com/600x400?text=Kecamatan' }}"
                                    alt="{{ $item->judul }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg uppercase">
                                        {{ $item->kategori }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h4
                                    class="text-xl font-bold text-slate-900 mb-3 leading-snug group-hover:text-blue-600 transition">
                                    <a href="{{ route('public.berita.show', $item->slug) }}">{{ $item->judul }}</a>
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

    </main>

    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-100 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-400 text-sm">© 2026 Pemerintah Kecamatan. Informasi Publik bersifat Terbuka.</p>
        </div>
    </footer>

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