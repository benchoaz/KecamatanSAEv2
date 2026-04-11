@extends('layouts.public')

@section('title', $workItem->job_title . ' - ' . $workItem->display_name . ' – ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('meta')
    <meta name="description"
        content="{{ $workItem->job_title }} oleh {{ $workItem->display_name }} di {{ $workItem->service_area ?? appProfile()->region_level . ' ' . appProfile()->region_name }}. {{ $workItem->short_description }}">
    <meta name="keywords"
        content="{{ $workItem->job_title }}, {{ $workItem->display_name }}, jasa {{ strtolower($workItem->job_category) }}, {{ appProfile()->region_name }}">
@endsection

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-teal-50">

        {{-- Breadcrumb - Compact --}}
        <div class="bg-white border-b border-slate-100">
            <div class="container mx-auto px-6 py-2.5">
                <nav class="flex items-center gap-2 text-sm">
                    <a href="/" class="text-teal-600 hover:text-teal-700 font-medium">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                    <i class="fas fa-chevron-right text-slate-400 text-xs"></i>
                    <a href="{{ route('economy.index') }}" class="text-teal-600 hover:text-teal-700 font-medium">
                        Direktori Kerja & Jasa
                    </a>
                    <i class="fas fa-chevron-right text-slate-400 text-xs"></i>
                    <span class="text-slate-600">{{ $workItem->job_title }}</span>
                </nav>
            </div>
        </div>

        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">

                        {{-- Header - Compact --}}
                        <div class="bg-gradient-to-r from-teal-600 to-emerald-600 text-white p-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-3xl shadow-lg">
                                    <i class="fas {{ $workItem->icon }}"></i>
                                </div>
                                <div class="flex-1">
                                    <span
                                        class="inline-block px-2 py-0.5 bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold rounded-full mb-2">
                                        {{ $workItem->job_category }}
                                    </span>
                                    <h1 class="text-2xl md:text-3xl font-black mb-1 flex items-center gap-2">
                                        {{ $workItem->job_title }}
                                        @if($workItem->is_verified)
                                            <i class="fas fa-check-circle text-blue-400 text-xl shadow-sm" title="Terverifikasi Resmi oleh Kecamatan"></i>
                                        @endif
                                    </h1>
                                    <p class="text-base text-teal-50 opacity-90">
                                        <i class="fas fa-user mr-1.5 text-xs"></i>
                                        {{ $workItem->display_name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Details - Compact --}}
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                                @if($workItem->service_area)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center text-teal-600 flex-shrink-0">
                                            <i class="fas fa-map-marker-alt text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-700 mb-0.5 text-xs">Wilayah Layanan</h3>
                                            <p class="text-slate-600 text-xs">{{ $workItem->service_area }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($workItem->service_time)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center text-teal-600 flex-shrink-0">
                                            <i class="fas fa-clock text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-700 mb-0.5 text-xs">Jam Layanan</h3>
                                            <p class="text-slate-600 text-xs">{{ $workItem->service_time }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center text-teal-600 flex-shrink-0">
                                        <i class="fas fa-briefcase text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-700 mb-0.5 text-xs">Tipe Pekerjaan</h3>
                                        <p class="text-slate-600 capitalize text-xs">{{ $workItem->job_type }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center text-teal-600 flex-shrink-0">
                                        <i class="fas fa-database text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-700 mb-0.5 text-xs">Sumber Data</h3>
                                        <p class="text-slate-600 capitalize text-xs">{{ ucfirst($workItem->data_source) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($workItem->short_description)
                                <div class="bg-slate-50 rounded-xl p-5 mb-6">
                                    <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2 text-xs">
                                        <i class="fas fa-info-circle text-teal-600 uppercase"></i>
                                        Deskripsi Layanan
                                    </h3>
                                    <p class="text-slate-700 leading-relaxed text-xs">{{ $workItem->short_description }}</p>
                                </div>
                            @endif

                            {{-- Contact Button - Compact --}}
                            <div class="flex gap-3">
                                <a href="{{ $workItem->whatsapp_link }}" target="_blank"
                                    class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-center transition-all shadow-md text-sm">
                                    <i class="fab fa-whatsapp mr-2"></i>
                                    Chat WhatsApp
                                </a>
                                <a href="tel:{{ $workItem->contact_phone }}"
                                    class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold transition-all shadow-md">
                                    <i class="fas fa-phone"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Disclaimer --}}
                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl p-6 mt-8">
                        <div class="flex items-start gap-4">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-2xl mt-1"></i>
                            <div>
                                <h4 class="font-bold text-amber-900 mb-2">Perhatian</h4>
                                <p class="text-amber-800 leading-relaxed text-sm">
                                    Informasi ini disediakan oleh <strong>Pemerintah {{ appProfile()->region_level }}
                                        {{ appProfile()->region_name }}</strong>
                                    berdasarkan pendataan yang ada. Pemerintah kecamatan hanya memfasilitasi informasi
                                    dan tidak terlibat dalam hubungan kerja, negosiasi harga, atau transaksi apapun.
                                    Harap berhati-hati dan pastikan kesepakatan yang jelas sebelum menggunakan jasa.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">

                    {{-- Quick Contact Card --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-slate-100 sticky top-6">
                        <h3 class="font-bold text-slate-800 mb-4 text-lg">Kontak Cepat</h3>

                        <div class="space-y-3">
                            <a href="{{ $workItem->whatsapp_link }}" target="_blank"
                                class="block px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-center transition-all">
                                <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                            </a>

                            <a href="tel:{{ $workItem->contact_phone }}"
                                class="block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-center transition-all">
                                <i class="fas fa-phone mr-2"></i> Telepon
                            </a>

                            <button
                                onclick="navigator.share ? navigator.share({title: '{{ $workItem->job_title }}', text: '{{ $workItem->display_name }}', url: window.location.href}) : alert('Browser tidak support share')"
                                class="w-full px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-all">
                                <i class="fas fa-share-alt mr-2"></i> Bagikan
                            </button>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <p class="text-xs text-slate-500 text-center">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Data terverifikasi oleh<br>Pemerintah {{ appProfile()->region_level }}
                                {{ appProfile()->region_name }}
                            </p>
                        </div>
                    </div>

                    {{-- Related Items --}}
                    @if($relatedItems->count() > 0)
                        <div class="bg-white rounded-2xl shadow-xl p-6 border border-slate-100">
                            <h3 class="font-bold text-slate-800 mb-4 text-lg">
                                <i class="fas fa-list mr-2 text-teal-600"></i>
                                Jasa Serupa
                            </h3>

                            <div class="space-y-4">
                                @foreach($relatedItems as $related)
                                    <a href="{{ route('economy.show', $related->id) }}"
                                        class="block p-4 bg-slate-50 hover:bg-teal-50 rounded-xl transition-all border border-slate-100 hover:border-teal-200">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center text-teal-600 flex-shrink-0">
                                                <i class="fas {{ $related->icon }}"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $related->job_title }}</h4>
                                                <p class="text-xs text-slate-500 truncate">{{ $related->display_name }}</p>
                                            </div>
                                            <i class="fas fa-chevron-right text-slate-400 text-sm"></i>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <a href="{{ route('economy.index', ['kategori' => $workItem->job_category]) }}"
                                class="block mt-4 text-center text-teal-600 hover:text-teal-700 font-bold text-sm">
                                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection