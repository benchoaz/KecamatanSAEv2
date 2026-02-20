<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login UMKM - Kecamatan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }

        .pattern-grid {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center relative overflow-hidden pattern-grid">

    <!-- Decorative Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-teal-400/10 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-purple-400/10 rounded-full blur-[120px] translate-x-1/2 translate-y-1/2">
        </div>
    </div>

    <!-- Back Button -->
    <a href="{{ url('/') }}"
        class="absolute top-8 left-8 z-20 group flex items-center gap-3 text-slate-500 hover:text-teal-600 transition-colors">
        <div
            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center border border-slate-100 group-hover:scale-110 transition-transform">
            <i class="fas fa-arrow-left"></i>
        </div>
        <span class="font-bold text-sm hidden md:block">Kembali ke Beranda</span>
    </a>

    <!-- Main Card -->
    <div class="w-full max-w-[420px] p-6 relative z-10">
        <div
            class="glass-effect rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-white/60 relative overflow-hidden">

            <!-- Top Decoration -->
            <div class="h-2 w-full bg-gradient-to-r from-teal-400 via-blue-500 to-purple-500"></div>

            <div class="p-8 md:p-10">
                <div class="text-center mb-8">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-slate-50 to-white rounded-3xl shadow-lg border border-white mx-auto mb-6 flex items-center justify-center transform rotate-3 hover:rotate-0 transition-transform duration-500">
                        <i
                            class="fas fa-store text-3xl text-transparent bg-clip-text bg-gradient-to-br from-teal-500 to-blue-600"></i>
                    </div>
                    <h1 class="text-2xl font-black text-slate-800 mb-2">Selamat Datang</h1>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">
                        Masuk untuk mengelola produk & pesanan UMKM Anda.
                    </p>
                </div>

                @if(session('error'))
                    <div
                        class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-3 rounded-2xl mb-6 flex items-center gap-3 animate-pulse">
                        <i class="fas fa-exclamation-circle text-lg"></i>
                        <span class="font-bold text-xs leading-tight">{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('umkm_rakyat.login.post') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="group">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nomor
                            WhatsApp</label>
                        <div class="relative">
                            <div
                                class="absolute left-0 top-0 bottom-0 px-4 bg-slate-50/50 rounded-l-2xl flex items-center border-y border-l border-slate-200 group-hover:border-teal-200 transition-colors">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/1200px-WhatsApp.svg.png"
                                    class="w-4 h-4 mr-2 opacity-70">
                                <span class="text-xs font-bold text-slate-600">+62</span>
                            </div>
                            <input type="text" name="no_wa" required placeholder="8123xxxx" autofocus
                                class="w-full bg-slate-50/30 border border-slate-200 rounded-2xl pl-24 pr-5 py-4 text-sm font-bold text-slate-800 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none placeholder:text-slate-300">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full relative group overflow-hidden bg-slate-900 text-white font-bold py-4 rounded-2xl shadow-xl shadow-slate-900/20 transform hover:-translate-y-1 transition-all duration-300">
                        <span
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-teal-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                        <div class="relative flex items-center justify-center gap-3">
                            <span>Masuk Dashboard</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                    <p class="text-[11px] font-bold text-slate-400 mb-2">Belum punya akun?</p>
                    <a href="{{ route('umkm_rakyat.create') }}"
                        class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-black text-xs bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-xl transition-colors">
                        <i class="fas fa-plus-circle"></i> Daftar UMKM Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-[10px] text-slate-400 mt-8 font-medium opacity-60">
            &copy; {{ date('Y') }} Kecamatan Digital. Aman & Terpercaya.
        </p>
    </div>

</body>

</html>