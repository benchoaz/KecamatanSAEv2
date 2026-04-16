@extends('landing.statistik.layout')

@section('stat_title', 'Statistik Kesehatan')
@section('stat_badge', 'Rincian Kesehatan')
@section('stat_header', 'Kesehatan &amp; Penanganan Stunting')
@section('stat_description')
    Laporan rincian indikator kesehatan balita dan prevalensi Stunting yang dihimpun dari data sinkronisasi 17 desa di wilayah {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
    {{-- SECTION 1: KESIMPULAN STRATEGIS (CONVERGENCE DATA) --}}
    @isset($convergenceData)
    @php
        $vils = $convergenceData['villages'] ?? [];
        $totalPenduduk = collect($vils)->sum(fn($v) => (float)str_replace('.', '', $v['indicators']['Jumlah Penduduk'] ?? 0));
        $totalAnak = collect($vils)->sum(fn($v) => (float)$v['indicators']['Anak usia 0-17 tahun'] ?? 0);
        $totalKIA = collect($vils)->sum(fn($v) => (float)$v['indicators']['30Jumlah akta kelahiran dan KIA (Kartu Identitas Anak) yang diterbitkan'] ?? 0);
        $approvedCount = collect($vils)->filter(fn($v) => strpos(strtolower($v['status']), 'approved') !== false)->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Penduduk</div>
            <div class="text-2xl font-black text-slate-800">{{ number_format($totalPenduduk) }}</div>
            <div class="text-[10px] text-emerald-600 mt-1 font-bold"><i class="fas fa-users mr-1"></i> Terarsip Kemendagri</div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Estimasi Balita (0-17)</div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($totalAnak) }}</div>
            <div class="text-[10px] text-rose-400 mt-1 font-bold"><i class="fas fa-child mr-1"></i> Sasaran Intervensi</div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Capaian KIA</div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($totalKIA) }}</div>
            <div class="text-[10px] text-emerald-400 mt-1 font-bold"><i class="fas fa-id-card mr-1"></i> Indikator 30</div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Validasi</div>
            <div class="text-2xl font-black text-amber-600">{{ $approvedCount }}/{{ count($vils) }}</div>
            <div class="text-[10px] text-amber-400 mt-1 font-bold"><i class="fas fa-check-double mr-1"></i> Desa Approved</div>
        </div>
    </div>
    @endisset

    {{-- SECTION 2: DATA STUNTING LOCAL (FROM FINAL VERSION) --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 overflow-hidden mb-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="font-black text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                Indikator Kesehatan Per Desa (Data Puskesmas/Posyandu)
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

    {{-- SECTION 3: DATA KONVERGENSI KEMENDAGRI (FROM LOCAL VERSION) --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-12">
        <div class="bg-slate-50 p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Detail Indikator Konvergensi Per Desa</h3>
                <p class="text-xs text-slate-500 mt-1">Data sinkronisasi sasarankec, pendukungkec, dan masterkec (Sumber: Portal Bangda Kemendagri).</p>
            </div>
            
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition-colors text-sm font-bold">
                <i class="fas fa-file-excel"></i> Export Laporan
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 sticky left-0 z-10 bg-slate-50 shadow-[1px_0_0_#e2e8f0]">Desa</th>
                        <th class="px-6 py-4 text-center">Penduduk</th>
                        <th class="px-6 py-4 text-center">Sasaran 0-17</th>
                        <th class="px-6 py-4 text-center">KIA (Ind. 30)</th>
                        <th class="px-6 py-4 text-center">Gizi (Ind. 31)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @isset($convergenceData)
                        @php
                            $vils = $convergenceData['villages'] ?? [];
                            usort($vils, function($a, $b) { return strcmp($a['nama_desa'], $b['nama_desa']); });
                        @endphp
                        @forelse($vils as $desa)
                            @php
                                $ind = $desa['indicators'] ?? [];
                                $kia = $ind['30Jumlah akta kelahiran dan KIA (Kartu Identitas Anak) yang diterbitkan'] ?? 0;
                                $gizi = $ind['31Jumlah Desa/kelurahan  yang memiliki program pemanfaatan pekarangan untuk peningkatan pangan (asupan Gizi)'] ?? 0;
                                $penduduk = $ind['Jumlah Penduduk'] ?? 0;
                                $anak = $ind['Anak usia 0-17 tahun'] ?? 0;
                                $isApproved = strpos(strtolower($desa['status'] ?? ''), 'approved') !== false;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4 font-bold text-slate-700 sticky left-0 z-10 bg-white group-hover:bg-slate-50 shadow-[1px_0_0_#f1f5f9]">
                                    {{ $desa['nama_desa'] }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">{{ number_format((float)str_replace('.', '', $penduduk)) }}</td>
                                <td class="px-6 py-4 text-center font-medium text-rose-600">{{ number_format((float)$anak) }}</td>
                                <td class="px-6 py-4 text-center font-medium">{{ number_format((float)$kia) }}</td>
                                <td class="px-6 py-4 text-center font-medium text-emerald-600">{{ number_format((float)$gizi) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($isApproved)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 uppercase">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center">Data kosong.</td></tr>
                        @endforelse
                    @else
                        <tr><td colspan="6" class="px-6 py-8 text-center font-bold text-rose-500">Gagal memuat data konvergensi.</td></tr>
                    @endisset
                </tbody>
            </table>
        </div>
    </div>
@endsection
