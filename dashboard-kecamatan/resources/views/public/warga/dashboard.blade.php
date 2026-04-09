<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Terpadu - Pusat Kendali Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>

<body class="text-slate-800 pb-20">

    <!-- Top Navigation -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-id-badge text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-none">Dasbor Warga</h1>
                        <p class="text-xs text-slate-500 font-medium">Pusat Kendali Ekonomi Terpadu</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('portal_warga.logout') }}" class="text-xs font-bold text-rose-500 bg-rose-50 px-4 py-2 rounded-lg hover:bg-rose-100 transition-colors flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl translate-x-1/3 -translate-y-1/3 pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-bold tracking-wide backdrop-blur-md border border-white/10 uppercase">
                        Identitas Terverifikasi
                    </span>
                </div>
                <h2 class="text-2xl md:text-3xl font-black mb-1">Halo, Identitas Warga</h2>
                <p class="text-slate-300 text-sm md:text-base font-medium flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-400"></i> +{{ $phone }}
                </p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-pulse">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Operational Status Management (NEW) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-xl text-slate-800">Status Toko & Jasa Hari Ini</h3>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Atur Kesediaan Anda</span>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @php 
                    $allAssets = collect($umkms->map(fn($item) => ['data' => $item, 'type' => 'umkm']))
                        ->concat($umkmLocals->map(fn($item) => ['data' => $item, 'type' => 'umkm_local']))
                        ->concat($jasas->map(fn($item) => ['data' => $item, 'type' => 'jasa']));
                @endphp

                @foreach($allAssets as $asset)
                @php 
                    $item = $asset['data'];
                    $type = $asset['type'];
                    $name = $type === 'umkm' ? $item->nama_usaha : ($type === 'jasa' ? $item->job_title : $item->name);
                    $opStatus = $item->operational_status;
                @endphp
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 {{ $opStatus['bg'] }} {{ $opStatus['text'] }} rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                            <i class="fas {{ $opStatus['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-black text-slate-800">{{ $name }}</h4>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-md {{ $opStatus['bg'] }} {{ $opStatus['text'] }} uppercase">{{ $opStatus['label'] }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-400">
                                Jam Operasional: <span class="text-slate-600">{{ $item->operating_hours ?: 'Belum diatur (Selalu Buka)' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Update Hours Modal Toggle (Future) -->
                        <button onclick="openHoursModal('{{ $type }}', '{{ $item->id }}', '{{ $item->operating_hours }}')" class="p-3 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-xl transition-all" title="Ubah Jam">
                            <i class="fas fa-clock"></i>
                        </button>
                        
                        <form action="{{ route('portal_warga.status_update') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <input type="hidden" name="operating_hours" value="{{ $item->operating_hours }}">
                            
                            @if($item->is_on_holiday)
                                <input type="hidden" name="is_on_holiday" value="0">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl text-xs font-black shadow-lg shadow-emerald-900/10 transition-all flex items-center gap-2">
                                    <i class="fas fa-play"></i> Buka Toko Kembali
                                </button>
                            @else
                                <input type="hidden" name="is_on_holiday" value="1">
                                <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 px-6 py-3 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                                    <i class="fas fa-calendar-times"></i> Libur Hari Ini
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Hours Modal Shell -->
        <dialog id="hoursModal" class="modal">
            <div class="modal-box rounded-3xl p-8 bg-white max-w-sm">
                <h3 class="font-black text-xl mb-4">Setel Jam Operasional</h3>
                <form id="hoursForm" action="{{ route('portal_warga.status_update') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" id="modal_type">
                    <input type="hidden" name="id" id="modal_id">
                    <input type="hidden" name="is_on_holiday" id="modal_is_holiday">
                    
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Jam Buka - Tutup</label>
                        <input type="text" name="operating_hours" id="modal_hours" placeholder="Contoh: 08:00 - 17:00" 
                               class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 transition-all">
                        <p class="text-[10px] text-slate-400 font-medium italic">* Gunakan format HH:mm - HH:mm</p>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="hoursModal.close()" class="flex-1 bg-slate-100 text-slate-600 p-4 rounded-2xl font-black text-xs hover:bg-slate-200 transition-all">Batal</button>
                        <button type="submit" class="flex-1 bg-slate-900 text-white p-4 rounded-2xl font-black text-xs shadow-lg shadow-slate-900/20 transition-all">Simpan Jam</button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <script>
            function openHoursModal(type, id, currentHours) {
                document.getElementById('modal_type').value = type;
                document.getElementById('modal_id').value = id;
                document.getElementById('modal_hours').value = currentHours || '08:00 - 17:00';
                document.getElementById('modal_is_holiday').value = 0; // Assume we want them open if setting hours
                document.getElementById('hoursModal').showModal();
            }
        </script>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- UMKM Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600">
                        <i class="fas fa-store text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 leading-tight">Bisnis UMKM</h3>
                        <p class="text-xs font-medium text-slate-500">Kelola toko dan etalase produk</p>
                    </div>
                </div>

                <div class="flex-grow space-y-4">
                    {{-- Blok 1: Bisnis Resmi (Model Umkm) --}}
                    @foreach($umkms as $umkm)
                    <div class="p-4 rounded-2xl border border-teal-100 bg-teal-50/10 hover:bg-teal-50 transition-colors group">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $umkm->nama_usaha }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $umkm->status == 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} uppercase tracking-wider">
                                        {{ $umkm->status }}
                                    </span>
                                    <span class="text-[9px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full uppercase">Verified UMKM</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('umkm_rakyat.manage', $umkm->manage_token) }}" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-teal-500 hover:text-teal-600 px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all">
                            Kelola Bisnis <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    @endforeach

                    {{-- Blok 2: Katalog Produk Cepat (Model UmkmLocal) --}}
                    @foreach($umkmLocals as $ul)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors group relative">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $ul->name }}</h4>
                                <p class="text-[10px] text-slate-500 font-medium truncate max-w-[200px]">{{ $ul->product }}</p>
                            </div>
                            <span class="text-[9px] font-bold text-slate-400 border border-slate-100 px-2 py-0.5 rounded-full uppercase">Katalog Cepat</span>
                        </div>
                        <div class="flex gap-2">
                             <a href="{{ route('economy.produk.show', $ul->id) }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-2 rounded-xl text-xs font-bold transition-all">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            {{-- Bridge button for management could be added here later --}}
                        </div>
                    </div>
                    @endforeach

                    @if($umkms->isEmpty() && $umkmLocals->isEmpty())
                    <div class="text-center py-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <i class="fas fa-box-open text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-500 mb-1">Daftar UMKM Belum Ada</p>
                        <p class="text-xs text-slate-400">Anda belum mendaftarkan UMKM.</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100">
                    <a href="{{ route('umkm_rakyat.create') }}" class="w-full inline-flex items-center justify-center gap-2 bg-teal-50 hover:bg-teal-100 text-teal-700 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                        <i class="fas fa-plus"></i> Buka Toko UMKM Baru
                    </a>
                </div>
            </div>

            <!-- Jasa & Pekerjaan Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 leading-tight">Jasa & Pekerjaan</h3>
                        <p class="text-xs font-medium text-slate-500">Kelola profil penyedia jasa / kerja</p>
                    </div>
                </div>

                <div class="flex-grow space-y-4">
                    @forelse($jasas as $jasa)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors group">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $jasa->job_title }}</h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $jasa->status == 'active' ? 'bg-emerald-100 text-emerald-700' : ($jasa->status == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }} uppercase tracking-wider">
                                    {{ $jasa->status }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('portal_warga.bridge.jasa', $jasa->id) }}" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-blue-500 hover:text-blue-600 px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all">
                            Kelola Jasa <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <i class="fas fa-hard-hat text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-500 mb-1">Profil Jasa Belum Ada</p>
                        <p class="text-xs text-slate-400">Anda belum mendaftar sebagai penyedia jasa.</p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100">
                    <a href="{{ route('economy.create') }}" class="w-full inline-flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                        <i class="fas fa-plus"></i> Tawarkan Jasa Anda
                    </a>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
