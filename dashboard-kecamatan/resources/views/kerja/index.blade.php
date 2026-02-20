@extends('layouts.public')

@section('title', 'Pusat Ekonomi & Kreatif Warga – ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('meta')
    <meta name="description"
        content="Pusat ekonomi dan kreatif warga {{ appProfile()->region_level }} {{ appProfile()->region_name }}. Temukan produk UMKM, jasa tukang, dan lowongan kerja lokal.">
@endsection

@section('content')
    <div class="min-h-screen bg-slate-50" x-data="{ activeTab: 'jasa' }">

        {{-- Header Section --}}
        <div class="bg-gradient-to-r from-teal-600 to-emerald-600 text-white pt-16 pb-24 relative overflow-hidden">
            {{-- Background Patterns --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-400/10 rounded-full blur-2xl translate-y-1/3 -translate-x-1/4">
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-4xl">
                    <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">
                        Pusat Ekonomi & Kreatif
                    </h1>
                    <p class="text-xl text-teal-50 mb-8 leading-relaxed max-w-2xl opacity-90">
                        Platform terintegrasi untuk mendukung ekonomi warga <strong>{{ appProfile()->region_level }}
                            {{ appProfile()->region_name }}</strong>.
                        Temukan produk lokal unggulan, jasa keahlian, dan peluang karier dalam satu pintu.
                    </p>

                    {{-- Tab Navigation --}}
                    <div class="flex flex-wrap gap-3">
                        <button @click="activeTab = 'jasa'"
                            :class="activeTab === 'jasa' ? 'bg-white text-teal-700 shadow-xl scale-105' : 'bg-teal-800/40 text-teal-100 hover:bg-teal-800/60 border-teal-500/30'"
                            class="px-6 py-3.5 rounded-2xl font-bold transition-all duration-300 flex items-center gap-2.5 border backdrop-blur-sm">
                            <i class="fas fa-briefcase text-lg"
                                :class="activeTab === 'jasa' ? 'text-teal-600' : 'text-teal-200'"></i>
                            <span>Pekerjaan & Jasa</span>
                        </button>
                        <button @click="activeTab = 'produk'"
                            :class="activeTab === 'produk' ? 'bg-white text-teal-700 shadow-xl scale-105' : 'bg-teal-800/40 text-teal-100 hover:bg-teal-800/60 border-teal-500/30'"
                            class="px-6 py-3.5 rounded-2xl font-bold transition-all duration-300 flex items-center gap-2.5 border backdrop-blur-sm">
                            <i class="fas fa-store text-lg"
                                :class="activeTab === 'produk' ? 'text-teal-600' : 'text-teal-200'"></i>
                            <span>Etalase Produk UMKM</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 1: PEKERJAAN & JASA --}}
        <div x-show="activeTab === 'jasa'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-8 -mt-12 relative z-20 pb-20">

            {{-- Filter Section --}}
            <div class="container mx-auto px-6">
                <div class="bg-white rounded-3xl shadow-xl p-6 border border-slate-100">
                    {{-- Search & Filter Row --}}
                    <div class="flex flex-col lg:flex-row gap-6 justify-between items-start lg:items-center mb-6">
                        {{-- Quick Filters --}}
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('kerja.index') }}"
                                class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all {{ !request('kategori') ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-100' }}">
                                Semua
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('kerja.index', ['kategori' => $cat]) }}"
                                    class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all {{ request('kategori') == $cat ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-100' }}">
                                    {{ $cat }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Search --}}
                        <form method="GET" action="{{ route('kerja.index') }}" class="w-full lg:w-80 relative">
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari jasa..."
                                class="w-full pl-5 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                            <button type="submit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-teal-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    {{-- CTA Registration --}}
                    <div
                        class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                        <div
                            class="absolute right-0 top-0 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl translate-x-1/3 -translate-y-1/4">
                        </div>
                        <div class="relative z-10">
                            <h4 class="font-bold text-lg mb-1 flex items-center gap-2">
                                <i class="fas fa-id-badge text-teal-400"></i>
                                Punya Keahlian?
                            </h4>
                            <p class="text-sm text-slate-300 max-w-md">
                                Daftarkan jasa atau keahlian Anda di direktori resmi kecamatan. Gratis dan terverifikasi.
                            </p>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', appProfile()->phone ?? '') }}?text=Halo%20Admin%20{{ appProfile()->region_level }}%20{{ appProfile()->region_name }},%20saya%20ingin%20mendaftarkan%20jasa/pekerjaan%20saya%20di%20Direktori%20Kerja."
                            target="_blank"
                            class="px-6 py-3 bg-teal-600 hover:bg-teal-500 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-teal-500/20 transition-all whitespace-nowrap relative z-10">
                            <i class="fab fa-whatsapp mr-2"></i> Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>

            {{-- Work Items Grid --}}
            <div class="container mx-auto px-6">
                @if($workItems->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($workItems as $item)
                            <div
                                class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 group">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div
                                            class="w-14 h-14 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600 text-2xl group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                                            <i class="fas {{ $item->icon }}"></i>
                                        </div>
                                        <span
                                            class="px-3 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-slate-100">
                                            {{ $item->job_category }}
                                        </span>
                                    </div>

                                    <h3
                                        class="text-lg font-black text-slate-800 mb-1 leading-tight group-hover:text-teal-700 transition-colors">
                                        {{ $item->job_title }}
                                    </h3>
                                    <p class="text-sm font-medium text-slate-500 mb-4">{{ $item->display_name }}</p>

                                    <div class="space-y-2 mb-6 text-xs text-slate-500">
                                        @if($item->service_area)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-teal-500 w-4 text-center"></i>
                                                <span>{{ $item->service_area }}</span>
                                            </div>
                                        @endif
                                        @if($item->service_time)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-clock text-teal-500 w-4 text-center"></i>
                                                <span>{{ $item->service_time }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex gap-2 pt-4 border-t border-slate-50">
                                        <a href="{{ $item->whatsapp_link }}" target="_blank"
                                            class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-center text-xs transition-colors flex items-center justify-center gap-2">
                                            <i class="fab fa-whatsapp"></i> Chat
                                        </a>
                                        <a href="{{ route('kerja.show', $item->id) }}"
                                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition-colors">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $workItems->appends(['kategori' => request('kategori'), 'q' => request('q')])->links() }}
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-slate-300 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700">Tidak Ada Data</h3>
                        <p class="text-sm text-slate-500">Belum ada data pekerjaan untuk kategori ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 2: PRODUK UMKM --}}
        <div x-show="activeTab === 'produk'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="container mx-auto px-6 py-12 -mt-12 relative z-20 min-h-[500px]" style="display: none;">

            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm mb-8 text-center">
                <h3 class="text-2xl font-black text-slate-800 mb-2">Etalase Produk Unggulan</h3>
                <p class="text-slate-500 max-w-2xl mx-auto">
                    Produk olahan dan kerajinan terbaik karya warga {{ appProfile()->region_level }}
                    {{ appProfile()->region_name }}.
                    Dukung ekonomi lokal dengan membeli produk tetangga.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($umkms as $u)
                    <div
                        class="group bg-white rounded-[2rem] overflow-hidden border border-slate-100 hover:shadow-2xl hover:shadow-teal-900/10 transition-all duration-300 flex flex-col h-full relative">
                        @if($u->is_featured)
                            <div class="absolute top-4 left-4 z-10">
                                <span
                                    class="bg-amber-400 text-amber-950 text-[9px] font-black px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1">
                                    <i class="fas fa-star"></i> PILIHAN
                                </span>
                            </div>
                        @endif

                        <div class="aspect-square relative overflow-hidden bg-slate-100">
                            <img src="{{ $u->image_path ? asset('storage/' . $u->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}"
                                alt="{{ $u->product }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>

                        <div class="p-6 flex flex-col flex-1">
                            <div class="mb-4">
                                <p class="text-[9px] font-bold text-teal-600 uppercase tracking-widest mb-1 truncate">
                                    {{ $u->name }}</p>
                                <h3
                                    class="font-extrabold text-slate-800 text-lg leading-tight group-hover:text-teal-700 transition-colors line-clamp-2">
                                    {{ $u->product }}
                                </h3>
                            </div>

                            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between gap-3">
                                <div>
                                    @if($u->price)
                                        <div class="text-base font-black text-slate-900">Rp
                                            {{ number_format($u->price, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-xs font-bold text-slate-400 italic">Hubungi Penjual</div>
                                    @endif
                                </div>
                                <a href="{{ route('public.umkm.show', $u->id) }}"
                                    class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-teal-600 hover:text-white transition-colors">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <i class="fas fa-store-slash text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700">Belum Ada Produk</h3>
                        <p class="text-slate-500 text-sm">Data produk UMKM belum tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('public.umkm.index') }}"
                    class="btn bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl px-8 font-bold shadow-sm transition-all hover:shadow-md animate-bounce">
                    <i class="fas fa-th-large mr-2"></i> Lihat Semua Produk
                </a>
            </div>
        </div>
    </div>
@endsection