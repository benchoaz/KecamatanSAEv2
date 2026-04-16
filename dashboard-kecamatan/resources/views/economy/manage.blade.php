@extends('layouts.public')

@section('page_title', 'Kelola Data Jasa')

@section('content')
<section class="min-h-screen bg-slate-50 py-20 px-6">
    <div class="max-w-4xl mx-auto">
        {{-- Header Status --}}
        <div class="bg-white rounded-[3rem] p-8 md:p-12 shadow-2xl shadow-slate-200 border border-slate-50 mb-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-teal-600 rounded-[2rem] flex items-center justify-center text-white text-3xl shadow-xl shadow-teal-500/20">
                    <i class="fas {{ $workItem->icon ?? 'fa-id-badge' }}"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 leading-tight mb-1">{{ $workItem->job_title }}</h1>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-200">
                            {{ $workItem->job_category }}
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-bold {{ $workItem->status === 'active' ? 'text-emerald-600' : 'text-slate-400' }}">
                            <span class="w-2 h-2 rounded-full {{ $workItem->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            {{ $workItem->status === 'active' ? 'Tayang ke Publik' : 'Sembunyi' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('economy.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                <i class="fas fa-eye"></i> Lihat Listing Umum
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl mb-10 flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('economy.update', $workItem->id) }}" method="POST">
            @csrf
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200 border border-slate-50 overflow-hidden">
                <div class="p-8 md:p-12 border-b border-slate-50">
                    <h3 class="text-xl font-black text-slate-800 mb-2">Informasi Profil Jasa</h3>
                    <p class="text-sm text-slate-500 font-medium">Perbarui informasi keahlian atau atur visibilitas layanan Anda.</p>
                </div>

                <div class="p-8 md:p-12 space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Nama Tampilan (Personal/Tim)</label>
                            <input type="text" name="display_name" value="{{ $workItem->display_name }}" required
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Nama Layanan / Keahlian</label>
                            <input type="text" name="job_title" value="{{ $workItem->job_title }}" required
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Kategori Jasa</label>
                            <select name="job_category" required
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none appearance-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $workItem->job_category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Tipe Kerja</label>
                            <select name="job_type" required
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none appearance-none">
                                @foreach($jobTypes as $val => $label)
                                    <option value="{{ $val }}" {{ $workItem->job_type == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Area Layanan</label>
                            <input type="text" name="service_area" value="{{ $workItem->service_area }}" 
                                placeholder="Contoh: Seluruh Kecamatan / Desa Tertentu"
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Jam Operasional</label>
                            <input type="text" name="service_time" id="service_time_input" value="{{ $workItem->service_time }}"
                                placeholder="Contoh: 08:00 - 17:00 / 24 Jam"
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none">
                            <div class="flex flex-wrap gap-2 mt-3">
                                <button type="button" onclick="setServiceTime('Setiap Hari (24 Jam)')" class="px-3 py-1.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-600 text-slate-500 rounded-lg text-[10px] font-bold transition-all border border-slate-200">24 Jam</button>
                                <button type="button" onclick="setServiceTime('Senin - Jumat (08:00 - 16:00)')" class="px-3 py-1.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-600 text-slate-500 rounded-lg text-[10px] font-bold transition-all border border-slate-200">Jam Kerja</button>
                                <button type="button" onclick="setServiceTime('Weekend (09:00 - 17:00)')" class="px-3 py-1.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-600 text-slate-500 rounded-lg text-[10px] font-bold transition-all border border-slate-200">Weekend</button>
                                <button type="button" onclick="setServiceTime('Sesuai Perjanjian')" class="px-3 py-1.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-600 text-slate-500 rounded-lg text-[10px] font-bold transition-all border border-slate-200">Sesuai Janji</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Deskripsi Layanan</label>
                        <textarea name="short_description" rows="4" 
                            class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-teal-500/20 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none resize-none">{{ $workItem->short_description }}</textarea>
                    </div>

                    <div class="pt-8 border-t border-slate-50">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1">Status Publikasi</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="status" value="active" class="hidden peer" {{ $workItem->status === 'active' ? 'checked' : '' }}>
                                <div class="p-6 rounded-2xl border-2 border-slate-50 bg-slate-50 peer-checked:bg-white peer-checked:border-emerald-500 peer-checked:ring-4 peer-checked:ring-emerald-500/10 transition-all group-hover:bg-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-200 peer-checked:bg-emerald-100 flex items-center justify-center text-slate-400 peer-checked:text-emerald-600">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-700">AKTIF</p>
                                            <p class="text-[10px] font-medium text-slate-400">Tampilkan di listing publik</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="status" value="inactive" class="hidden peer" {{ $workItem->status === 'inactive' ? 'checked' : '' }}>
                                <div class="p-6 rounded-2xl border-2 border-slate-50 bg-slate-50 peer-checked:bg-white peer-checked:border-rose-500 peer-checked:ring-4 peer-checked:ring-rose-500/10 transition-all group-hover:bg-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-200 peer-checked:bg-rose-100 flex items-center justify-center text-slate-400 peer-checked:text-rose-600">
                                            <i class="fas fa-eye-slash"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-700">NON-AKTIF</p>
                                            <p class="text-[10px] font-medium text-slate-400">Sembunyikan sementara</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-12 bg-slate-50 border-t border-white flex items-center justify-between gap-6">
                    <p class="text-xs text-slate-400 font-medium">Data ini terakhir diperbarui pada {{ $workItem->updated_at->format('d/m/Y H:i') }}</p>
                    <button type="submit" 
                        class="px-10 py-4 bg-teal-600 hover:bg-teal-700 text-white font-black rounded-2xl shadow-xl shadow-teal-500/30 transition-all transform hover:-translate-y-1 active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    function setServiceTime(value) {
        document.getElementById('service_time_input').value = value;
    }
</script>
@endsection
