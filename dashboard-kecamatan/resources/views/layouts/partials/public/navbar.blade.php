<div class="navbar bg-white shadow-md px-6 py-3 sticky top-0 z-50 border-b border-gray-200">
    <div class="navbar-start">
        <a href="/" class="flex items-center gap-3">
            @if(appProfile()->logo_path)
                <img src="{{ asset('storage/' . appProfile()->logo_path) }}"
                    alt="Logo {{ appProfile()->region_level }} {{ appProfile()->region_name }}"
                    style="height: 60px; width: auto; object-fit: contain;" class="flex-shrink-0">
            @else
                <div
                    class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-landmark text-white text-lg"></i>
                </div>
            @endif
            <div>
                <div class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                    {{ strtoupper(appProfile()->full_region_name) }}
                </div>
                <div class="text-[10px] text-gray-500">{{ appProfile()->app_name }}</div>
            </div>
        </a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1 gap-1">
            <li><a href="{{ request()->is('/') ? '#layanan' : '/#layanan' }}"
                    class="text-sm font-medium text-gray-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg">Layanan</a>
            </li>
            <li><a href="{{ route('landing.wilayah') }}"
                    class="text-sm font-medium text-gray-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg">Wilayah</a>
            </li>
            <li class="dropdown dropdown-hover group">
                <label tabindex="0"
                    class="text-sm font-bold text-slate-600 group-hover:text-teal-600 group-hover:bg-teal-50/50 rounded-full px-4 py-2 transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-briefcase text-teal-500 opacity-80 group-hover:opacity-100"></i>
                    <span>Ekonomi & Jasa</span>
                    <i
                        class="fas fa-chevron-down text-[9px] opacity-40 group-hover:translate-y-0.5 transition-transform duration-300"></i>
                </label>
                <ul tabindex="0"
                    class="dropdown-content z-[200] menu p-3 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.1)] bg-white/95 backdrop-blur-xl border border-white/60 rounded-[1.5rem] w-64 mt-2 animate-[slideUp_0.2s_ease-out]">

                    {{-- Section: Direktori --}}
                    <div class="px-4 py-2 mb-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Direktori</span>
                    </div>

                    <li class="mb-1"><a href="{{ route('public.loker.index') }}"
                            class="py-3 px-4 text-xs font-bold text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-xl flex gap-3 group/item">
                            <span
                                class="w-6 h-6 rounded-lg bg-teal-100/50 flex items-center justify-center text-teal-500 group-hover/item:bg-teal-500 group-hover/item:text-white transition-all">
                                <i class="fas fa-briefcase text-[10px]"></i>
                            </span>
                            Papan Lowongan Kerja
                        </a></li>

                    <li class="mb-1"><a href="{{ route('kerja.index') }}"
                            class="py-3 px-4 text-xs font-bold text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-xl flex gap-3 group/item">
                            <span
                                class="w-6 h-6 rounded-lg bg-teal-100/50 flex items-center justify-center text-teal-500 group-hover/item:bg-teal-500 group-hover/item:text-white transition-all">
                                <i class="fas fa-search text-[10px]"></i>
                            </span>
                            Katalog Pelaku Ekonomi
                        </a></li>

                    <li class="mb-1"><a href="{{ route('umkm_rakyat.nearby') }}"
                            class="py-3 px-4 text-xs font-bold text-slate-600 hover:text-purple-600 hover:bg-purple-50 rounded-xl flex gap-3 group/item">
                            <span
                                class="w-6 h-6 rounded-lg bg-purple-100/50 flex items-center justify-center text-purple-500 group-hover/item:bg-purple-500 group-hover/item:text-white transition-all">
                                <i class="fas fa-map-marked-alt text-[10px]"></i>
                            </span>
                            Peta Sebaran Ekonomi
                        </a></li>

                    {{-- Section: Untuk Warga --}}
                    <div class="px-4 py-2 mt-2 mb-1 border-t border-slate-50">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Untuk Warga</span>
                    </div>

                    <li class="mb-1"><a href="{{ route('public.loker.create') }}"
                            class="py-3 px-4 text-xs font-bold text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-xl flex gap-3 group/item">
                            <span
                                class="w-6 h-6 rounded-lg bg-teal-100/50 flex items-center justify-center text-teal-500 group-hover/item:bg-teal-500 group-hover/item:text-white transition-all">
                                <i class="fas fa-bullhorn text-[10px]"></i>
                            </span>
                            Pasang Info Loker
                        </a></li>

                    <li class="mb-1"><a href="{{ route('umkm_rakyat.create') }}"
                            class="py-3 px-4 text-xs font-bold text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl flex gap-3 group/item">
                            <span
                                class="w-6 h-6 rounded-lg bg-emerald-100/50 flex items-center justify-center text-emerald-500 group-hover/item:bg-emerald-500 group-hover/item:text-white transition-all">
                                <i class="fas fa-plus text-[10px]"></i>
                            </span>
                            Buka Etalase UMKM
                        </a></li>

                    <li><a href="{{ route('umkm_rakyat.login') }}"
                            class="py-3 px-4 text-xs font-black text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl flex gap-3 group/item">
                            <span
                                class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover/item:bg-rose-500 group-hover/item:text-white transition-all">
                                <i class="fas fa-sign-in-alt text-[10px]"></i>
                            </span>
                            Dashboard Seller
                        </a></li>
                </ul>
            </li>
            <li><a href="{{ request()->is('/') ? '#berita' : '/#berita' }}"
                    class="text-sm font-medium text-gray-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg">Berita</a>
            </li>
        </ul>
    </div>
    <div class="navbar-end">
        <a href="{{ route('login') }}"
            class="btn btn-sm bg-teal-600 hover:bg-teal-700 text-white border-0 rounded-lg px-5 font-medium shadow-sm">Masuk</a>
    </div>
</div>