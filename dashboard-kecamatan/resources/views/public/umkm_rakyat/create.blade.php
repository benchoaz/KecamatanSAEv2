<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Etalase UMKM - Kecamatan Digital</title>
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
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col relative overflow-x-hidden">

    <!-- Decorative Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div
            class="absolute top-0 left-0 w-[500px] h-[500px] bg-teal-400/20 rounded-full blur-[100px] -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-400/20 rounded-full blur-[100px] translate-x-1/2 translate-y-1/2">
        </div>
    </div>

    <!-- Simple Navbar -->
    <nav class="w-full py-6 px-6 relative z-10">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                <div
                    class="w-10 h-10 bg-white shadow-md rounded-xl flex items-center justify-center text-teal-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <span class="font-bold text-slate-600 group-hover:text-teal-600 transition-colors">Kembali ke
                    Beranda</span>
            </a>
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo-example.png') }}" class="h-8 w-auto mix-blend-multiply opacity-80"
                    alt="Logo">
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow container mx-auto px-4 py-8 relative z-10 flex items-center justify-center">
        <div class="w-full max-w-2xl">

            <!-- Header Section -->
            <div class="text-center mb-10">
                <div
                    class="inline-flex items-center gap-2 bg-white shadow-sm border border-slate-100 px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Gerakan Ekonomi
                        Digital</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-800 mb-4 tracking-tight">
                    Mulai Bisnis Digital Anda <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">Tanpa
                        Biaya</span>
                </h1>
                <p class="text-slate-500 font-medium text-sm md:text-base max-w-lg mx-auto leading-relaxed">
                    Daftarkan UMKM Anda sekarang dan jangkau lebih banyak pelanggan di wilayah Kecamatan. Proses cepat,
                    mudah, dan gratis selamanya.
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="glass-effect rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-white/50 relative overflow-hidden">
                <!-- Top Accent -->
                <div
                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-400">
                </div>

                <div class="p-8 md:p-12 relative z-10">

                    <!-- Steps Indicator (Visual Guide) -->
                    <div class="flex items-center justify-center mb-10 space-x-4">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-teal-500/30 ring-4 ring-teal-50">
                                1</div>
                            <span class="text-[10px] font-bold text-teal-700 mt-2 uppercase tracking-wide">Profil
                                Usaha</span>
                        </div>
                        <div class="w-12 h-0.5 bg-slate-200"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-sm border border-slate-200">
                                2</div>
                            <span
                                class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-wide">Kontak</span>
                        </div>
                        <div class="w-12 h-0.5 bg-slate-200"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-sm border border-slate-200">
                                <i class="fas fa-check"></i>
                            </div>
                            <span
                                class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-wide">Selesai</span>
                        </div>
                    </div>

                    <form action="{{ route('umkm_rakyat.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-10">
                        @csrf

                        <!-- Section: Identitas Usaha -->
                        <div class="bg-white/50 p-6 rounded-3xl border border-white/60 shadow-sm">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                                <span
                                    class="w-10 h-10 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center text-lg shadow-sm"><i
                                        class="fas fa-store"></i></span>
                                <div>
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Informasi
                                        Toko</h3>
                                    <p class="text-[10px] text-slate-400 font-medium">Lengkapi data agar usaha mudah
                                        ditemukan pelanggan.</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="group">
                                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nama Usaha Anda
                                        <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_usaha" required
                                        placeholder="Contoh: Keripik Singkong Makmur Jaya"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm font-semibold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none placeholder:text-slate-300 shadow-sm">
                                    <p class="text-[10px] text-slate-400 mt-2 ml-1 flex items-center gap-1"><i
                                            class="fas fa-info-circle text-teal-500"></i> Gunakan nama yang unik dan
                                        mudah diingat.</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Kategori / Jenis
                                            Usaha <span class="text-rose-500">*</span></label>
                                        <div class="relative">
                                            <i
                                                class="fas fa-tag absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                            <select name="jenis_usaha" required
                                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-5 py-4 text-sm font-semibold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none appearance-none cursor-pointer shadow-sm">
                                                <option value="" disabled selected>Pilih Kategori Usaha</option>
                                                <option value="Makanan & Minuman">Makanan & Minuman</option>
                                                <option value="Kerajinan Tangan">Kerajinan Tangan</option>
                                                <option value="Fashion & Konveksi">Fashion & Konveksi</option>
                                                <option value="Pertanian & Perkebunan">Pertanian & Perkebunan</option>
                                                <option value="Jasa">Jasa</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Lokasi Desa
                                            <span class="text-rose-500">*</span></label>
                                        <div class="relative">
                                            <i
                                                class="fas fa-map-marker-alt absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                            <select name="desa" required
                                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-5 py-4 text-sm font-semibold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none appearance-none cursor-pointer shadow-sm">
                                                <option value="" disabled selected>Pilih Desa Domisili</option>
                                                @foreach($desas as $desa)
                                                    <option value="{{ $desa->nama_desa }}">{{ $desa->nama_desa }}</option>
                                                @endforeach
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Ceritakan Produk
                                        Anda</label>
                                    <div class="relative">
                                        <textarea name="deskripsi" rows="3"
                                            placeholder="Contoh: Kami menjual keripik singkong renyah dengan bumbu rahasia warisan nenek moyang. Tersedia rasa pedas dan original."
                                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm font-semibold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none placeholder:text-slate-300 resize-none shadow-sm"></textarea>
                                        <div
                                            class="absolute bottom-3 right-3 text-[9px] text-slate-300 font-bold bg-white px-2 py-1 rounded-md border border-slate-100">
                                            Tips: Cerita yang menarik memikat pembeli</div>
                                    </div>
                                </div>

                                <!-- Upload Box Enhanced -->
                                <div class="group">
                                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Foto Tempat Usaha /
                                        Produk</label>
                                    <div class="relative">
                                        <input type="file" name="foto_usaha" id="foto_usaha" class="peer hidden"
                                            accept="image/*" onchange="previewImage(this)">
                                        <label for="foto_usaha"
                                            class="flex flex-col items-center justify-center w-full h-40 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300 cursor-pointer peer-focus:ring-4 peer-focus:ring-teal-500/10 hover:bg-white hover:border-teal-400 transition-all relative overflow-hidden group-hover:shadow-md">

                                            <!-- Preview Container -->
                                            <div id="image-preview"
                                                class="absolute inset-0 bg-cover bg-center hidden z-10"></div>

                                            <div class="flex flex-col items-center relative z-0 transition-opacity duration-300"
                                                id="upload-placeholder">
                                                <div
                                                    class="w-12 h-12 rounded-full bg-teal-50 text-teal-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                    <i class="fas fa-camera text-xl"></i>
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-slate-600 group-hover:text-teal-600">Tekan
                                                    untuk upload foto</span>
                                                <span class="text-[10px] text-slate-400 mt-1">Format JPG/PNG, Maks
                                                    2MB</span>
                                            </div>

                                            <!-- Change Overlay -->
                                            <div id="change-overlay"
                                                class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity z-20 hidden">
                                                <span class="text-white text-xs font-bold"><i
                                                        class="fas fa-sync-alt mr-1"></i> Ganti Foto</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Data Pemilik -->
                        <div class="bg-white/50 p-6 rounded-3xl border border-white/60 shadow-sm">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                                <span
                                    class="w-10 h-10 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center text-lg shadow-sm"><i
                                        class="fas fa-user-shield"></i></span>
                                <div>
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Kontak
                                        Pemilik</h3>
                                    <p class="text-[10px] text-slate-400 font-medium">Data ini digunakan untuk
                                        verifikasi dan pelanggan menghubungi Anda.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nama Lengkap Pemilik
                                        <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_pemilik" required placeholder="Sesuai KTP"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm font-semibold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none placeholder:text-slate-300 shadow-sm">
                                </div>
                                <div class="group">
                                    <label class="block text-xs font-bold text-slate-700 mb-2 ml-1">Nomor WhatsApp Aktif
                                        <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <div
                                            class="absolute left-0 top-0 bottom-0 px-4 bg-slate-100 rounded-l-2xl flex items-center border-y border-l border-slate-200">
                                            <span class="text-xs font-bold text-slate-500">+62</span>
                                        </div>
                                        <input type="number" name="no_wa" required placeholder="812xxxxx"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-16 pr-5 py-4 text-sm font-semibold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none placeholder:text-slate-300 shadow-sm">

                                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/1200px-WhatsApp.svg.png"
                                                class="w-5 h-5 opacity-50" alt="WA">
                                        </div>
                                    </div>
                                    <p
                                        class="text-[10px] text-teal-600 mt-2 ml-1 font-bold bg-teal-50 inline-block px-2 py-1 rounded border border-teal-100">
                                        <i class="fas fa-shield-alt mr-1"></i> Kode Verifikasi akan dikirim ke nomor ini
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <div
                                class="bg-gradient-to-r from-slate-50 to-white border border-slate-100 p-4 rounded-2xl mb-6 flex gap-3 shadow-sm">
                                <div class="flex-shrink-0 text-amber-500 mt-0.5"><i
                                        class="fas fa-exclamation-triangle"></i></div>
                                <p class="text-[11px] text-slate-500 leading-relaxed font-medium">Pastikan data yang
                                    Anda isi sudah benar. Setelah mendaftar, Anda akan diminta untuk melakukan
                                    verifikasi nomor WhatsApp untuk mengaktifkan etalase.</p>
                            </div>

                            <button type="submit"
                                class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-teal-500/20 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 group relative overflow-hidden">
                                <span
                                    class="absolute inset-0 w-full h-full bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></span>
                                <span class="relative z-10">Buka Toko Sekarang <i
                                        class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i></span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            const preview = document.getElementById('image-preview');
                            const placeholder = document.getElementById('upload-placeholder');
                            const overlay = document.getElementById('change-overlay');

                            preview.style.backgroundImage = 'url(' + e.target.result + ')';
                            preview.classList.remove('hidden');
                            placeholder.classList.add('opacity-0');
                            overlay.classList.remove('hidden');
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>

            <!-- Footer Note -->
            <div class="mt-12 text-center pb-8 opacity-60 hover:opacity-100 transition-opacity">
                <p class="text-xs font-semibold text-slate-500">
                    &copy; {{ date('Y') }} Kecamatan Digital. Didukung oleh Pemerintah Kabupaten.
                </p>
            </div>

        </div>
    </div>

</body>

</html>