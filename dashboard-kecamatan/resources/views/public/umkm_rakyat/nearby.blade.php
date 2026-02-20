@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 bg-rose-50 text-rose-700 px-4 py-2 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Gunakan Lokasi Anda</span>
                </div>
                <h1 class="text-4xl font-black text-slate-800 mb-4">UMKM Terdekat</h1>
                <p class="text-slate-500 font-medium max-w-2xl mx-auto">Temukan produk dan layanan dari tetangga di sekitar
                    lokasi Anda saat ini.</p>
            </div>

            <!-- Location Permission Card -->
            <div id="locationPrompt"
                class="bg-white rounded-[2.5rem] p-12 shadow-2xl shadow-slate-200/50 border border-slate-100 text-center max-w-2xl mx-auto mb-16">
                <div
                    class="w-24 h-24 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-8 text-3xl shadow-inner">
                    <i class="fas fa-location-arrow animate-bounce"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-800 mb-4">Aktifkan Lokasi?</h2>
                <p class="text-slate-600 font-medium leading-relaxed mb-8">
                    Untuk menampilkan UMKM yang paling dekat dengan Anda melalui smartphone, kami membutuhkan akses ke
                    lokasi perangkat Anda.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button onclick="getLocation()"
                        class="bg-rose-600 text-white font-black px-8 py-4 rounded-2xl shadow-lg shadow-rose-500/30 hover:bg-rose-700 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-search-location"></i>
                        <span>Cari UMKM Terdekat</span>
                    </button>
                </div>
            </div>

            <!-- Results Grid (Initially Static, showing latest) -->
            <div id="resultsGrid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($umkms as $item)
                    <div class="bg-white rounded-[2.5rem] p-4 shadow-lg shadow-slate-200/50 border border-slate-100 opacity-60">
                        <div class="h-40 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-200 text-3xl mb-4">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="font-black text-slate-800 truncate mb-1">{{ $item->nama_usaha }}</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-4"><i
                                class="fas fa-map-marker-alt mr-1"></i> {{ $item->desa }}</p>
                        <div class="h-2 w-full bg-slate-100 rounded-full"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                Swal.fire({
                    title: 'Mendeteksi Lokasi...',
                    text: 'Mohon izinkan akses lokasi pada browser Anda.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                navigator.geolocation.getCurrentPosition(position => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lokasi Terdeteksi',
                        text: 'Menampilkan UMKM di sekitar wilayah Anda.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // In real implementation, redirect with lat/lng
                        window.location.href = "{{ route('umkm_rakyat.index') }}?lat=" + position.coords.latitude + "&lng=" + position.coords.longitude;
                    });
                }, error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Akses lokasi ditolak atau tidak tersedia.'
                    });
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Geolocation tidak didukung oleh browser ini.' });
            }
        }
    </script>
@endsection