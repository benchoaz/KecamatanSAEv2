@extends('landing.statistik.layout')

@section('stat_title', 'Overview Demografi')
@section('stat_badge', 'Overview Statistik')
@section('stat_header', 'Laporan Demografi 17 Desa')
@section('stat_description')
    Ringkasan data kependudukan, rumah tangga, dan wilayah seluruh desa di {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
    <!-- Stat Summary Cards - 4 Pillars -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-14">
        <!-- Demografi -->
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl shadow-indigo-700/20">
            <i class="fas fa-users text-2xl text-indigo-200 mb-3"></i>
            <p class="text-3xl font-black">{{ number_format($demografiStats['total_penduduk']) }}</p>
            <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mt-1">Total Penduduk</p>
        </div>
        <!-- Pendidikan (Higher Ed) -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-3xl p-6 text-white shadow-xl shadow-emerald-700/20">
            <i class="fas fa-graduation-cap text-2xl text-emerald-200 mb-3"></i>
            <p class="text-3xl font-black">{{ number_format($summary['pendidikan_tinggi']) }}</p>
            <p class="text-emerald-200 text-xs font-bold uppercase tracking-widest mt-1">Lulusan Sarjana</p>
        </div>
        <!-- Kesehatan (Stunting) -->
        <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-3xl p-6 text-white shadow-xl shadow-rose-600/20">
            <i class="fas fa-heart-pulse text-2xl text-rose-200 mb-3"></i>
            <p class="text-3xl font-black">{{ number_format($summary['stunting_cases']) }}</p>
            <p class="text-rose-200 text-xs font-bold uppercase tracking-widest mt-1">Kasus Stunting</p>
        </div>
        <!-- Ekonomi (P3KE) -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-xl shadow-amber-600/20">
            <i class="fas fa-coins text-2xl text-amber-200 mb-3"></i>
            <p class="text-3xl font-black">{{ number_format($summary['kk_total']) }}</p>
            <p class="text-amber-200 text-xs font-bold uppercase tracking-widest mt-1">Kepala Keluarga</p>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="font-black text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-table"></i>
                </div>
                Matriks Demografi Umum (17 Desa)
            </h3>
        </div>

        <div class="relative overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/30">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-[10px] sticky left-0 z-10 bg-slate-800">Nama Desa</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Penduduk</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">KK</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Laki-Laki</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Perempuan</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Luas (Km²)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 italic">
                    @foreach($desas as $desa)
                    <tr class="hover:bg-teal-50/50 transition-colors group bg-white">
                        <td class="px-6 py-4 font-bold text-slate-700 sticky left-0 z-10 bg-white group-hover:bg-teal-50/50 not-italic border-r border-slate-100">
                            {{ $desa->nama_desa }}
                        </td>
                        <td class="px-6 py-4 text-center font-medium not-italic">{{ number_format($desa->jumlah_penduduk ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium not-italic">{{ number_format($desa->jumlah_kk ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium not-italic text-indigo-600">{{ number_format($desa->jumlah_laki_laki ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium not-italic text-rose-600">{{ number_format($desa->jumlah_perempuan ?? 0) }}</td>
                        <td class="px-6 py-4 text-center text-slate-400 not-italic">
                            {{ $desa->luas_wilayah ? number_format($desa->luas_wilayah, 2) : '0.00' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
