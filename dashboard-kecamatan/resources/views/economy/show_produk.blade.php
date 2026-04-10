@extends('layouts.public')

@section('title', $produk->name . ' – ' . $produk->product . ' | ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('meta')
<meta name="description" content="{{ $produk->name }} menjual {{ $produk->product }}. Produk UMKM lokal dari {{ appProfile()->region_level }} {{ appProfile()->region_name }}.">
@endsection

@section('content')
@php
    $waPhone = preg_replace('/[^0-9]/', '', $produk->contact_wa ?? '');
    if (str_starts_with($waPhone, '0')) { $waPhone = '62' . substr($waPhone, 1); }
    $waLink = $waPhone ? "https://wa.me/{$waPhone}?text=" . urlencode("Halo, saya tertarik dengan produk *{$produk->product}* dari *{$produk->name}*. Apakah masih tersedia?") : null;
@endphp

<div class="min-h-screen bg-white">
    {{-- Breadcrumb - Cleaner --}}
    <div class="bg-white border-b border-slate-100 hidden md:block">
        <div class="container mx-auto px-6 py-4">
            <nav class="flex items-center gap-2 text-[13px] text-slate-500 font-medium">
                <a href="{{ route('landing') }}" class="hover:text-teal-600 transition-colors">Beranda</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('economy.index', ['tab' => 'produk']) }}" class="hover:text-teal-600 transition-colors">Produk UMKM</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-400 truncate max-w-[200px]">{{ $produk->name }}</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-4 md:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            {{-- COLUMN 1: Visuals (Sticky on Desktop) --}}
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-4">
                    {{-- Main Image Area --}}
                    <div class="aspect-square bg-slate-50 rounded-2xl md:rounded-3xl border border-slate-100 overflow-hidden group relative">
                        <img src="{{ $produk->image_path ? asset('storage/' . $produk->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}"
                            alt="{{ $produk->product }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        
                        @if($produk->is_featured)
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur-md text-amber-600 text-[10px] font-black px-3 py-1.5 rounded-xl shadow-lg border border-amber-100 flex items-center gap-1.5">
                                    <i class="fas fa-bolt"></i> PILIHAN
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Small Grid Gallery Mockup --}}
                    <div class="grid grid-cols-5 gap-3">
                        <div class="aspect-square rounded-lg border-2 border-teal-600 overflow-hidden p-0.5">
                            <img src="{{ $produk->image_path ? asset('storage/' . $produk->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}" class="w-full h-full object-cover rounded-md">
                        </div>
                        @for ($i = 0; $i < 4; $i++)
                        <div class="aspect-square rounded-lg border border-slate-100 bg-slate-50 opacity-40"></div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- COLUMN 2: Info & Details --}}
            <div class="lg:col-span-5 space-y-8">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 leading-tight mb-2">
                        {{ $produk->product }}
                    </h1>
                    <div class="flex items-center gap-4 text-sm mb-6">
                        <div class="flex items-center gap-1 text-amber-400">
                            <i class="fas fa-star text-xs"></i>
                            <i class="fas fa-star text-xs"></i>
                            <i class="fas fa-star text-xs"></i>
                            <i class="fas fa-star text-xs"></i>
                            <i class="fas fa-star text-xs"></i>
                        </div>
                        <span class="text-slate-400 text-xs font-medium border-l border-slate-200 pl-4">Terjual 10+</span>
                    </div>

                    <div class="text-3xl font-black text-slate-900 mb-8 border-b border-slate-50 pb-6 flex items-baseline gap-2">
                        @if($produk->price)
                            <span>Rp{{ number_format($produk->price, 0, ',', '.') }}</span>
                        @else
                            <span class="text-lg text-slate-400 italic">Hubungi Penjual</span>
                        @endif
                    </div>
                </div>

                {{-- Tabs-like sections --}}
                <div x-data="{ activeTab: 'detail' }" class="space-y-6">
                    <div class="flex border-b border-slate-100 overflow-x-auto no-scrollbar">
                        <button @click="activeTab = 'detail'" :class="activeTab === 'detail' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-400'" class="pb-3 px-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Detail Produk</button>
                        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-400'" class="pb-3 px-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Informasi Penting</button>
                    </div>

                    <div class="py-2" x-show="activeTab === 'detail'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                <span class="text-teal-600">Kondisi:</span> Baru
                                <span class="mx-3 text-slate-200">|</span>
                                <span class="text-teal-600">Min. Pemesanan:</span> 1
                            </div>
                            <div class="text-slate-600 text-sm leading-[1.8] font-medium whitespace-pre-line">
                                {{ $produk->description ?? 'Tidak ada deskripsi untuk produk ini.' }}
                            </div>
                        </div>
                    </div>

                    <div class="py-2" x-show="activeTab === 'info'" style="display: none;">
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                            <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Catatan Toko:
                            </h4>
                            <p class="text-xs text-blue-700 leading-relaxed font-medium">
                                Produk ini merupakan hasil karya lokal warga {{ appProfile()->region_name }}. Pastikan ketersediaan barang dengan menghubungi penjual melalui WhatsApp sebelum memesan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Store Info Section --}}
                <div class="pt-8 border-t border-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center border border-slate-200 overflow-hidden">
                                @if($produk->image_path)
                                    <img src="{{ asset('storage/' . $produk->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-store text-2xl text-slate-400"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 flex items-center gap-1.5">
                                    {{ $produk->name }}
                                    <i class="fas fa-check-circle text-teal-500 text-xs shadow-sm"></i>
                                </h4>
                                <p class="text-[11px] font-bold text-teal-600 uppercase tracking-widest mt-0.5">
                                    Online <span class="text-slate-300 mx-1">•</span> Desa {{ $produk->address ?? appProfile()->region_name }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('economy.index', ['tab' => 'produk']) }}" class="btn btn-sm bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl px-4 font-bold normal-case">
                            Kunjungi Toko
                        </a>
                    </div>
                </div>

                {{-- Shipping Info Mockup --}}
                <div class="pt-8 border-t border-slate-50">
                    <h4 class="font-black text-slate-800 mb-4 text-sm">Pengiriman</h4>
                    <div class="flex items-start gap-3 text-sm text-slate-500">
                        <i class="fas fa-truck-moving text-slate-400 mt-1"></i>
                        <div>
                            <p class="font-bold text-slate-600">Dikirim dari {{ appProfile()->region_name }}</p>
                            <p class="text-[11px] mt-0.5">Produk lokal, pengiriman cepat & aman via Kurir Lokal atau Pickup.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMN 3: Buy Box (Sticky Card) --}}
            <div class="lg:col-span-3">
                <div class="sticky top-24">
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 p-6 space-y-6">
                        <h4 class="font-black text-slate-800 text-sm">Atur jumlah dan catatan</h4>
                        
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 border border-slate-200 rounded-xl p-1 bg-slate-50">
                                <button class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-teal-600"><i class="fas fa-minus text-xs"></i></button>
                                <span class="w-10 text-center font-black text-slate-800">1</span>
                                <button class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-teal-600"><i class="fas fa-plus text-xs"></i></button>
                            </div>
                            <p class="text-xs font-medium text-slate-400">Tersedia <span class="text-slate-600 font-bold">10+</span></p>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <span class="text-slate-500 font-medium">Subtotal</span>
                            <span class="text-lg font-black text-slate-800">
                                @if($produk->price)
                                    Rp{{ number_format($produk->price, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        <div class="space-y-3 pt-2">
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank"
                                    class="w-full flex items-center justify-center gap-2 bg-teal-800 hover:bg-teal-900 text-white font-black py-4 rounded-2xl shadow-lg shadow-teal-900/10 transition-all hover:-translate-y-1">
                                    <i class="fas fa-shopping-cart text-sm"></i> Beli Langsung
                                </a>
                                <a href="{{ $waLink }}" target="_blank"
                                    class="w-full flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-teal-800 border-2 border-teal-800 font-black py-4 rounded-2xl transition-all">
                                    <i class="fab fa-whatsapp text-lg"></i> Chat Penjual
                                </a>
                            @endif
                        </div>

                        {{-- Footer Action Symbols --}}
                        <div class="flex items-center justify-around text-xs font-bold text-slate-400 pt-4 border-t border-slate-50">
                            <button class="flex items-center gap-1.5 hover:text-teal-600"><i class="fas fa-share-alt"></i> Bagikan</button>
                            <span class="text-slate-100">|</span>
                            <button class="flex items-center gap-1.5 hover:text-red-500"><i class="fas fa-heart"></i> Wislist</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Mobile CTA Bar (Fixed Bottom) --}}
        <div class="md:hidden fixed bottom-16 left-0 w-full bg-white border-t border-slate-100 p-4 z-40 flex items-center gap-3 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
            <div class="flex-grow">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total Harga</p>
                <p class="text-base font-black text-slate-900">
                    @if($produk->price)
                        Rp{{ number_format($produk->price, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </p>
            </div>
            @if($waLink)
                <a href="{{ $waLink }}" target="_blank" class="bg-teal-800 text-white px-6 py-3 rounded-xl font-black text-sm flex items-center gap-2">
                    Beli <i class="fas fa-arrow-right"></i>
                </a>
            @endif
        </div>

        {{-- Spacer for Mobile Bar --}}
        <div class="h-24 md:hidden"></div>

        {{-- Produk Lain dari Toko --}}
        @if($produkLainnya->count() > 0)
        <div class="mt-16 pt-12 border-t border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-black text-slate-800">Lainnya di Toko Ini</h2>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Produk Serupa dari {{ $produk->name }}</p>
                </div>
                <a href="{{ route('economy.index', ['tab' => 'produk']) }}" class="text-teal-600 font-black text-sm hover:underline">Lihat Semua</a>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($produkLainnya as $p)
                <a href="{{ route('economy.produk.show', $p->id) }}"
                    class="bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-teal-900/5 transition-all group flex flex-col h-full">
                    <div class="aspect-square overflow-hidden bg-slate-50">
                        <img src="{{ $p->image_path ? asset('storage/' . $p->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&auto=format&fit=crop&q=60' }}"
                            alt="{{ $p->product }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest mb-1">{{ $p->name }}</p>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-teal-700 transition-colors line-clamp-2 mb-3">
                            {{ $p->product }}
                        </h3>
                        <div class="mt-auto">
                            @if($p->price)
                                <p class="text-sm font-black text-slate-900">Rp{{ number_format($p->price, 0, ',', '.') }}</p>
                            @else
                                <p class="text-xs text-slate-400 italic">Hubungi Penjual</p>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

