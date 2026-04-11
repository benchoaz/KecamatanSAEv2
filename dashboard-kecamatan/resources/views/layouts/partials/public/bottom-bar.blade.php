<div
    class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-lg border-t border-slate-100 z-50 lg:hidden px-6 py-3">
    <div class="flex items-center justify-between">
        <a href="/"
            class="flex flex-col items-center gap-1 {{ request()->is('/') ? 'text-teal-600' : 'text-slate-400' }}">
            <i class="fas fa-home text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Beranda</span>
        </a>
        <a href="{{ request()->is('/') ? '#layanan' : '/#layanan' }}"
            class="flex flex-col items-center gap-1 text-slate-400 hover:text-teal-600 transition-colors">
            <i class="fas fa-layer-group text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Layanan</span>
        </a>
        <button onclick="document.getElementById('complaintModal').showModal()"
            class="flex flex-col items-center gap-1 -mt-8 bg-rose-500 text-white w-14 h-14 rounded-full shadow-lg border-4 border-white">
            <i class="fas fa-exclamation-circle mt-3 text-lg"></i>
            <span class="text-[7px] font-black uppercase tracking-tighter mt-0.5">Pengaduan</span>
        </button>
        <a href="{{ route('landing.statistik.index') }}"
            class="flex flex-col items-center gap-1 {{ request()->is('statistik*') ? 'text-teal-600' : 'text-slate-400' }} hover:text-teal-600 transition-colors">
            <i class="fas fa-chart-bar text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Statistik</span>
        </a>
        <a href="{{ route('economy.index') }}"
            class="flex flex-col items-center gap-1 {{ request()->is('ekonomi*') ? 'text-teal-600' : 'text-slate-400' }} hover:text-teal-600 transition-colors">
            <i class="fas fa-store text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">UMKM</span>
        </a>
    </div>
</div>