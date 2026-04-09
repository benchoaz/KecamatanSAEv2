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

<div class="min-h-screen bg-slate-50">
    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-slate-100">
        <div class="container mx-auto px-4 sm:px-6 py-3">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                <a href="{{ route('landing') }}" class="hover:text-teal-600 transition-colors">Beranda</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('economy.index', ['tab' => 'produk']) }}" class="hover:text-teal-600 transition-colors">Produk UMKM</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600 truncate max-w-[180px]">{{ $produk->name }}</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 py-8">
        <div class="max-w-4xl mx-auto">

            {{-- Main Card --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

                    {{-- Gambar Produk --}}
                    <div class="aspect-square md:aspect-auto md:min-h-[380px] relative overflow-hidden bg-slate-100">
                        <img src="{{ $produk->image_path ? asset('storage/' . $produk->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}"
                            alt="{{ $produk->product }}"
                            class="w-full h-full object-cover">
                        @if($produk->is_featured)
                            <div class="absolute top-4 left-4">
                                <span class="bg-amber-400 text-amber-950 text-[10px] font-black px-3 py-1.5 rounded-xl shadow-sm flex items-center gap-1.5">
                                    <i class="fas fa-star text-[9px]"></i> PILIHAN EDITOR
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Info Produk --}}
                    <div class="p-6 md:p-8 flex flex-col">
                        {{-- Nama Toko --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-teal-600 text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-teal-600 uppercase tracking-widest">{{ $produk->name }}</span>
                        </div>

                        {{-- Nama Produk --}}
                        <h1 class="text-2xl md:text-3xl font-black text-slate-800 leading-tight mb-4">
                            {{ $produk->product }}
                        </h1>

                        {{-- Harga --}}
                        <div class="mb-6">
                            @if($produk->price)
                                @if($produk->original_price && $produk->original_price > $produk->price)
                                    <div class="text-sm text-slate-400 line-through mb-1">
                                        Rp {{ number_format($produk->original_price, 0, ',', '.') }}
                                    </div>
                                @endif
                                <div class="text-3xl font-black text-teal-600">
                                    Rp {{ number_format($produk->price, 0, ',', '.') }}
                                </div>
                            @else
                                <div class="text-lg font-bold text-slate-400 italic">Hubungi Penjual untuk Harga</div>
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        @if($produk->description)
                        <p class="text-sm text-slate-500 leading-relaxed mb-6">{{ $produk->description }}</p>
                        @endif

                        {{-- Alamat --}}
                        @if($produk->address)
                        <div class="flex items-start gap-2 text-sm text-slate-500 mb-6">
                            <i class="fas fa-map-marker-alt text-teal-500 mt-0.5 w-4 text-center flex-shrink-0"></i>
                            <span>{{ $produk->address }}</span>
                        </div>
                        @endif

                        {{-- Tombol Aksi --}}
                        <div class="mt-auto space-y-3">
                            @if($waLink)
                            <a href="{{ $waLink }}" target="_blank"
                                class="w-full flex items-center justify-center gap-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-emerald-100 transition-all hover:shadow-emerald-200 hover:-translate-y-0.5 active:translate-y-0">
                                <i class="fab fa-whatsapp text-xl"></i>
                                Chat Penjual via WhatsApp
                            </a>
                            @endif
                            <a href="{{ route('economy.index', ['tab' => 'produk']) }}"
                                class="w-full flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-3 px-6 rounded-2xl transition-colors text-sm">
                                <i class="fas fa-arrow-left text-xs"></i> Lihat Produk Lainnya
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Produk Lain dari Toko yang Sama --}}
            @if($produkLainnya->count() > 0)
            <div class="mb-8">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-store text-teal-600"></i>
                    Produk Lain dari <span class="text-teal-600">{{ $produk->name }}</span>
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($produkLainnya as $p)
                    @php
                        $pWaPhone = preg_replace('/[^0-9]/', '', $p->contact_wa ?? '');
                        if (str_starts_with($pWaPhone, '0')) { $pWaPhone = '62' . substr($pWaPhone, 1); }
                        $pWaLink = $pWaPhone ? "https://wa.me/{$pWaPhone}?text=" . urlencode("Halo, saya tertarik dengan produk *{$p->product}* dari *{$p->name}*. Apakah masih tersedia?") : null;
                    @endphp
                    <a href="{{ route('economy.produk.show', $p->id) }}"
                        class="bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg transition-all group">
                        <div class="aspect-square overflow-hidden bg-slate-50">
                            <img src="{{ $p->image_path ? asset('storage/' . $p->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&auto=format&fit=crop&q=60' }}"
                                alt="{{ $p->product }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-3">
                            <p class="text-xs font-bold text-slate-700 line-clamp-2 mb-1">{{ $p->product }}</p>
                            @if($p->price)
                                <p class="text-xs font-black text-teal-600">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                            @else
                                <p class="text-[10px] text-slate-400 italic">Hubungi Penjual</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
