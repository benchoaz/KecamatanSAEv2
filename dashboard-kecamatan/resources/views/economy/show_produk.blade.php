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
    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-slate-100 hidden md:block">
        <div class="container mx-auto px-6 py-4">
            <nav class="flex items-center gap-2 text-[13px] text-slate-500 font-medium">
                <a href="{{ route('landing') }}" class="hover:text-teal-600 transition-colors">Beranda</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('economy.index', ['tab' => 'produk']) }}" class="hover:text-teal-600 transition-colors">Produk UMKM</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('economy.index', ['q' => $produk->name, 'tab' => 'produk']) }}" class="hover:text-teal-600 transition-colors truncate max-w-[200px]">{{ $produk->name }}</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-700 font-bold truncate max-w-[200px]">{{ $produk->product }}</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-4 md:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

            {{-- COLUMN 1: Visuals --}}
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-4">
                    {{-- Main Image --}}
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

                        @php $opStatus = $produk->operational_status; @endphp
                        <div class="absolute bottom-4 right-4">
                            <span class="bg-white/90 backdrop-blur-md text-{{ $opStatus['color'] }}-600 text-[10px] font-black px-3 py-1.5 rounded-xl shadow-lg border border-{{ $opStatus['color'] }}-100 flex items-center gap-1.5">
                                <i class="fas {{ $opStatus['icon'] }}"></i> {{ $opStatus['label'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Other products from same shop --}}
                    @if($produkLainnya->count() > 0)
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                            {{ $produkLainnya->count() }} Produk Lain dari {{ $produk->name }}
                        </p>
                        <div class="grid grid-cols-4 gap-2">
                            <div class="aspect-square rounded-xl border-2 border-teal-500 overflow-hidden ring-2 ring-teal-200">
                                <img src="{{ $produk->image_path ? asset('storage/' . $produk->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200' }}" class="w-full h-full object-cover">
                            </div>
                            @foreach($produkLainnya->take(3) as $p)
                            <a href="{{ route('economy.produk.show', $p->id) }}" class="aspect-square rounded-xl border border-slate-200 overflow-hidden hover:border-teal-400 transition-all" title="{{ $p->product }}">
                                <img src="{{ $p->image_path ? asset('storage/' . $p->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200' }}"
                                     alt="{{ $p->product }}" class="w-full h-full object-cover">
                            </a>
                            @endforeach
                        </div>
                        @if($produkLainnya->count() > 3)
                        <a href="{{ route('economy.index', ['q' => $produk->name, 'tab' => 'produk']) }}"
                           class="mt-3 flex items-center justify-center gap-1 text-[11px] font-bold text-teal-600 hover:text-teal-800 transition-colors">
                            Lihat {{ $produkLainnya->count() - 3 }} produk lainnya <i class="fas fa-chevron-right text-[9px]"></i>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- COLUMN 2: Info & Details --}}
            <div class="lg:col-span-5 space-y-8">
                <div>
                    {{-- Shop Name Link --}}
                    <a href="{{ route('economy.index', ['q' => $produk->name, 'tab' => 'produk']) }}"
                       class="inline-flex items-center gap-2 mb-3 group">
                        <div class="w-6 h-6 bg-teal-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-[10px] text-teal-600"></i>
                        </div>
                        <span class="text-xs font-black text-teal-700 uppercase tracking-widest group-hover:text-teal-900">{{ $produk->name }}</span>
                        @if($produkLainnya->count() > 0)
                        <span class="text-[10px] bg-teal-50 text-teal-600 border border-teal-100 px-1.5 py-0.5 rounded font-bold">
                            +{{ $produkLainnya->count() }} produk
                        </span>
                        @endif
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-teal-600"></i>
                    </a>

                    <h1 class="text-xl md:text-2xl font-black text-slate-800 leading-tight mb-6">
                        {{ $produk->product }}
                    </h1>

                    <div class="text-3xl font-black text-slate-900 mb-8 border-b border-slate-50 pb-6">
                        @if($produk->price)
                            <span>Rp{{ number_format($produk->price, 0, ',', '.') }}</span>
                        @else
                            <span class="text-lg text-slate-400 italic font-medium">Hubungi Penjual untuk Harga</span>
                        @endif
                    </div>
                </div>

                {{-- Detail Tabs --}}
                <div x-data="{ activeTab: 'detail' }" class="space-y-6">
                    <div class="flex border-b border-slate-100 overflow-x-auto">
                        <button @click="activeTab = 'detail'" :class="activeTab === 'detail' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-400'" class="pb-3 px-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Detail Produk</button>
                        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-400'" class="pb-3 px-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Info Penting</button>
                    </div>

                    <div x-show="activeTab === 'detail'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" class="py-2">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                <div><span class="text-teal-600">Kondisi:</span> Baru</div>
                                <div><span class="text-teal-600">Min. Pesan:</span> 1</div>
                                <div><span class="text-teal-600">Asal:</span> {{ appProfile()->region_name }}</div>
                            </div>
                            <div class="text-slate-600 text-sm leading-[1.8] font-medium whitespace-pre-line">
                                {{ $produk->description ?? 'Tidak ada deskripsi. Silakan hubungi penjual untuk keterangan lebih lanjut.' }}
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'info'" style="display: none;" class="py-2">
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                            <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Catatan Penting:
                            </h4>
                            <p class="text-xs text-blue-700 leading-relaxed font-medium">
                                Produk ini merupakan hasil karya lokal warga {{ appProfile()->region_name }}. Pastikan ketersediaan barang dengan menghubungi penjual melalui WhatsApp sebelum memesan. Platform ini hanya memfasilitasi informasi, bukan bertindak sebagai makelar.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Store Info --}}
                <div class="pt-8 border-t border-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center border border-teal-100 overflow-hidden">
                                @if($produk->image_path)
                                    <img src="{{ asset('storage/' . $produk->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-store text-2xl text-teal-400"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 flex items-center gap-1.5">
                                    {{ $produk->name }}
                                    @if($produk->is_verified)
                                        <i class="fas fa-check-circle text-blue-500 text-xs shadow-sm" title="Terverifikasi Resmi oleh Kecamatan"></i>
                                    @else
                                        <i class="fas fa-check-circle text-teal-500 text-xs opacity-50" title="Warga Terdaftar"></i>
                                    @endif
                                </h4>
                                <p class="text-[11px] font-bold text-slate-400 mt-0.5">
                                    {{ $produk->address ?? appProfile()->region_name }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('economy.index', ['q' => $produk->name, 'tab' => 'produk']) }}"
                           class="btn btn-sm bg-white hover:bg-teal-50 text-teal-700 border border-teal-200 rounded-xl px-4 font-bold normal-case text-xs">
                            <i class="fas fa-store mr-1"></i> Lihat Toko
                        </a>
                    </div>
                </div>

                {{-- Shipping --}}
                <div class="pt-8 border-t border-slate-50">
                    <h4 class="font-black text-slate-800 mb-4 text-sm">Informasi Pengiriman</h4>
                    <div class="flex items-start gap-3 text-sm text-slate-500">
                        <i class="fas fa-truck-moving text-teal-400 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-slate-600">Dikirim dari {{ appProfile()->region_name }}</p>
                            <p class="text-[11px] mt-0.5">Pengiriman via Kurir Lokal atau Ambil Sendiri. Konfirmasi ke penjual untuk detail ongkos kirim.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMN 3: Contact Card --}}
            <div class="lg:col-span-3">
                <div class="sticky top-24">
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 p-6 space-y-5">

                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-2xl p-4">
                            <i class="fas fa-info-circle text-amber-500 mt-0.5 shrink-0"></i>
                            <p class="text-xs text-amber-700 font-medium leading-relaxed">
                                Transaksi dilakukan <strong>langsung dengan penjual</strong> via WhatsApp atau tatap muka.
                            </p>
                        </div>

                        @if($produk->price)
                        <div class="flex items-center justify-between py-3 border-b border-slate-50">
                            <span class="text-slate-500 font-medium text-sm">Harga Referensi</span>
                            <span class="text-lg font-black text-slate-800">Rp{{ number_format($produk->price, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="space-y-3">
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank"
                                    class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5a] text-white font-black py-4 rounded-2xl shadow-lg shadow-green-500/20 transition-all hover:-translate-y-0.5 active:scale-95">
                                    <i class="fab fa-whatsapp text-xl"></i> Hubungi via WhatsApp
                                </a>
                                @if($waPhone)
                                <a href="tel:+{{ $waPhone }}"
                                    class="w-full flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-black py-3.5 rounded-2xl transition-all text-sm">
                                    <i class="fas fa-phone text-slate-400"></i> Telepon Penjual
                                </a>
                                @endif
                            @else
                                <p class="text-center text-slate-400 text-sm font-medium py-4">Kontak tidak tersedia.</p>
                            @endif
                        </div>

                        <p class="text-center text-[10px] text-slate-400 font-medium">
                            Tanyakan stok, harga, dan detail produk langsung ke penjual.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Mobile CTA Bar --}}
        <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-slate-100 p-4 z-40 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
            @if($waLink)
                <a href="{{ $waLink }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#25D366] text-white px-6 py-4 rounded-2xl font-black text-base">
                    <i class="fab fa-whatsapp text-xl"></i> Hubungi Penjual via WhatsApp
                </a>
            @endif
        </div>

        <div class="h-24 md:hidden"></div>

        {{-- More products from same shop --}}
        @if($produkLainnya->count() > 0)
        <div class="mt-16 pt-12 border-t border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-black text-slate-800">Produk Lainnya dari Toko Ini</h2>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $produkLainnya->count() }} produk • {{ $produk->name }}</p>
                </div>
                <a href="{{ route('economy.index', ['q' => $produk->name, 'tab' => 'produk']) }}" class="text-teal-600 font-black text-sm hover:underline">Lihat Toko <i class="fas fa-arrow-right ml-1"></i></a>
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
