@extends('layouts.public')

@section('title', $umkm->product . ' - ' . $umkm->name)

@section('content')
    <section class="py-20 bg-white min-h-screen">
        <div class="container max-w-7xl mx-auto px-6">
            <div class="mb-10">
                <a href="{{ route('economy.index', ['tab' => 'produk']) }}"
                    class="inline-flex items-center gap-2 text-slate-500 hover:text-teal-600 font-bold transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali ke Etalase
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Image Gallery -->
                <div class="space-y-6">
                    <div
                        class="aspect-square rounded-[48px] overflow-hidden bg-slate-100 border border-slate-100 shadow-premium">
                        <img src="{{ $umkm->image_path ? asset('storage/' . $umkm->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}"
                            class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Product Details -->
                <div class="flex flex-col h-full">
                    <div class="mb-8">
                        <span
                            class="inline-block px-4 py-1.5 bg-teal-50 text-teal-700 text-[10px] font-extrabold tracking-widest uppercase rounded-full border border-teal-100 mb-6">
                            Produk UMKM Lokal
                        </span>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 leading-tight">{{ $umkm->product }}
                        </h1>
                        <div class="flex items-center gap-3 text-lg font-bold text-slate-500">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-teal-600">
                                <i class="fas fa-store"></i>
                            </div>
                            <span class="flex items-center gap-1.5">
                                {{ $umkm->name }}
                                @if($umkm->is_verified)
                                    <i class="fas fa-check-circle text-blue-500 text-sm" title="Terverifikasi Resmi"></i>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="mb-10 p-8 bg-slate-50 rounded-[40px] border border-slate-100">
                        <div class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-2">Harga Produk</div>
                        @if($umkm->price)
                            <div class="flex items-end gap-4">
                                <div class="text-4xl font-black text-slate-900">Rp
                                    {{ number_format($umkm->price, 0, ',', '.') }}</div>
                                @if($umkm->original_price)
                                    <div class="text-xl text-slate-400 line-through mb-1">Rp
                                        {{ number_format($umkm->original_price, 0, ',', '.') }}</div>
                                @endif
                            </div>
                        @else
                            <div class="text-2xl font-bold text-slate-400 italic">Hubungi via WhatsApp</div>
                        @endif
                    </div>

                    <div class="mb-12">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-4">Deskripsi Produk</h3>
                        <div class="text-slate-600 leading-relaxed text-lg prose">
                            @if($umkm->description)
                                {!! nl2br(e($umkm->description)) !!}
                            @else
                                <p class="italic">Penjual belum menambahkan deskripsi untuk produk ini.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ $waUrl }}" target="_blank"
                                class="btn h-20 bg-emerald-600 hover:bg-emerald-700 border-none text-white rounded-3xl flex items-center justify-center gap-4 text-lg font-bold shadow-xl shadow-emerald-500/20 transition-all stagger-in">
                                <i class="fab fa-whatsapp text-3xl"></i>
                                <div class="text-left leading-tight">
                                    <div class="text-[10px] opacity-80 uppercase tracking-widest">Tanyak / Beli</div>
                                    <div class="text-lg">Chat WhatsApp</div>
                                </div>
                            </a>

                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center gap-4">
                                <i class="fas fa-shield-alt text-teal-600 text-2xl opacity-40"></i>
                                <p class="text-[10px] text-slate-500 leading-tight">Transaksi dilakukan langsung dengan
                                    penjual. Tetap waspada & teliti.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Extra Info -->
    <section class="py-20 bg-slate-50 border-t border-slate-100">
        <div class="container max-w-7xl mx-auto px-6">
            <div class="max-w-3xl">
                <h4 class="text-xl font-bold text-slate-900 mb-6">Informasi Tambahan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-shipping-fast text-teal-600"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-800 text-sm mb-1 uppercase">Pengiriman</h5>
                            <p class="text-xs text-slate-500">Tanyakan kepada penjual apakah mendukung jasa kirim atau COD
                                di wilayah {{ appProfile()->region_name }}.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-info-circle text-teal-600"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-800 text-sm mb-1 uppercase">Lokasi UMKM</h5>
                            <p class="text-xs text-slate-500">Merupakan UMKM yang terverifikasi berada di wilayah {{ appProfile()->region_level }}
                                {{ appProfile()->region_name }}.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection