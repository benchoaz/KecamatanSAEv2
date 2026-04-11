@extends('layouts.public')

@section('title', 'Pusat Ekonomi & Kreatif Warga – ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('meta')
    <meta name="description"
        content="Pusat ekonomi dan kreatif warga {{ appProfile()->region_level }} {{ appProfile()->region_name }}. Temukan produk UMKM, jasa tukang, dan lowongan kerja lokal.">
@endsection

@section('content')
    <div class="min-h-screen bg-slate-50" x-data="{ activeTab: '{{ $defaultTab }}' }">

        {{-- Success Message --}}
        @session('success')
            <div class="container mx-auto px-6 pt-6">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-xl p-4 flex items-center gap-3 shadow-lg animate__animated animate__fadeInDown">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-emerald-800">{{ $value }}</p>
                    </div>
                </div>
            </div>
        @endsession

        {{-- Header Section - Scaled down --}}
        <div class="bg-gradient-to-r from-teal-800 to-slate-800 text-white pt-10 pb-16 relative overflow-hidden">
            {{-- Background Patterns --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-400/10 rounded-full blur-2xl translate-y-1/3 -translate-x-1/4">
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-3xl">
                    <h1 class="text-3xl md:text-4xl font-black mb-3 tracking-tight">
                        Pusat Ekonomi & Kreatif
                    </h1>
                    <p class="text-base text-teal-50 mb-8 leading-relaxed max-w-2xl opacity-90">
                        Platform terintegrasi untuk mendukung ekonomi warga <strong>{{ appProfile()->region_level }}
                            {{ appProfile()->region_name }}</strong>.
                        Temukan produk lokal unggulan, jasa keahlian, dan peluang karier dalam satu pintu.
                    </p>

                    {{-- Global Search Bar - Glassmorphism --}}
                    <div class="mt-8 max-w-xl animate__animated animate__fadeInUp animate__delay-1s">
                        <form action="{{ route('economy.index') }}" method="GET" class="relative group">
                            {{-- Tetap di tab yang sama saat cari --}}
                            <input type="hidden" name="tab" :value="activeTab">
                            
                            <div class="relative overflow-hidden rounded-2xl">
                                <input type="text" name="q" value="{{ request('q') }}" 
                                    placeholder="Cari produk atau jasa..."
                                    class="w-full bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl py-4 md:py-5 pl-11 pr-24 text-white placeholder-teal-100 focus:outline-none focus:bg-white/20 transition-all shadow-2xl text-xs md:text-base">
                                
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-teal-200">
                                    <i class="fas fa-search text-lg"></i>
                                </div>

                                <button type="submit" 
                                    class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-white text-teal-800 px-4 md:px-6 py-2 md:py-3 rounded-xl font-black text-[10px] md:text-sm hover:bg-teal-50 shadow-lg transform hover:scale-105 active:scale-95 transition-all">
                                    CARI
                                </button>
                            </div>
                            
                            {{-- Hint --}}
                            @if(!request('q'))
                            <div class="mt-3 flex flex-wrap gap-3 items-center ml-1">
                                <span class="text-[10px] font-bold text-teal-100/60 uppercase tracking-widest">Populer:</span>
                                <a href="?q=bakso&tab=produk" class="text-[10px] bg-white/10 hover:bg-white/20 px-2 py-0.5 rounded-lg border border-white/10 text-white transition-all">Bakso</a>
                                <a href="?q=tukang&tab=jasa" class="text-[10px] bg-white/10 hover:bg-white/20 px-2 py-0.5 rounded-lg border border-white/10 text-white transition-all">Tukang</a>
                                <a href="?q=keripik&tab=produk" class="text-[10px] bg-white/10 hover:bg-white/20 px-2 py-0.5 rounded-lg border border-white/10 text-white transition-all">Keripik</a>
                            </div>
                            @endif
                        </form>
                    </div>

                    {{-- Tab Navigation --}}
                    <div id="economy-tabs" class="flex flex-wrap gap-2.5 mt-10">
                        <button @click="activeTab = 'jasa'"
                            :class="activeTab === 'jasa' ? 'bg-white text-teal-800 shadow-lg scale-105' : 'bg-slate-800/40 text-teal-100 hover:bg-slate-800/60 border-slate-500/30'"
                            class="px-5 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center gap-2 border backdrop-blur-sm text-sm">
                            <i class="fas fa-briefcase text-base"
                                :class="activeTab === 'jasa' ? 'text-teal-700' : 'text-teal-200'"></i>
                            <span>Pekerjaan & Jasa</span>
                        </button>
                        <button @click="activeTab = 'produk'"
                            :class="activeTab === 'produk' ? 'bg-white text-teal-800 shadow-lg scale-105' : 'bg-slate-800/40 text-teal-100 hover:bg-slate-800/60 border-slate-500/30'"
                            class="px-5 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center gap-2 border backdrop-blur-sm text-sm">
                            <i class="fas fa-store text-base"
                                :class="activeTab === 'produk' ? 'text-teal-700' : 'text-teal-200'"></i>
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
                <div class="bg-white rounded-2xl shadow-lg p-5 border border-slate-100">
                    {{-- Search & Filter Row --}}
                    <div class="flex flex-col lg:flex-row gap-6 justify-between items-start lg:items-center mb-6">
                        {{-- Quick Filters --}}
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('economy.index') }}"
                                class="px-4 py-2 rounded-xl font-bold text-xs transition-all {{ !request('kategori') ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-100' }}">
                                Semua
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('economy.index', ['kategori' => $cat]) }}"
                                    class="px-4 py-2 rounded-xl font-bold text-xs transition-all {{ request('kategori') == $cat ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-100' }}">
                                    {{ $cat }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Search --}}
                        <form method="GET" action="{{ route('economy.index') }}" class="w-full lg:w-72 relative">
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari jasa..."
                                class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-medium focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                            <button type="submit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-teal-600">
                                <i class="fas fa-search text-xs"></i>
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
                        <div class="flex flex-wrap items-center gap-4 relative z-10">
                            <a href="{{ route('economy.create') }}"
                                target="_blank"
                                class="px-6 py-3 bg-teal-600 hover:bg-teal-500 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-teal-500/20 transition-all whitespace-nowrap">
                                <i class="fas fa-plus mr-2"></i> Daftar Sekarang
                            </a>
                            <a href="{{ route('economy.login') }}"
                                class="px-6 py-3 bg-transparent hover:bg-white/10 text-white border border-white/20 rounded-xl font-bold text-sm transition-all whitespace-nowrap">
                                <i class="fas fa-key mr-2"></i> Kelola Jasa Saya
                            </a>
                        </div>
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
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span
                                                class="px-3 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-slate-100">
                                                {{ $item->job_category }}
                                            </span>
                                            @php $opStatus = $item->operational_status; @endphp
                                            <span class="px-3 py-1 bg-{{ $opStatus['color'] }}-50 text-{{ $opStatus['color'] }}-600 text-[10px] font-extrabold uppercase rounded-lg border border-{{ $opStatus['color'] }}-100 shadow-sm">
                                                <i class="fas {{ $opStatus['icon'] }} mr-1"></i> {{ $opStatus['label'] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Body: Klik ke detail --}}
                                    <a href="{{ route('economy.show', $item->id) }}" class="block">
                                        <h3 class="text-lg font-black text-slate-800 mb-1 leading-tight group-hover:text-teal-700 transition-colors flex items-center gap-1.5">
                                            {{ $item->job_title }}
                                            @if($item->is_verified)
                                                <i class="fas fa-check-circle text-blue-500 text-sm" title="Terverifikasi Resmi"></i>
                                            @endif
                                        </h3>
                                        <div class="flex items-center justify-between mb-4">
                                            <p class="text-xs font-medium text-slate-500">{{ $item->display_name }}</p>
                                            @if($item->price)
                                                <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
                                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>

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
                                    </a>

                                    <div class="flex gap-2 pt-4 border-t border-slate-50">
                                        <a href="{{ $item->whatsapp_link }}" target="_blank"
                                            class="px-4 py-2.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 flex-shrink-0">
                                            <i class="fab fa-whatsapp"></i> Chat
                                        </a>
                                        <a href="{{ route('economy.show', $item->id) }}"
                                            class="flex-1 px-4 py-2.5 bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white rounded-xl font-bold transition-all text-center text-xs flex items-center justify-center">
                                            Lihat Detail
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

            {{-- Shopee-Style Category Grid (Minimalist) --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm mb-8 animate__animated animate__fadeIn">
                <div class="flex items-center justify-between mb-6 px-2">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.2em]">Kategori Pilihan</h3>
                    <div class="h-px bg-slate-100 flex-grow mx-4"></div>
                </div>
                
                <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10 gap-y-8 gap-x-2">
                    @foreach($umkmCategories as $uCat)
                    <a href="?q={{ $uCat['name'] }}&tab=produk" class="group flex flex-col items-center gap-2.5 transition-all">
                        <div class="w-12 h-12 md:w-14 md:h-14 {{ $uCat['color_class'] }} rounded-2xl flex items-center justify-center text-lg md:text-xl shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all duration-300">
                            <i class="fas {{ $uCat['icon'] }}"></i>
                        </div>
                        <span class="text-[10px] md:text-[11px] font-bold text-slate-500 text-center leading-tight group-hover:text-teal-600 transition-colors">
                            {{ $uCat['name'] }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- CTA for Self-Registration (Minimalist) --}}
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6 mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-amber-500 text-xl border border-slate-100">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Punya Produk Unggulan?</h4>
                        <p class="text-xs text-slate-500">Buka etalase toko digital Anda di platform kecamatan gratis.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('umkm_rakyat.create') }}"
                        class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl font-bold text-xs shadow-sm transition-all">
                        Buka Toko
                    </a>
                    <a href="{{ route('umkm_rakyat.login') }}"
                        class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl font-bold text-xs shadow-sm transition-all">
                        Masuk Seller Center
                    </a>
                </div>
            </div>

            {{-- SUB-TAB 1: UMKM TERVERIFIKASI (OFFICIAL) --}}
            @if($officialUmkms->count() > 0)
            <div class="mb-12">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-slate-800 leading-none">Bisnis Terverifikasi</h4>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Unggulan Kecamatan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($officialUmkms as $o)
                    @php
                        $oLink = route('umkm_rakyat.show', $o->slug);
                    @endphp
                    <a href="{{ $oLink }}" class="group relative bg-slate-900 rounded-[1.5rem] md:rounded-[2.5rem] overflow-hidden shadow-2xl hover:-translate-y-2 transition-all duration-500 block h-64">
                        @if($o->foto_usaha)
                            <img src="{{ asset('storage/' . $o->foto_usaha) }}" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-1000">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-teal-600 to-indigo-700 opacity-80"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                        
                        <div class="absolute inset-0 p-8 flex flex-col justify-end">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="bg-amber-400 text-amber-950 text-[9px] font-black px-2.5 py-1 rounded-lg shadow-lg flex items-center gap-1 uppercase tracking-wider">
                                    <i class="fas fa-star"></i> OFFICIAL
                                </span>
                                <span class="bg-white/10 backdrop-blur-md text-white text-[9px] font-bold px-2.5 py-1 rounded-lg border border-white/20 uppercase tracking-widest">
                                    {{ $o->jenis_usaha }}
                                </span>
                                @if($o->verification_level == 'legal')
                                <span class="bg-blue-500/80 backdrop-blur-md text-white text-[9px] font-black px-2.5 py-1 rounded-lg border border-blue-400 shadow-sm flex items-center gap-1 uppercase tracking-wider">
                                    <i class="fas fa-certificate"></i> NIB LEGAL
                                </span>
                                @elseif($o->verification_level == 'warga')
                                <span class="bg-teal-500/80 backdrop-blur-md text-white text-[9px] font-black px-2.5 py-1 rounded-lg border border-teal-400 shadow-sm flex items-center gap-1 uppercase tracking-wider">
                                    <i class="fas fa-user-check"></i> WARGA
                                </span>
                                @endif
                            </div>
                            <h3 class="text-2xl md:text-3xl font-black text-white mb-1 group-hover:text-amber-300 transition-colors flex items-center gap-2">
                                {{ $o->nama_usaha }}
                                @if($o->verification_level == 'legal')
                                    <i class="fas fa-check-circle text-blue-400 text-xl shadow-sm" title="Terverifikasi Legal (NIB)"></i>
                                @elseif($o->verification_level == 'warga')
                                    <i class="fas fa-check-circle text-teal-400 text-xl shadow-sm" title="Warga Terverifikasi"></i>
                                @endif
                            </h3>
                            <p class="text-white/70 text-sm font-medium flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-teal-400"></i> {{ $o->desa }}
                                <span class="mx-2 text-white/20">|</span>
                                <i class="fas fa-box-open text-amber-400"></i> {{ $o->products_count ?? 'Lihat Produk' }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- SUB-TAB 2: KATALOG PRODUK CEPAT (LOCAL) --}}
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-th-large"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-slate-800 leading-none">Katalog Produk Warga</h4>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Produk & Olahan Lokal</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($localUmkms as $u)
                    @php
                        $waPhone = preg_replace('/[^0-9]/', '', $u->contact_wa ?? '');
                        if (str_starts_with($waPhone, '0')) {
                            $waPhone = '62' . substr($waPhone, 1);
                        }
                        $waLink = $waPhone ? "https://wa.me/{$waPhone}?text=" . urlencode("Halo, saya tertarik dengan produk *{$u->product}* dari *{$u->name}*. Apakah masih tersedia?") : null;
                        $detailLink = route('economy.produk.show', $u->id);
                    @endphp
                    <div class="group bg-white rounded-2xl md:rounded-[2rem] overflow-hidden border border-slate-100 hover:shadow-2xl hover:shadow-teal-900/10 transition-all duration-300 flex flex-col h-full relative">
                        {{-- Gambar & Info: klik ke halaman detail --}}
                        <a href="{{ $detailLink }}" class="block flex-1 flex flex-col">
                            <div class="aspect-square relative overflow-hidden bg-slate-100">
                                <img src="{{ $u->image_path ? asset('storage/' . $u->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}"
                                    alt="{{ $u->product }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                @if($u->is_featured)
                                <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                                    <span class="bg-amber-400 text-amber-950 text-[9px] font-black px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1 uppercase">
                                        <i class="fas fa-bolt"></i> LARIS
                                    </span>
                                    @php $opStatus = $u->operational_status; @endphp
                                    <span class="bg-white/90 backdrop-blur-sm text-{{ $opStatus['color'] }}-600 text-[9px] font-black px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1 uppercase border border-{{ $opStatus['color'] }}-100">
                                        <i class="fas {{ $opStatus['icon'] }}"></i> {{ $opStatus['label'] }}
                                    </span>
                                </div>
                                @else
                                <div class="absolute top-4 left-4 z-10">
                                    @php $opStatus = $u->operational_status; @endphp
                                    <span class="bg-white/90 backdrop-blur-sm text-{{ $opStatus['color'] }}-600 text-[9px] font-black px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1 uppercase border border-{{ $opStatus['color'] }}-100">
                                        <i class="fas {{ $opStatus['icon'] }}"></i> {{ $opStatus['label'] }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            <div class="p-5 pb-3 flex flex-col flex-1">
                                <p class="text-[9px] font-bold text-teal-600 uppercase tracking-widest mb-1 truncate">
                                    {{ $u->name }}
                                </p>
                                <h3 class="font-extrabold text-slate-800 text-base leading-tight group-hover:text-teal-700 transition-colors line-clamp-2 flex items-center gap-1.5">
                                    {{ $u->product }}
                                    @if($u->is_verified)
                                        <i class="fas fa-check-circle text-blue-500 text-xs shadow-sm"></i>
                                    @endif
                                </h3>
                                @if($u->price)
                                    <p class="text-base font-black text-slate-900 mt-2">Rp {{ number_format($u->price, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-xs font-bold text-slate-400 italic mt-2">Hubungi Penjual</p>
                                @endif
                            </div>
                        </a>

                        {{-- Footer: Tombol Lihat Detail + WA --}}
                        <div class="px-5 pb-5 pt-2 border-t border-slate-50 flex items-center gap-2 mt-auto">
                            <a href="{{ $detailLink }}"
                                class="flex-1 text-center text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-600 hover:text-white py-2 rounded-xl transition-colors">
                                Lihat Detail
                            </a>
                            @if($waLink)
                            <a href="{{ $waLink }}" target="_blank" title="Chat WhatsApp Penjual"
                                class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-colors flex-shrink-0">
                                <i class="fab fa-whatsapp text-base"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <i class="fas fa-store-slash text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700">Belum Ada Produk</h3>
                        <p class="text-slate-500 text-sm">Data katalog produk belum tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-12 text-center">
                <button @click="activeTab = 'produk'; window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="btn bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl px-8 font-bold shadow-sm transition-all hover:shadow-md">
                    <i class="fas fa-th-large mr-2"></i> Lihat Semua Produk
                </button>
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('q') && urlParams.get('q').length > 0) {
            setTimeout(() => {
                const tabsElement = document.getElementById('economy-tabs');
                if (tabsElement) {
                    tabsElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 500);
        }
    });
</script>
