@extends('layouts.public')

@section('title', 'Etalase UMKM Lokal - ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('content')
    <section class="py-20 bg-slate-50 min-h-screen">
        <div class="container max-w-7xl mx-auto px-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-16">
                <div class="max-w-2xl">
                    <nav class="flex mb-4 text-xs font-bold uppercase tracking-widest text-teal-600">
                        <a href="/" class="hover:text-teal-700">Beranda</a>
                        <span class="mx-2 text-slate-300">/</span>
                        <span class="text-slate-400">Etalase UMKM</span>
                    </nav>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">Etalase UMKM Lokal</h1>
                    <p class="text-slate-500 text-lg">Dukungan ekonomi kreatif warga {{ appProfile()->region_level }} {{ appProfile()->region_name }}. Produk berkualitas,
                        harga terjangkau, langsung dari produsen.</p>
                </div>

                <!-- Search Bar -->
                <div class="w-full md:w-96">
                    <form action="{{ route('public.umkm.index') }}" method="GET" class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="w-full h-14 bg-white border border-slate-200 rounded-2xl px-6 pr-14 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/5 transition-all outline-none font-medium shadow-sm"
                            placeholder="Cari produk atau toko...">
                        <button type="submit"
                            class="absolute right-2 top-2 h-10 w-10 bg-teal-600 text-white rounded-xl flex items-center justify-center hover:bg-teal-700 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            @if(request('q'))
                <div class="mb-10 text-slate-500 font-medium">
                    Menampilkan hasil pencarian untuk: <span class="text-teal-600 font-bold">"{{ request('q') }}"</span>
                    <a href="{{ route('public.umkm.index') }}"
                        class="ml-2 text-xs text-slate-400 underline hover:text-slate-600">Hapus</a>
                </div>
            @endif

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($umkms as $u)
                    <div
                        class="group bg-white rounded-[32px] overflow-hidden border border-slate-100 hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500 flex flex-col h-full relative">
                        @if($u->is_featured)
                            <div class="absolute top-4 left-4 z-10">
                                <span
                                    class="bg-amber-400 text-amber-950 text-[10px] font-extrabold px-3 py-1 rounded-full shadow-lg flex items-center gap-1">
                                    <i class="fas fa-star"></i> PILIHAN TERBAIK
                                </span>
                            </div>
                        @endif

                        <!-- Product Image -->
                        <div class="aspect-square relative overflow-hidden shrink-0">
                            <img src="{{ $u->image_path ? asset('storage/' . $u->image_path) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=60' }}"
                                alt="{{ $u->product }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>

                        <!-- Product Content -->
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest mb-1">{{ $u->name }}
                                    </p>
                                    <h3
                                        class="font-extrabold text-slate-900 group-hover:text-teal-600 transition-colors line-clamp-1">
                                        {{ $u->product }}</h3>
                                </div>
                            </div>

                            <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                                <div>
                                    @if($u->price)
                                        <div class="text-lg font-black text-slate-900">Rp
                                            {{ number_format($u->price, 0, ',', '.') }}</div>
                                        @if($u->original_price)
                                            <div class="text-[10px] text-slate-400 line-through">Rp
                                                {{ number_format($u->original_price, 0, ',', '.') }}</div>
                                        @endif
                                    @else
                                        <div class="text-xs font-bold text-slate-400 italic">Hubungi untuk Harga</div>
                                    @endif
                                </div>

                                <a href="{{ route('public.umkm.show', $u->id) }}"
                                    class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-teal-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 text-center bg-white rounded-[50px] border border-dashed border-slate-200">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
                            <i class="fas fa-store-slash text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-slate-400">Maaf, kami tidak menemukan produk UMKM yang Anda cari.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-16">
                {{ $umkms->links() }}
            </div>
        </div>
    </section>
@endsection