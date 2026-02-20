@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12">
        <!-- Hero Section -->
        <div class="container mx-auto px-6 mb-16">
            <div
                class="bg-gradient-to-br from-teal-600 to-teal-800 rounded-[3rem] p-8 md:p-16 text-white relative overflow-hidden shadow-2xl shadow-teal-900/20">
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-black/10 rounded-full blur-3xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                    <div class="max-w-2xl text-center md:text-left">
                        <div
                            class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-full mb-6 text-[10px] font-black uppercase tracking-widest">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Ekonomi Kerakyatan</span>
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight">UMKM Rakyat</h1>
                        <p class="text-teal-50 font-medium text-lg leading-relaxed opacity-90">
                            Lapak online milik warga.
                        </p>
                        <div class="mt-10 flex flex-wrap justify-center md:justify-start gap-4">
                            <a href="{{ route('umkm_rakyat.create') }}"
                                class="bg-white text-teal-700 font-black px-8 py-4 rounded-2xl shadow-lg hover:bg-teal-50 transition-all transform hover:-translate-y-1 active:scale-95">
                                Buka Lapak UMKM
                            </a>
                            <a href="#explore"
                                class="bg-teal-500/30 backdrop-blur-md border border-white/20 text-white font-black px-8 py-4 rounded-2xl hover:bg-teal-500/50 transition-all">
                                Lihat Produk Terbaru
                            </a>
                        </div>
                    </div>
                    <!-- Mini Stats Overlay -->
                    <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                        <div class="bg-white/10 backdrop-blur-md p-6 rounded-[2rem] border border-white/20">
                            <div class="text-3xl font-black mb-1">{{ $umkms->total() }}</div>
                            <div class="text-[9px] font-black uppercase tracking-widest opacity-80">Pelaku UMKM</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-6 rounded-[2rem] border border-white/20">
                            <div class="text-3xl font-black mb-1">{{ count($desas) }}</div>
                            <div class="text-[9px] font-black uppercase tracking-widest opacity-80">Desa Terintegrasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="explore" class="container mx-auto px-6">
            <!-- Filter & Search Bar -->
            <div
                class="bg-white rounded-[2rem] p-4 shadow-xl shadow-slate-200/50 mb-12 flex flex-col md:flex-row items-center gap-4">
                <div class="relative flex-1 w-full">
                    <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <form action="{{ route('umkm_rakyat.index') }}" method="GET">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari nama usaha atau jenis produk..."
                            class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 transition-all">
                    </form>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <select onchange="window.location.href=this.value"
                        class="flex-1 md:w-48 bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 transition-all">
                        <option value="{{ route('umkm_rakyat.index') }}">Semua Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ route('umkm_rakyat.index', ['desa' => $desa->nama_desa]) }}" {{ request('desa') == $desa->nama_desa ? 'selected' : '' }}>
                                {{ $desa->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                    <button
                        class="bg-slate-800 text-white p-4 rounded-2xl px-6 font-black text-xs uppercase tracking-widest">
                        Filter
                    </button>
                </div>
            </div>

            <!-- UMKM Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($umkms as $item)
                    <div
                        class="group relative bg-white rounded-[2.5rem] p-3 shadow-lg shadow-slate-200/50 border border-slate-100 hover:border-teal-200 hover:shadow-[0_20px_50px_-12px_rgba(20,184,166,0.15)] transition-all duration-500 flex flex-col h-full hover:-translate-y-2">
                        <!-- Image Wrapper -->
                        <div class="relative aspect-[4/3] rounded-[2rem] overflow-hidden mb-5 z-10">
                            @if($item->foto_usaha)
                                <img src="{{ asset('storage/' . $item->foto_usaha) }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out"
                                    alt="{{ $item->nama_usaha }}">
                            @else
                                <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-300">
                                    <i class="fas fa-store text-5xl opacity-50"></i>
                                </div>
                            @endif

                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span
                                    class="bg-white/95 backdrop-blur-md text-teal-700 text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest shadow-sm border border-white/50">
                                    {{ $item->jenis_usaha }}
                                </span>
                            </div>

                            <!-- Link Overlay -->
                            <a href="{{ route('umkm_rakyat.show', $item->slug) }}"
                                class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors z-20"></a>
                        </div>

                        <!-- Info -->
                        <div class="px-2 pb-2 flex-grow flex flex-col relative z-20">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                <span
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $item->desa }}</span>
                            </div>

                            <h3
                                class="text-xl font-black text-slate-800 mb-2 truncate group-hover:text-teal-600 transition-colors">
                                <a href="{{ route('umkm_rakyat.show', $item->slug) }}">{{ $item->nama_usaha }}</a>
                            </h3>

                            <p class="text-xs text-slate-500 font-medium line-clamp-2 mb-6 leading-relaxed h-8">
                                {{ $item->deskripsi ?? 'Akses produk unggulan dari tangan pelaku usaha kreatif ' . $item->desa . '.' }}
                            </p>

                            <!-- Bottom Action -->
                            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs border border-slate-200 shadow-sm">
                                        {{ substr($item->nama_pemilik, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Pemilik</span>
                                        <span
                                            class="text-xs font-bold text-slate-700 truncate max-w-[80px]">{{ explode(' ', $item->nama_pemilik)[0] }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('umkm_rakyat.show', $item->slug) }}"
                                    class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center hover:bg-teal-600 hover:text-white transition-all shadow-sm group/arrow border border-teal-100">
                                    <i
                                        class="fas fa-arrow-right group-hover/arrow:-rotate-45 transition-transform duration-300"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div
                            class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 text-4xl animate-pulse">
                            <i class="fas fa-store-slash"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-2">Belum ada UMKM</h3>
                        <p class="text-slate-500 font-medium max-w-sm mx-auto mb-6">Jadilah yang pertama membuka etalase di
                            sini!</p>
                        <a href="{{ route('umkm_rakyat.create') }}"
                            class="inline-block bg-teal-600 text-white font-black px-8 py-3 rounded-2xl shadow-lg hover:bg-teal-700 transition-colors">
                            Ajak UMKM Pertama Daftar
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-16">
                {{ $umkms->links() }}
            </div>
        </div>
    </div>
@endsection