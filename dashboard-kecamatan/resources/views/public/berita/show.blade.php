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
    </style>

    <!-- Style Overrides -->
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- Header -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('public.berita.index') }}"
                    class="flex items-center text-slate-500 hover:text-blue-600 transition group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="font-medium">Kembali ke Warta</span>
                </a>
                <div class="text-sm font-bold text-slate-900 tracking-tight">KECAMATAN NEWS</div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12">

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
        <div class="max-w-3xl mx-auto mt-12 border-t border-slate-200 pt-8 flex justify-between items-center">
            <div class="text-slate-500 text-sm font-medium">
                Dibaca {{ number_format($berita->view_count) }} kali
            </div>
            <div class="flex gap-2">
                <button
                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                    </svg>
                </button>
                <button
                    class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-100 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                </button>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-12">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <a href="{{ route('public.berita.index') }}"
                class="text-blue-600 font-bold mb-4 inline-block hover:underline">Warta Kecamatan Lainnya</a>
            <p class="text-slate-400 text-sm">© 2026 Pemerintah Kecamatan. Informasi Publik.</p>
        </div>
    </footer>

    <!-- Voice Guide Scripts -->
    <script>
        window.APP_WILAYAH_NAMA = "{{ appProfile()->region_name }}";
    </script>
    <script src="{{ asset('voice-guide/voice.bundle.js') }}?v=2.9.2"></script>
</body>

</html>