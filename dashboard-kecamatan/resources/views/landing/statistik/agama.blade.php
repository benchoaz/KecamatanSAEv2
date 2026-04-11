@extends('landing.statistik.layout')

@section('stat_title', 'Statistik Agama')
@section('stat_badge', 'Rincian Agama')
@section('stat_header', 'Keberagaman Agama Penduduk')
@section('stat_description')
    Laporan rincian pemeluk agama yang dihimpun dari data sinkronisasi 17 desa di wilayah {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="font-black text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-fuchsia-50 text-fuchsia-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book-quran"></i>
                </div>
                Distribusi Agama Per Desa
            </h3>
            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 italic">
                Geser tabel ke kanan untuk melihat rincian <i class="fas fa-arrow-right ml-1"></i>
            </div>
        </div>

        <div class="relative overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/30">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-[10px] sticky left-0 z-10 bg-slate-800">Nama Desa</th>
                        @foreach($topAgama as $label)
                            <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] min-w-[110px]">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($desas as $desa)
                    @php
                        $stats = is_string($desa->stat_agama) ? json_decode($desa->stat_agama, true) : ($desa->stat_agama ?? []);
                        $mapped = collect($stats)->pluck('jumlah', 'nama')->toArray();
                    @endphp
                    <tr class="hover:bg-fuchsia-50/50 transition-colors group bg-white">
                        <td class="px-6 py-4 font-bold text-slate-700 sticky left-0 z-10 bg-white group-hover:bg-fuchsia-50/50 border-r border-slate-100">
                            {{ $desa->nama_desa }}
                        </td>
                        @foreach($topAgama as $label)
                            <td class="px-6 py-4 text-center">
                                @if(isset($mapped[$label]))
                                    <span class="font-bold text-slate-700">{{ number_format($mapped[$label]) }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
