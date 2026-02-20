@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#f8fafc] relative overflow-hidden font-sans pb-20">
        <!-- Premium Background Orbs -->
        <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-teal-200/20 rounded-full blur-[120px] pointer-events-none animate-pulse"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-200/20 rounded-full blur-[120px] pointer-events-none animate-pulse" style="animation-delay: 2s"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
            
            {{-- Navigation/Breadcrumb --}}
            <nav class="flex mb-8 items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                <a href="/" class="hover:text-teal-600 transition-colors">Beranda</a>
                <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                <span class="text-slate-600">Seller Center</span>
            </nav>

            <!-- Main Header -->
            <div class="bg-white rounded-[2.5rem] p-8 mb-10 shadow-sm border border-slate-200/60 overflow-hidden relative group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:bg-teal-500/10 transition-colors duration-700"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-slate-800 to-slate-950 flex items-center justify-center text-white text-3xl shadow-xl shadow-slate-900/20 border-4 border-white">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-white rounded-full"></div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $umkm->nama_usaha }}</h1>
                            <div class="flex flex-wrap items-center gap-4 text-slate-500 font-bold text-xs uppercase tracking-wider">
                                <span class="flex items-center gap-1.5"><i class="fas fa-check-circle text-emerald-500"></i> Terverifikasi</span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-tag text-teal-500"></i> Etalase UMKM</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('umkm_rakyat.show', $umkm->slug) }}" target="_blank"
                            class="bg-teal-600 hover:bg-teal-700 text-white font-black px-6 py-3.5 rounded-2xl transition-all flex items-center gap-2 shadow-lg shadow-teal-600/20 active:scale-95">
                            <i class="fas fa-external-link-alt text-xs"></i>
                            <span class="text-[11px] uppercase tracking-widest">Kunjungi Etalase</span>
                        </a>
                        <a href="{{ route('umkm_rakyat.settings', $umkm->manage_token) }}"
                            class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-black px-6 py-3.5 rounded-2xl transition-all flex items-center gap-2 shadow-sm hover:shadow-md active:scale-95">
                            <i class="fas fa-cog text-slate-400"></i>
                            <span class="text-[11px] uppercase tracking-widest">Pengaturan</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Sidebar Tools -->
                <div class="lg:col-span-4 space-y-8">
                    
                    {{-- Quick Stats Box --}}
                    <div class="bg-gradient-to-br from-slate-800 to-slate-950 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-teal-500/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2"></div>
                        
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                             Ringkasan Etalase
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-6 relative z-10">
                            <div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">Total Produk</div>
                                <div class="text-3xl font-black">{{ count($products) }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">Aktif Sejak</div>
                                <div class="text-base font-black text-emerald-400">{{ $umkm->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Box --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-slate-100/50 sticky top-24">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600 text-lg">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h2 class="text-lg font-black text-slate-800 tracking-tight">Tambah Produk</h2>
                        </div>

                        <form action="{{ route('umkm_rakyat.product.store', $umkm->manage_token) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="space-y-2 group">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-teal-600">Nama Produk</label>
                                <input type="text" name="nama_produk" required placeholder="Nama produk anda..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/5 transition-all outline-none">
                            </div>

                            <div class="space-y-2 group">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-teal-600">Harga (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                                    <input type="number" name="harga" required placeholder="0"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-5 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/5 transition-all outline-none">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                                <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/5 transition-all outline-none resize-none"></textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Foto Produk</label>
                                <input type="file" name="foto_produk" class="hidden" id="foto_produk" accept="image/*">
                                <label for="foto_produk"
                                    class="relative group/upl flex flex-col items-center justify-center w-full h-32 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 cursor-pointer hover:border-teal-400 hover:bg-teal-50/30 transition-all overflow-hidden">
                                    <div class="flex flex-col items-center justify-center opacity-60 group-hover/upl:opacity-100 transition-opacity">
                                        <i class="fas fa-image text-2xl mb-2 text-slate-400 group-hover/upl:text-teal-500 transition-colors"></i>
                                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-tighter group-hover/upl:text-teal-600">Pilih File</p>
                                    </div>
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-slate-900/10 active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-up-from-bracket text-xs"></i>
                                <span class="text-[11px] uppercase tracking-[0.2em]">Terbitkan Produk</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Products Display -->
                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between mb-8 px-2">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Katalog Produk</h2>
                            <p class="text-slate-500 text-sm font-medium">Data produk yang tampil di etalase publik</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-20">
                        @forelse($products as $product)
                            <div class="group/c bg-white rounded-[2.2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-slate-200 transition-all duration-500 border border-slate-100 flex flex-col p-4 relative">
                                
                                {{-- Top Tools --}}
                                <div class="absolute top-6 right-6 z-20">
                                    <div class="dropdown dropdown-end">
                                        <label tabindex="0" class="btn btn-sm btn-circle bg-white/80 backdrop-blur border-slate-200 text-slate-400 hover:bg-white hover:text-rose-500 shadow-sm border">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </label>
                                        <ul tabindex="0" class="dropdown-content z-[30] menu p-2 shadow-2xl bg-white rounded-2xl w-44 border border-slate-100 mt-2">
                                            <li>
                                                 <form action="{{ route('umkm_rakyat.product.delete', [$umkm->manage_token, $product->id]) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:bg-rose-50 font-bold text-xs py-3"><i class="fas fa-trash-alt mr-2"></i> Konfirmasi Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Image Section --}}
                                <div class="aspect-[4/3] rounded-[1.8rem] overflow-hidden bg-slate-50 border border-slate-100 mb-5 relative">
                                    @if($product->foto_produk)
                                        <img src="{{ asset('storage/' . $product->foto_produk) }}" class="w-full h-full object-cover group-hover/c:scale-110 transition-transform duration-1000">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-200">
                                            <i class="fas fa-image text-4xl opacity-40"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover/c:opacity-100 transition-opacity"></div>
                                </div>
                                
                                {{-- Content --}}
                                <div class="px-2 flex flex-col flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-black text-slate-800 text-lg group-hover/c:text-teal-700 transition-colors truncate">
                                            {{ $product->nama_produk }}
                                        </h3>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-base font-black text-slate-900">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                                        <span class="w-1.5 h-1.5 bg-slate-200 rounded-full"></span>
                                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-widest bg-teal-50 px-2 py-0.5 rounded-lg border border-teal-100/50">Aktif</span>
                                    </div>

                                    <p class="text-xs text-slate-400 font-medium line-clamp-2 leading-relaxed mb-6">{{ $product->deskripsi ?? 'Belum ada deskripsi produk.' }}</p>
                                    
                                    <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                                        <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter">Diperbarui {{ $product->updated_at->diffForHumans() }}</span>
                                        <a href="#" class="text-teal-600 hover:text-teal-800 font-black text-[10px] uppercase tracking-widest flex items-center gap-1.5 transition-all group-hover/c:gap-2">
                                            Edit <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="bg-white rounded-[3rem] border-4 border-dashed border-slate-100 p-16 text-center group hover:border-teal-200 transition-all duration-500">
                                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200 group-hover:bg-teal-50 group-hover:text-teal-400 group-hover:scale-110 transition-all">
                                        <i class="fas fa-box-open text-4xl"></i>
                                    </div>
                                    <h3 class="text-slate-800 font-black text-xl mb-3 tracking-tight">Mulai Jualan Produk Anda</h3>
                                    <p class="text-slate-400 text-sm font-medium mb-10 max-w-sm mx-auto leading-relaxed">Etalase anda saat ini masih kosong. Tambahkan produk unggulan melalui form di samping.</p>
                                    <button onclick="document.querySelector('input[name=nama_produk]').focus()" class="bg-teal-600 hover:bg-teal-700 text-white px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-teal-600/20 active:scale-95 transition-all">
                                        <i class="fas fa-plus mr-2"></i> Tambah Produk Sekarang
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
             <!-- Safety Guard Section -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white/40 backdrop-blur rounded-3xl border border-white p-8 shadow-sm flex flex-col md:flex-row items-center gap-8 border-l-4 border-l-amber-400">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-3xl flex items-center justify-center text-2xl shrink-0 shadow-sm border border-amber-200/50">
                        <i class="fas fa-shield-keyhole"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-2 flex items-center gap-2">
                            Akses Keamanan Penjual
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                        </h4>
                        <p class="text-xs text-slate-500 font-bold leading-relaxed mb-4">
                            Halaman ini menggunakan <span class="text-slate-800">Token Akses Unik</span>. Simpan (Bookmark) URL halaman ini di browser Anda agar dapat kembali mengelola toko kapan saja tanpa perlu login manual.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
                                <i class="fas fa-print"></i> Cetak Token
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
