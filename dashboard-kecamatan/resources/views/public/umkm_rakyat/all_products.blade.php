@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-box-open"></i>
                    <span>Katalog Produk Unggulan</span>
                </div>
                <h1 class="text-4xl font-black text-slate-800 mb-4">Semua Produk UMKM</h1>
                <p class="text-slate-500 font-medium max-w-2xl mx-auto">Cari dan temukan berbagai produk kreatif buatan
                    warga desa kami. Dukung ekonomi lokal dengan belanja langsung.</p>
            </div>

            <div class="bg-white rounded-[2rem] p-4 shadow-xl shadow-slate-200/50 mb-12 flex items-center">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <form action="{{ route('umkm_rakyat.products') }}" method="GET">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..."
                            class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($products as $product)
                    <div
                        class="bg-white rounded-[2.5rem] p-4 shadow-lg shadow-slate-200/50 border border-slate-100 group transition-all duration-300 transform hover:-translate-y-2 hover:shadow-xl hover:border-indigo-200">
                        <div class="relative aspect-square overflow-hidden rounded-[2rem] mb-4">
                            @if($product->foto_produk)
                                <img src="{{ asset('storage/' . $product->foto_produk) }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                            @else
                                <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200 text-5xl">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            @endif
                            <div class="absolute bottom-3 right-3 animate-fade-in-up">
                                <span
                                    class="bg-indigo-600/90 backdrop-blur-md text-white font-black px-4 py-2 rounded-xl shadow-lg text-sm border border-white/20">
                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="absolute top-3 left-3">
                                <div
                                    class="bg-white/95 backdrop-blur-md text-slate-700 text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-sm border border-white/50 flex items-center gap-1.5">
                                    <i class="fas fa-store text-indigo-400"></i>
                                    {{ Str::limit($product->umkm->nama_usaha, 15) }}
                                </div>
                            </div>
                        </div>
                        <div class="px-2 pb-2">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></span>
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $product->umkm->desa }}</span>
                            </div>

                            <h3
                                class="text-lg font-black text-slate-800 mb-2 truncate group-hover:text-indigo-600 transition-colors">
                                {{ $product->nama_produk }}</h3>

                            <p class="text-xs font-medium text-slate-500 line-clamp-2 mb-4 leading-relaxed h-8">
                                {{ $product->deskripsi ?? 'Produk unggulan berkualitas dari ' . $product->umkm->nama_usaha }}
                            </p>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-50 gap-3">
                                <a href="{{ route('umkm_rakyat.show', $product->umkm->slug) }}"
                                    class="flex-1 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 text-[10px] font-black uppercase tracking-widest py-2.5 rounded-xl text-center transition-all group-hover:bg-indigo-50">
                                    Lihat Detail
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->umkm->no_wa) }}"
                                    target="_blank"
                                    class="w-10 h-10 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all shadow-sm hover:rotate-12 hover:scale-110">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div
                            class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 text-4xl animate-pulse">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 mb-2">Produk tidak ditemukan</h3>
                        <p class="text-slate-500 font-medium">Coba gunakan kata kunci pencarian lain atau kembali nanti.</p>
                        <a href="{{ route('umkm_rakyat.index') }}"
                            class="inline-flex items-center gap-2 mt-8 text-indigo-600 font-bold bg-indigo-50 px-6 py-3 rounded-xl hover:bg-indigo-100 transition-colors">
                            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-16">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection