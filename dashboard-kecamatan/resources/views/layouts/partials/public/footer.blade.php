<footer class="relative bg-slate-950 pt-24 pb-12 border-t border-slate-900/60 overflow-hidden">
    {{-- Background Decoration --}}
    <div
        class="absolute top-0 right-0 w-[500px] h-[500px] bg-teal-500/5 blur-[120px] rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-500/5 blur-[100px] rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none">
    </div>

    <div class="container mx-auto max-w-7xl px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 mb-20">
            <!-- Col 1: Branding (5 cols) -->
            <div class="lg:col-span-5 space-y-10">
                <div class="group flex items-center gap-6">
                    @if(appProfile()->logo_path)
                        <div class="relative">
                            <div class="absolute inset-0 bg-white/20 blur-xl rounded-full scale-150"></div>
                            <img src="{{ asset('storage/' . appProfile()->logo_path) }}"
                                class="relative w-20 h-20 object-contain group-hover:scale-110 transition-transform duration-500 drop-shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                                alt="Logo">
                        </div>
                    @endif
                    <div>
                        <h4
                            class="text-white font-black text-2xl leading-none uppercase tracking-tighter group-hover:text-teal-400 transition-colors">
                            {{ appProfile()->region_name }}
                        </h4>
                        <p
                            class="text-[10px] text-teal-500 font-extrabold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                            <span class="w-6 h-px bg-teal-500/40"></span>
                            {{ appProfile()->tagline }}
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <p class="text-[15px] leading-relaxed text-slate-400 font-medium max-w-sm">
                        <span class="text-white font-bold">{{ appProfile()->app_name }}</span> — Portal informasi publik
                        resmi terintegrasi untuk mendukung percepatan digitalisasi pelayanan masyarakat di wilayah <span
                            class="text-teal-400">{{ appProfile()->region_level }}
                            {{ appProfile()->region_name }}</span>.
                    </p>

                    <div class="pt-4 space-y-4">
                        <h6
                            class="text-white font-black text-[10px] uppercase tracking-[0.3em] flex items-center gap-3">
                            Ikuti Media Sosial
                            <span class="flex-grow h-px bg-white/5"></span>
                        </h6>
                        <div class="flex gap-4">
                            @if(appProfile()->facebook_url)
                                <a href="{{ appProfile()->facebook_url }}" target="_blank"
                                    class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#1877F2] hover:text-white hover:-translate-y-1.5 transition-all duration-300 shadow-xl group"
                                    aria-label="Facebook">
                                    <i class="fab fa-facebook-f text-lg"></i>
                                </a>
                            @endif
                            @if(appProfile()->instagram_url)
                                <a href="{{ appProfile()->instagram_url }}" target="_blank"
                                    class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:text-white hover:-translate-y-1.5 transition-all duration-300 shadow-xl group"
                                    aria-label="Instagram">
                                    <i class="fab fa-instagram text-lg"></i>
                                </a>
                            @endif
                            @if(appProfile()->youtube_url)
                                <a href="{{ appProfile()->youtube_url }}" target="_blank"
                                    class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#FF0000] hover:text-white hover:-translate-y-1.5 transition-all duration-300 shadow-xl group"
                                    aria-label="YouTube">
                                    <i class="fab fa-youtube text-lg"></i>
                                </a>
                            @endif
                            @if(appProfile()->x_url)
                                <a href="{{ appProfile()->x_url }}" target="_blank"
                                    class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:bg-black hover:text-white hover:-translate-y-1.5 transition-all duration-300 shadow-xl group"
                                    aria-label="X">
                                    <i class="fab fa-x-twitter text-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Col 2: Navigation (3 cols) -->
            <div class="lg:col-span-3">
                <h5 class="text-white font-black text-xs uppercase tracking-[0.2em] mb-10 flex items-center gap-3">
                    <span class="w-8 h-px bg-teal-500"></span>
                    Navigasi
                </h5>
                <ul class="space-y-5">
                    <li>
                        <a href="/"
                            class="text-slate-400 hover:text-white transition-all flex items-center gap-3 group">
                            <i
                                class="fas fa-chevron-right text-[10px] text-teal-500 group-hover:translate-x-1 transition-transform"></i>
                            <span class="font-bold text-sm">Beranda</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ request()->is('/') ? '#layanan' : '/#layanan' }}"
                            class="text-slate-400 hover:text-white transition-all flex items-center gap-3 group">
                            <i
                                class="fas fa-chevron-right text-[10px] text-teal-500 group-hover:translate-x-1 transition-transform"></i>
                            <span class="font-bold text-sm">Layanan Publik</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ request()->is('/') ? '#berita' : '/#berita' }}"
                            class="text-slate-400 hover:text-white transition-all flex items-center gap-3 group">
                            <i
                                class="fas fa-chevron-right text-[10px] text-teal-500 group-hover:translate-x-1 transition-transform"></i>
                            <span class="font-bold text-sm">Berita & Informasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('economy.index', ['tab' => 'jasa']) }}"
                            class="text-slate-400 hover:text-white transition-all flex items-center gap-3 group">
                            <i
                                class="fas fa-chevron-right text-[10px] text-teal-500 group-hover:translate-x-1 transition-transform"></i>
                            <span class="font-bold text-sm">Ekonomi & Jasa</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Contact Details (4 cols) -->
            <div class="lg:col-span-4">
                <h5 class="text-white font-black text-xs uppercase tracking-[0.2em] mb-10 flex items-center gap-3">
                    <span class="w-8 h-px bg-blue-500"></span>
                    Hubungi Kami
                </h5>
                <div class="space-y-8">
                    <div class="flex gap-5 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-teal-500 group-hover:bg-teal-500/10 group-hover:border-teal-500/30 transition-all duration-300">
                            <i class="fas fa-map-marked-alt text-xl"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Alamat
                                Kantor</span>
                            <p
                                class="text-white font-bold leading-relaxed text-sm group-hover:text-teal-400 transition-colors">
                                {{ appProfile()->address ?? 'Alamat Belum Diatur' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-5 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-blue-500 group-hover:bg-blue-500/10 group-hover:border-blue-500/30 transition-all duration-300">
                            <i class="fas fa-phone-volume text-xl"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Hotline
                                Resmi</span>
                            <p class="text-white font-black text-lg group-hover:text-blue-400 transition-colors">
                                {{ appProfile()->phone ?? '(0335) 123456' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Attribution Bar --}}
        <div class="pt-10 border-t border-slate-800/80 flex flex-col lg:flex-row justify-between items-center gap-8">
            <div class="order-2 lg:order-1 text-center lg:text-left space-y-2">
                <p class="text-sm text-slate-500 font-medium">
                    &copy; {{ date('Y') }} <span
                        class="text-white font-black uppercase tracking-tighter">{{ appProfile()->full_region_name }}</span>.
                    Seluruh Hak Cipta Dilindungi.
                </p>
                <div
                    class="flex flex-wrap justify-center lg:justify-start gap-x-6 gap-y-2 text-[11px] font-bold uppercase tracking-widest text-slate-600">
                    <a href="#" class="hover:text-teal-400 transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-teal-400 transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-teal-400 transition-colors">Peta Situs</a>
                </div>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-4 order-1 lg:order-2">
                <div
                    class="group flex items-center gap-3 px-5 py-2.5 bg-slate-900 border border-slate-800 rounded-2xl hover:border-teal-500/30 transition-all duration-300">
                    <div
                        class="w-8 h-8 rounded-lg bg-teal-500/10 flex items-center justify-center text-teal-400 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </div>
                    <span
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-200 transition-colors">Portal
                        Resmi Pemerintah</span>
                </div>
                <div
                    class="group flex items-center gap-3 px-5 py-2.5 bg-slate-900 border border-slate-800 rounded-2xl hover:border-amber-500/30 transition-all duration-300">
                    <div
                        class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bolt text-sm"></i>
                    </div>
                    <span
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-200 transition-colors">Powered
                        by Sae-Digital</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Essential SEO Keywords (Visually Hidden) --}}
    <div class="sr-only text-slate-900 opacity-0 pointer-events-none">
        Pemerintahan {{ appProfile()->region_level }} {{ appProfile()->region_name }},
        Layanan publik {{ appProfile()->region_name }},
        Kantor Kecamatan {{ appProfile()->region_name }},
        Informatika Kecamatan {{ appProfile()->region_name }},
        Website Desa Probolinggo.
    </div>
</footer>