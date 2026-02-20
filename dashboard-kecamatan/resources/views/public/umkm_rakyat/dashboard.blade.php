@extends('layouts.umkm')

@section('page_title', 'Ringkasan Bisnis')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Stats Card -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 transition-all hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-sky-100 rounded-2xl flex items-center justify-center text-sky-600">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-sky-600 bg-sky-50 px-3 py-1 rounded-full uppercase">Produk</span>
            </div>
            <h3 class="text-4xl font-black text-slate-800">{{ $umkm->products->count() }}</h3>
            <p class="text-slate-400 font-bold text-xs mt-2 uppercase tracking-widest">Total Produk Aktif</p>
        </div>

        <!-- Stats Card -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 transition-all hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase">Views</span>
            </div>
            <h3 class="text-4xl font-black text-slate-800">{{ rand(10, 100) }}</h3>
            <p class="text-slate-400 font-bold text-xs mt-2 uppercase tracking-widest">Kunjungan Etalase</p>
        </div>

        <!-- Stats Card -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 transition-all hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="fab fa-whatsapp text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full uppercase">Chat</span>
            </div>
            <h3 class="text-4xl font-black text-slate-800">{{ rand(5, 50) }}</h3>
            <p class="text-slate-400 font-bold text-xs mt-2 uppercase tracking-widest">Permintaan Pesanan</p>
        </div>

        <!-- Action Card -->
        <a href="{{ route('umkm_rakyat.manage.products', $umkm->manage_token) }}"
            class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl shadow-slate-900/20 border border-slate-800 transition-all hover:scale-[1.02] group">
            <div class="flex items-center justify-between mb-6 text-white/50">
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white">
                    <i class="fas fa-plus"></i>
                </div>
                <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
            </div>
            <h3 class="text-xl font-black text-white leading-tight">Tambah Produk Baru</h3>
            <p class="text-white/40 font-bold text-xs mt-2 uppercase tracking-widest">Update Dagangan Anda</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Welcome Card -->
        <div
            class="lg:col-span-2 bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-50 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-sky-50 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10">
                <h2 class="text-2xl font-black text-slate-800 mb-4">Tips Berjualan Online</h2>
                <div class="space-y-6">
                    <div class="flex gap-6">
                        <div
                            class="w-14 h-14 bg-slate-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-slate-400 hover:bg-sky-500 hover:text-white transition-all cursor-default shadow-sm border border-slate-100">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800">Foto Produk yang Menarik</h4>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed mt-1">
                                Gunakan pencahayaan alami agar warna produk terlihat asli dan menggugah selera pelanggan.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div
                            class="w-14 h-14 bg-slate-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-slate-400 hover:bg-sky-500 hover:text-white transition-all cursor-default shadow-sm border border-slate-100">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800">Deskripsi yang Jelas</h4>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed mt-1">
                                Berikan informasi ukuran, bahan, atau varian rasa yang lengkap untuk mengurangi pertanyaan
                                berulang.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Status Card -->
        <div
            class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[3rem] p-10 text-white shadow-2xl shadow-indigo-900/20 relative overflow-hidden">
            <h2 class="text-xl font-black mb-6 flex items-center gap-3">
                <i class="fas fa-info-circle opacity-50"></i> Informasi Verifikasi
            </h2>
            @if($umkm->status == 'aktif')
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                    <p class="text-sm font-medium leading-relaxed opacity-90">
                        Toko Anda sudah <strong>Terverifikasi</strong>. Pelanggan dapat menemukan produk Anda di halaman utama
                        Kecamatan News.
                    </p>
                </div>
            @else
                <div class="bg-amber-400/20 backdrop-blur-md rounded-2xl p-6 border border-amber-400/20 mb-6">
                    <p class="text-sm font-medium leading-relaxed">
                        Toko Anda sedang menunggu verifikasi admin kecamatan.
                    </p>
                </div>
                <a href="https://wa.me/{{ $admin_wa ?? '08123456789' }}" target="_blank"
                    class="block w-full bg-white text-indigo-700 text-center font-black py-4 rounded-2xl shadow-lg hover:bg-indigo-50 transition-all">
                    Hubungi Admin
                </a>
            @endif
        </div>
    </div>
@endsection