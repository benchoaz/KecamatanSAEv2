@extends('landing.statistik.layout')

@section('stat_title', 'Statistik Kesejahteraan')
@section('stat_badge', 'Data P3KE')
@section('stat_header', 'Kesejahteraan Keluarga (P3KE)')
@section('stat_description')
    Laporan rincian tingkat kesejahteraan keluarga berdasarkan data P3KE yang dihimpun dari 17 desa di wilayah {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="font-black text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie"></i>
                </div>
                Data Desil Kesejahteraan Per Desa
            </h3>
        </div>

        <div class="relative overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/30">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-[10px] sticky left-0 z-10 bg-slate-800">Nama Desa</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] bg-blue-600 text-white">Desil 1</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Desil 2</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Desil 3</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Desil 4</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] bg-slate-700">Total KK Terdata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($desas as $desa)
                    @php
                        $stats = is_string($desa->stat_kesejahteraan) ? json_decode($desa->stat_kesejahteraan, true) : ($desa->stat_kesejahteraan ?? []);
                    @endphp
                    <tr class="hover:bg-blue-50/50 transition-colors group bg-white">
                        <td class="px-6 py-4 font-bold text-slate-700 sticky left-0 z-10 bg-white group-hover:bg-blue-50/50 border-r border-slate-100">
                            {{ $desa->nama_desa }}
                        </td>
                        <td class="px-6 py-4 text-center font-black text-blue-600 bg-blue-50">{{ number_format($stats['desil_1'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium">{{ number_format($stats['desil_2'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium">{{ number_format($stats['desil_3'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-medium">{{ number_format($stats['desil_4'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-center font-black text-slate-700">
                             {{ number_format(($stats['desil_1'] ?? 0) + ($stats['desil_2'] ?? 0) + ($stats['desil_3'] ?? 0) + ($stats['desil_4'] ?? 0)) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100 text-[10px] text-amber-800 leading-relaxed">
            <i class="fas fa-info-circle mr-1"></i> <strong>Catatan:</strong> Desil 1 menunjukkan kelompok keluarga dengan tingkat kesejahteraan terendah. Data ini sangat penting untuk penentuan target intervensi sosial.
        </div>
    </div>
@endsection
