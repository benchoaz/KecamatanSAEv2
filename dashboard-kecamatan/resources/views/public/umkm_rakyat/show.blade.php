@extends('layouts.public')

@section('content')
    <div class="min-h-screen bg-gray-50 font-sans">

        <!-- Compact White Header -->
        <div class="bg-white border-b border-gray-100 shadow-sm">
            <div class="container mx-auto px-6 py-6">
                <!-- Back Button -->
                <a href="{{ route('umkm_rakyat.index') }}"
                    class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-700 font-bold text-xs uppercase tracking-widest transition-colors mb-6">
                    <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke Katalog
                </a>

                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Store Logo -->
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 md:w-28 md:h-28 rounded-2xl bg-gray-100 overflow-hidden border-2 border-gray-100 shadow-sm">
                            @if($umkm->foto_usaha)
                                <img src="{{ asset('storage/' . $umkm->foto_usaha) }}"
                                    class="w-full h-full object-cover" alt="{{ $umkm->nama_usaha }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-store text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        @if($umkm->is_verified)
                        <div class="absolute -bottom-2 -right-2 bg-blue-500 text-white w-7 h-7 rounded-full flex items-center justify-center border-2 border-white shadow-sm" title="Terverifikasi Resmi">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        @else
                        <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white w-7 h-7 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        @endif
                    </div>

                    <!-- Store Info -->
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="bg-teal-50 text-teal-700 border border-teal-100 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                                {{ $umkm->jenis_usaha }}
                            </span>
                            <span class="bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                                <i class="fas fa-map-marker-alt text-[8px]"></i> {{ $umkm->desa }}
                            </span>

                            @php $opStatus = $umkm->operational_status; @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $opStatus['bg'] }} {{ $opStatus['text'] }} border {{ $opStatus['is_open'] ? 'border-emerald-100' : 'border-rose-100' }}">
                                <i class="fas fa-circle text-[6px] mr-1"></i> {{ $opStatus['label'] }}
                            </span>

                            @if($umkm->verification_level == 'legal')
                            <span class="bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                                <i class="fas fa-certificate text-[8px]"></i> Legalitas NIB
                            </span>
                            @elseif($umkm->verification_level == 'warga')
                            <span class="bg-teal-50 text-teal-700 border border-teal-100 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                                <i class="fas fa-user-check text-[8px]"></i> Warga Terverifikasi
                            </span>
                            @endif
                        </div>

                        <h1 class="text-2xl md:text-3xl font-black text-slate-800 leading-tight mb-1 flex items-center gap-2">
                            {{ $umkm->nama_usaha }}
                            @if($umkm->verification_level == 'legal')
                                <i class="fas fa-check-circle text-blue-500 text-xl shadow-sm" title="Terverifikasi Legal (NIB)"></i>
                            @elseif($umkm->verification_level == 'warga')
                                <i class="fas fa-check-circle text-teal-500 text-xl shadow-sm" title="Warga Terverifikasi"></i>
                            @endif
                        </h1>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-user text-xs text-slate-300"></i> {{ $umkm->nama_pemilik }}
                            </span>
                            @if($umkm->operating_hours)
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-clock text-xs text-slate-300"></i> {{ $umkm->operating_hours }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-xs text-slate-300"></i> Bergabung {{ $umkm->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="flex flex-col gap-3 shrink-0 w-full md:w-auto">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->no_wa) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 bg-[#25D366] text-white font-black px-6 py-3.5 rounded-xl shadow-md shadow-green-500/20 hover:bg-[#1ebe5a] hover:-translate-y-0.5 transition-all active:scale-95">
                            <i class="fab fa-whatsapp text-xl"></i>
                            <div class="flex flex-col items-start leading-tight">
                                <span class="text-[9px] uppercase tracking-widest opacity-80">Chat Langsung</span>
                                <span class="text-sm">Hubungi Penjual</span>
                            </div>
                        </a>

                        @if($umkm->tokopedia_url || $umkm->shopee_url || $umkm->tiktok_url)
                            <div class="flex gap-2">
                                @if($umkm->tokopedia_url)
                                    <a href="{{ $umkm->tokopedia_url }}" target="_blank"
                                        class="flex-1 flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-slate-600 font-bold px-3 py-2 rounded-lg hover:border-[#42b549] hover:text-[#42b549] hover:bg-[#42b549]/5 transition-all text-xs">
                                        <img src="https://assets.tokopedia.net/assets-tokopedia-lite/v2/zeus/kratos/6046e723.png" class="w-4 h-4" alt="Tokopedia"> Tokopedia
                                    </a>
                                @endif
                                @if($umkm->shopee_url)
                                    <a href="{{ $umkm->shopee_url }}" target="_blank"
                                        class="flex-1 flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-slate-600 font-bold px-3 py-2 rounded-lg hover:border-[#ee4d2d] hover:text-[#ee4d2d] hover:bg-[#ee4d2d]/5 transition-all text-xs">
                                        <img src="https://logospng.org/download/shopee/logo-shopee-icon-1024.png" class="w-4 h-4" alt="Shopee"> Shopee
                                    </a>
                                @endif
                                @if($umkm->tiktok_url)
                                    <a href="{{ $umkm->tiktok_url }}" target="_blank"
                                        class="flex-1 flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-slate-600 font-bold px-3 py-2 rounded-lg hover:border-black hover:text-black hover:bg-black/5 transition-all text-xs">
                                        <i class="fab fa-tiktok"></i> TikTok
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Body -->
        <div class="container mx-auto px-6 py-10 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Left Sidebar -->
                <div class="lg:col-span-4 space-y-8 h-fit lg:sticky lg:top-24">
                    <!-- Description Card -->
                    <div
                        class="bg-white rounded-[2.5rem] p-6 md:p-8 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group hover:border-teal-100 transition-colors">
                        <div
                            class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-teal-50 rounded-full blur-2xl group-hover:bg-teal-100 transition-colors">
                        </div>

                        <div class="relative z-10">
                            <h3
                                class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <i class="fas fa-info-circle text-teal-400"></i> Tentang Usaha
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
                                @if($umkm->nib_number)
                                <div class="flex items-center gap-4 group/item">
                                    <div
                                        class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-600 transition-colors border border-blue-100">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-0.5">
                                            No. NIB OSS</h4>
                                        <p class="text-sm font-black text-slate-800">
                                            {{ $umkm->nib_number }}</p>
                                    </div>
                                </div>
                                @endif
                                <div class="flex items-center gap-4 group/item">
                                    <div
                                        class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover/item:bg-teal-50 group-hover/item:text-teal-500 transition-colors border border-slate-100">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Lokasi</h4>
                                        <p class="text-sm font-black text-slate-800">{{ $umkm->desa }}</p>
                                        @if($umkm->patokan_lokasi)
                                            <p class="text-[10px] text-slate-500 font-medium leading-tight mt-1">
                                                <i class="fas fa-info-circle text-indigo-400 mr-1"></i> {{ $umkm->patokan_lokasi }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Button -->
                    <button
                        class="w-full group bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 font-bold py-5 rounded-3xl border border-dashed border-slate-200 hover:border-rose-200 transition-all text-[10px] uppercase tracking-widest flex items-center justify-center gap-3 active:scale-95 shadow-sm">
                        <i class="fas fa-exclamation-triangle opacity-50 group-hover:opacity-100 transition-opacity"></i>
                        Laporkan Profil Toko
                    </button>
                </div>

                <!-- Right Content: Products -->
                <div class="lg:col-span-8">
                    <div class="flex flex-col md:flex-row items-center md:items-end justify-between mb-10 gap-6">
                        <div class="text-center md:text-left">
                            <div
                                class="inline-flex items-center gap-2 bg-teal-50 text-teal-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mb-3">
                                <i class="fas fa-box"></i> Katalog Produk
                            </div>
                            <h2 class="text-3xl font-black text-slate-800 mb-1 leading-tight">Daftar Produk</h2>
                            <p class="text-slate-500 font-medium text-sm">Temukan koleksi pilihan toko kami</p>
                        </div>
                        <span
                            class="bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-2xl shadow-lg flex items-center gap-2">
                            <i class="fas fa-layer-group text-teal-400"></i> {{ count($products) }} Item Tersedia
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8">
                        @forelse($products as $product)
                            <div
                                class="group bg-white rounded-[2.5rem] p-4 shadow-lg shadow-slate-200/50 border border-slate-100 hover:border-teal-200 hover:shadow-[0_20px_40px_-15px_rgba(13,148,136,0.15)] transition-all duration-500 hover:-translate-y-2 flex flex-col h-full">
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

                                    <div class="absolute top-4 right-4 text-right">
                                        <span
                                            class="bg-slate-900/90 backdrop-blur-md text-white font-black px-4 py-2 rounded-xl shadow-lg border border-white/20 text-sm">
                                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                                            <span class="text-[10px] opacity-60 font-medium lowercase">/{{ $product->satuan_harga ?? 'Pcs' }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="px-2 flex-grow flex flex-col">
                                    <h3
                                        class="text-xl font-black text-slate-800 mb-2 truncate group-hover:text-teal-600 transition-colors">
                                        {{ $product->nama_produk }}
                                    </h3>
                                    <p
                                        class="text-[13px] text-slate-500 font-medium mb-6 line-clamp-2 leading-relaxed h-10 border-l-2 border-slate-100 pl-3">
                                        {{ $product->deskripsi ?? 'Produk berkualitas tinggi dari ' . $umkm->nama_usaha }}
                                    </p>

                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->no_wa) }}?text={{ urlencode('Halo ' . $umkm->nama_usaha . ', saya tertarik dengan produk *' . $product->nama_produk . '*. Apakah masih tersedia?') }}"
                                        target="_blank"
                                        class="mt-auto w-full inline-flex items-center justify-center gap-2 bg-[#25D366] text-white font-black py-4 rounded-2xl hover:bg-[#1ebe5a] transition-all active:scale-95 text-xs shadow-sm hover:shadow-green-500/30">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                        <span>Tanya via WhatsApp</span>
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
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
        .animate-shimmer { animation: shimmer 2s infinite; }
    </style>
@endsection