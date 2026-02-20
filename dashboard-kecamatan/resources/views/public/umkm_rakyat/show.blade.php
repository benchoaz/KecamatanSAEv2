@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 relative overflow-hidden font-sans">

        <!-- Background Decorations -->
        <div
            class="fixed top-0 left-0 w-[600px] h-[600px] bg-teal-200/20 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        </div>
        <div
            class="fixed bottom-0 right-0 w-[600px] h-[600px] bg-indigo-200/20 rounded-full blur-[120px] translate-x-1/2 translate-y-1/2 pointer-events-none">
        </div>

        <!-- Cover & Profile Header -->
        <div class="relative h-[28rem] md:h-[32rem] bg-slate-900 overflow-hidden group/cover">
            <!-- Dynamic Cover Image or Fallback -->
            @if($umkm->foto_usaha)
                <img src="{{ asset('storage/' . $umkm->foto_usaha) }}"
                    class="w-full h-full object-cover opacity-60 scale-105 blur-sm group-hover/cover:scale-110 transition-transform duration-[2s]"
                    alt="{{ $umkm->nama_usaha }}">
            @else
                <div class="w-full h-full bg-slate-800 pattern-grid-lg"></div>
            @endif

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/30 via-slate-900/60 to-slate-50"></div>

            <div class="container mx-auto px-6 h-full relative z-10 flex flex-col justify-end pb-12">
                <!-- Back Button -->
                <a href="{{ route('umkm_rakyat.index') }}"
                    class="absolute top-8 left-6 md:left-0 text-white/80 hover:text-white flex items-center gap-2 font-bold text-xs uppercase tracking-widest transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali ke Katalog
                </a>

                <div class="flex flex-col md:flex-row items-end gap-10">
                    <!-- Store Profile Picture -->
                    <div class="relative group">
                        <div
                            class="w-36 h-36 md:w-52 md:h-52 rounded-[2.5rem] bg-white p-2 shadow-2xl shadow-slate-900/20 overflow-hidden rotate-0 group-hover:rotate-2 transition-transform duration-500 ease-out shrink-0">
                            <div
                                class="w-full h-full rounded-[2rem] overflow-hidden bg-slate-100 flex items-center justify-center relative">
                                @if($umkm->foto_usaha)
                                    <img src="{{ asset('storage/' . $umkm->foto_usaha) }}"
                                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                @else
                                    <i class="fas fa-store text-5xl text-slate-300"></i>
                                @endif
                                <div
                                    class="absolute inset-0 border-[6px] border-white/10 rounded-[2rem] pointer-events-none">
                                </div>
                            </div>
                        </div>
                        <div
                            class="absolute -bottom-4 -right-4 bg-emerald-500 text-white w-12 h-12 rounded-full flex items-center justify-center border-4 border-slate-50 shadow-lg animate-bounce">
                            <i class="fas fa-check text-lg"></i>
                        </div>
                    </div>

                    <!-- Store Info -->
                    <div class="flex-1 pb-4">
                        <div class="flex flex-wrap items-center gap-3 mb-4 animate-fade-in-up"
                            style="animation-delay: 100ms">
                            <span
                                class="bg-teal-500/20 backdrop-blur-md border border-teal-500/30 text-teal-100 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                {{ $umkm->jenis_usaha }}
                            </span>
                            <span
                                class="bg-white/10 backdrop-blur-md border border-white/20 text-white/90 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-rose-400"></i> {{ $umkm->desa }}
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-6xl font-black text-slate-800 mb-4 tracking-tight leading-none animate-fade-in-up md:text-slate-900"
                            style="animation-delay: 200ms; text-shadow: 0 2px 10px rgba(255,255,255,0.5);">
                            {{ $umkm->nama_usaha }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-6 text-slate-600 font-bold animate-fade-in-up"
                            style="animation-delay: 300ms">
                            <div
                                class="flex items-center gap-3 bg-white/60 backdrop-blur-md px-4 py-2 rounded-xl border border-white/40 shadow-sm">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[9px] uppercase tracking-widest opacity-60 leading-none mb-0.5">Pemilik</span>
                                    <span class="text-xs text-slate-800">{{ $umkm->nama_pemilik }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Actions -->
                    <div class="flex gap-4 mb-4 shrink-0 animate-fade-in-up w-full md:w-auto"
                        style="animation-delay: 400ms">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->no_wa) }}" target="_blank"
                            class="group relative overflow-hidden bg-[#25D366] text-white font-black px-8 py-5 rounded-[1.5rem] shadow-xl shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3 w-full md:w-auto">
                            <div
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:animate-shimmer">
                            </div>
                            <i class="fab fa-whatsapp text-2xl"></i>
                            <div class="flex flex-col items-start leading-none">
                                <span class="text-[9px] uppercase tracking-widest opacity-80 font-bold">Chat WhatsApp</span>
                                <span class="text-base">Hubungi Penjual</span>
                            </div>
                        </a>
                    </div>
                        
                    <!-- Marketplace Links (Secondary Actions) -->
                    @if($umkm->tokopedia_url || $umkm->shopee_url || $umkm->tiktok_url)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full animate-fade-in-up" style="animation-delay: 500ms">
                            @if($umkm->tokopedia_url)
                                <a href="{{ $umkm->tokopedia_url }}" target="_blank"
                                    class="flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 font-bold px-4 py-3 rounded-xl hover:border-[#42b549] hover:text-[#42b549] hover:bg-[#42b549]/5 transition-all shadow-sm">
                                    <img src="https://assets.tokopedia.net/assets-tokopedia-lite/v2/zeus/kratos/6046e723.png" class="w-5 h-5" alt="Tokopedia">
                                    <span class="text-xs">Buka di Tokopedia</span>
                                </a>
                            @endif
                            @if($umkm->shopee_url)
                                <a href="{{ $umkm->shopee_url }}" target="_blank"
                                    class="flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 font-bold px-4 py-3 rounded-xl hover:border-[#ee4d2d] hover:text-[#ee4d2d] hover:bg-[#ee4d2d]/5 transition-all shadow-sm">
                                    <img src="https://logospng.org/download/shopee/logo-shopee-icon-1024.png" class="w-5 h-5" alt="Shopee">
                                    <span class="text-xs">Buka di Shopee</span>
                                </a>
                            @endif
                            @if($umkm->tiktok_url)
                                <a href="{{ $umkm->tiktok_url }}" target="_blank"
                                    class="flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 font-bold px-4 py-3 rounded-xl hover:border-black hover:text-black hover:bg-black/5 transition-all shadow-sm">
                                    <i class="fab fa-tiktok text-lg"></i>
                                    <span class="text-xs">Buka di TikTok</span>
                                </a>
                            @endif
                        </div>
                        
                        <!-- Disclaimer Marketplace -->
                        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 flex gap-3 items-start animate-fade-in-up" style="animation-delay: 600ms">
                            <i class="fas fa-info-circle text-slate-400 text-xs mt-0.5"></i>
                            <p class="text-[10px] text-slate-500 leading-relaxed font-medium">
                                <strong>Disclaimer:</strong> Tautan marketplace di atas dikelola langsung oleh pemilik UMKM. 
                                Pemerintah Kecamatan tidak bertanggung jawab atas transaksi yang terjadi di platform pihak ketiga.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Body -->
        <div class="container mx-auto px-6 py-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                <!-- Left Sidebar -->
                <div class="lg:col-span-4 space-y-8 h-fit lg:sticky lg:top-24">
                    <!-- Description Card -->
                    <div
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group hover:border-indigo-100 transition-colors">
                        <div
                            class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-50 rounded-full blur-2xl group-hover:bg-indigo-100 transition-colors">
                        </div>

                        <div class="relative z-10">
                            <h3
                                class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <i class="fas fa-info-circle text-indigo-400"></i> Tentang Usaha
                            </h3>
                            <p class="text-slate-600 font-medium leading-relaxed mb-8 text-sm">
                                {{ $umkm->deskripsi ?? 'Pemilik usaha belum menambahkan deskripsi lengkap. Dukung terus produk lokal!' }}
                            </p>

                            <div class="space-y-5">
                                <div class="flex items-center gap-4 group/item">
                                    <div
                                        class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover/item:bg-indigo-50 group-hover/item:text-indigo-500 transition-colors border border-slate-100">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Bergabung</h4>
                                        <p class="text-sm font-black text-slate-800">
                                            {{ $umkm->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 group/item">
                                    <div
                                        class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover/item:bg-teal-50 group-hover/item:text-teal-500 transition-colors border border-slate-100">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Lokasi</h4>
                                        <p class="text-sm font-black text-slate-800">{{ $umkm->desa }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Button -->
                    <button
                        class="w-full group bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-600 font-bold py-4 rounded-2xl border border-dashed border-slate-200 hover:border-rose-200 transition-all text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95">
                        <i class="fas fa-exclamation-triangle opacity-50 group-hover:opacity-100 transition-opacity"></i>
                        Laporkan Iklan Ini
                    </button>
                </div>

                <!-- Right Content: Products -->
                <div class="lg:col-span-8">
                    <div class="flex items-end justify-between mb-10">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mb-3">
                                <i class="fas fa-box"></i> Katalog
                            </div>
                            <h2 class="text-3xl font-black text-slate-800 mb-1">Daftar Produk</h2>
                            <p class="text-slate-500 font-medium text-sm">Temukan produk terbaik dari
                                {{ $umkm->nama_usaha }}</p>
                        </div>
                        <span
                            class="hidden md:flex bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl shadow-lg shadow-slate-900/20 items-center gap-2">
                            <i class="fas fa-layer-group text-teal-400"></i> {{ count($products) }} Item
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        @forelse($products as $product)
                            <div
                                class="group bg-white rounded-[2.5rem] p-4 shadow-lg shadow-slate-200/50 border border-slate-100 hover:border-indigo-200 hover:shadow-[0_20px_40px_-15px_rgba(79,70,229,0.2)] transition-all duration-500 hover:-translate-y-2 flex flex-col h-full">
                                <!-- Product Image -->
                                <div class="relative aspect-[4/3] rounded-[2rem] overflow-hidden mb-6 shadow-inner bg-slate-50">
                                    @if($product->foto_produk)
                                        <img src="{{ asset('storage/' . $product->foto_produk) }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700 ease-in-out">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i class="fas fa-box-open text-5xl opacity-30"></i>
                                        </div>
                                    @endif

                                    <div class="absolute top-4 right-4">
                                        <span
                                            class="bg-indigo-600/90 backdrop-blur-md text-white font-black px-4 py-2 rounded-xl shadow-lg border border-white/20 text-sm">
                                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="px-2 flex-grow flex flex-col">
                                    <h3
                                        class="text-xl font-black text-slate-800 mb-2 truncate group-hover:text-indigo-600 transition-colors">
                                        {{ $product->nama_produk }}
                                    </h3>
                                    <p
                                        class="text-[13px] text-slate-500 font-medium mb-6 line-clamp-2 leading-relaxed h-10 border-l-2 border-slate-100 pl-3">
                                        {{ $product->deskripsi ?? 'Produk berkualitas tinggi dari ' . $umkm->nama_usaha }}
                                    </p>

                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->no_wa) }}?text={{ urlencode('Halo ' . $umkm->nama_usaha . ', saya tertarik dengan produk *' . $product->nama_produk . '* seharga Rp ' . number_format($product->harga, 0, ',', '.')) }}"
                                        target="_blank"
                                        class="mt-auto w-full inline-flex items-center justify-center gap-2 bg-slate-50 text-slate-700 font-black py-4 rounded-2xl hover:bg-emerald-500 hover:text-white transition-all transform active:scale-95 text-xs uppercase tracking-widest shadow-sm hover:shadow-emerald-500/30 group/btn border border-slate-100 hover:border-emerald-500">
                                        <i class="fab fa-whatsapp text-lg group-hover/btn:scale-110 transition-transform"></i>
                                        <span>Beli Sekarang</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full py-20 text-center bg-white rounded-[3rem] border border-dashed border-slate-200">
                                <div
                                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-box-open text-3xl"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-lg mb-2">Belum Ada Produk</h3>
                                <p class="text-slate-500 text-sm font-medium">Toko ini belum menambahkan produk ke etalase.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .pattern-grid-lg {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection