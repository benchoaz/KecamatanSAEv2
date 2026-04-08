<div class="navbar bg-white shadow-md px-6 py-3 sticky top-0 z-50 border-b border-gray-200">
    <div class="navbar-start">
        <a href="/" class="flex items-center gap-3">
            @if(appProfile()->logo_path && Storage::disk('public')->exists(appProfile()->logo_path))
                <img src="{{ asset('storage/' . appProfile()->logo_path) }}"
                    alt="Logo {{ appProfile()->region_name }}"
                    class="h-10 md:h-14 lg:h-16 w-auto object-contain flex-shrink-0 transition-transform hover:scale-105">
            @else
                <div
                    class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                    <img src="{{ asset('assets/images/logo-garuda.png') }}" alt="Logo Garuda" class="h-8 object-contain">
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
            <li><a href="{{ route('economy.index') }}"
                    class="text-sm font-medium {{ request()->is('ekonomi*') ? 'text-teal-600 bg-teal-50' : 'text-gray-600 hover:text-teal-600 hover:bg-teal-50' }} rounded-lg">Pusat
                    Ekonomi</a>
            </li>
            <li><a href="{{ route('landing.wilayah') }}"
                    class="text-sm font-medium {{ request()->is('wilayah*') ? 'text-teal-600 bg-teal-50' : 'text-gray-600 hover:text-teal-600 hover:bg-teal-50' }} rounded-lg">Peta
                    Wilayah</a>
            </li>
            <li><a href="{{ request()->is('/') ? '#berita' : '/#berita' }}"
                    class="text-sm font-medium text-gray-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg">Berita</a>
            </li>
        </ul>
    </div>
    <div class="navbar-end gap-2">
        <button onclick="document.getElementById('publicServiceModal').showModal()"
            class="btn btn-sm btn-ghost text-slate-500 hover:text-teal-600 hover:bg-teal-50 hidden lg:flex rounded-lg px-4 font-bold border-0">
            <i class="fas fa-headset mr-1"></i> Bantuan
        </button>
        <button onclick="document.getElementById('complaintModal').showModal()"
            class="btn btn-sm bg-rose-100/50 hover:bg-rose-500 text-rose-600 hover:text-white border-0 hidden lg:flex rounded-lg px-5 font-bold shadow-sm transition-colors">
            <i class="fas fa-exclamation-circle mr-1"></i> Lapor!
        </button>
        <div class="hidden lg:block w-px h-6 bg-slate-200 mx-1"></div>
        <a href="{{ route('login') }}"
            class="btn btn-sm bg-teal-600 hover:bg-teal-700 text-white border-0 rounded-lg px-6 font-bold shadow-md">Portal Admin</a>
    </div>
</div>