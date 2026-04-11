@extends('landing.statistik.layout')

@section('stat_title', 'Statistik Kesehatan')
@section('stat_badge', 'Rincian Kesehatan')
@section('stat_header', 'Kesehatan & Penanganan Stunting')
@section('stat_description')
    Laporan rincian indikator kesehatan balita dan prevalensi Stunting yang dihimpun dari data sinkronisasi 17 desa di wilayah {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="font-black text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                Indikator Kesehatan Per Desa
            </h3>
        </div>

        <div class="relative overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/30">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-[10px] sticky left-0 z-10 bg-slate-800">Nama Desa</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Total Balita</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] bg-rose-600 text-white">Stunting</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Gizi Normal</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Gizi Buruk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($desas as $desa)
                    @php
                        $stats = is_string($desa->stat_kesehatan) ? json_decode($desa->stat_kesehatan, true) : ($desa->stat_kesehatan ?? []);
                    @endphp
                    <tr class="hover:bg-rose-50/50 transition-colors group bg-white">
                        <td class="px-6 py-4 font-bold text-slate-700 sticky left-0 z-10 bg-white group-hover:bg-rose-50/50 border-r border-slate-100">
                            {{ $desa->nama_desa }}
                        </td>
                        <td class="px-6 py-4 text-center font-medium">{{ number_format(($stats['totalStunting'] ?? 0) + ($stats['totalGiziNormal'] ?? 0) + ($stats['totalGiziBuruk'] ?? 0)) }}</td>
                        <td class="px-6 py-4 text-center font-black text-rose-600 bg-rose-50">{{ number_format($stats['totalStunting'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium">{{ number_format($stats['totalGiziNormal'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium">{{ number_format($stats['totalGiziBuruk'] ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
