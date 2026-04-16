@extends('layouts.app')

@section('title', 'Peringkat Desa - Stunting')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('kecamatan.kesra.stunting.analysis') }}" class="text-slate-400 hover:text-emerald-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Peringkat Capaian Desa</h1>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase tracking-wider">
                    Konvergensi Stunting
                </span>
            </div>
            <p class="text-slate-500 text-sm">Peringkat desa berdasarkan tingkat pelaporan capaian layanan dan data sasaran stunting per semester.</p>
        </div>
    </div>

    @if(!$convergenceData)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
        <h3 class="font-bold text-slate-800 mb-1">Data Belum Tersedia</h3>
        <p class="text-sm text-slate-600">Sistem belum melakukan sinkronisasi data dengan Portal Kemendagri.</p>
    </div>
    @else
        @php
            $capaian = $convergenceData['capaian_per_desa'] ?? [];
            
            // Map the arrays by village name for easy lookup
            $capaianMap = [];
            foreach ($capaian as $c) {
                // Ensure array key is lowercase string
                $namaRaw = strtolower(trim($c['nama_desa']));
                
                // Indikator 30 = Target (Total)
                // Indikator 31 = Capaian (Realisasi)
                // The keys might have long header strings, we take the values
                $keys = array_keys($c['indikator'] ?? []);
                $target = isset($keys[0]) ? ($c['indikator'][$keys[0]] ?? 0) : 0;
                $realisasi = isset($keys[1]) ? ($c['indikator'][$keys[1]] ?? 0) : 0;
                
                $capaianMap[$namaRaw] = [
                    'status' => strtolower($c['status']),
                    'target' => $target,
                    'realisasi' => $realisasi,
                    'is_approved' => strpos(strtolower($c['status']), 'approved') !== false,
                    'percent' => $target > 0 ? min(100, round(($realisasi / $target) * 100, 1)) : 0
                ];
            }
        @endphp

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="bg-slate-50 p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Tabel Peringkat Capaian Layanan</h3>
                    <p class="text-xs text-slate-500 mt-1">Mengukur persentase capaian pemanfaatan pekarangan untuk peningkatan asupan gizi terhadap jumlah akta/KIA yang diterbitkan (Indikator Layanan Kemendagri).</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">Rank</th>
                            <th class="px-6 py-4">Nama Desa</th>
                            <th class="px-6 py-4 text-center">Validasi Admin</th>
                            <th class="px-6 py-4 text-right">Target (Balita)</th>
                            <th class="px-6 py-4 text-right">Realisasi Layanan</th>
                            <th class="px-6 py-4 text-center">Capaian (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            // Sort desa model instances by their achievement in capaianMap
                            $sortedDesas = $desas->sortByDesc(function($desa) use ($capaianMap) {
                                $nama = strtolower(str_replace(' ', '', $desa->nama_desa));
                                return $capaianMap[$nama]['percent'] ?? -1; // -1 to push unrecorded to bottom
                            })->values();
                        @endphp
                        
                        @foreach($sortedDesas as $index => $desa)
                        @php
                            $namaL = strtolower(str_replace(' ', '', $desa->nama_desa));
                            // Try exact match or direct match
                            $data = $capaianMap[$namaL] ?? $capaianMap[strtolower($desa->nama_desa)] ?? null;
                            $hasData = !is_null($data);
                            $pct = $hasData ? $data['percent'] : 0;
                            
                            $rankClass = '';
                            $iconColor = '';
                            if ($index === 0) { $rankClass = 'bg-amber-100 text-amber-700 font-black'; $iconColor = 'text-amber-500'; }
                            elseif ($index === 1) { $rankClass = 'bg-slate-200 text-slate-700 font-black'; $iconColor = 'text-slate-400'; }
                            elseif ($index === 2) { $rankClass = 'bg-orange-100 text-orange-800 font-black'; $iconColor = 'text-orange-600'; }
                            else { $rankClass = 'text-slate-400 font-bold'; }
                            
                            // Color code the achievement
                            $pctColor = 'text-slate-500';
                            $barColor = 'bg-slate-200';
                            if ($pct >= 80) { $pctColor = 'text-emerald-600'; $barColor = 'bg-emerald-500'; }
                            elseif ($pct >= 50) { $pctColor = 'text-amber-600'; $barColor = 'bg-amber-500'; }
                            elseif ($hasData && $pct < 50) { $pctColor = 'text-rose-600'; $barColor = 'bg-rose-500'; }
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors {{ $hasData ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="px-6 py-4 text-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto {{ $rankClass }}">
                                    @if($index < 3)
                                        <i class="fas fa-trophy text-sm"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $desa->nama_desa }}
                                @if(!$hasData)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-[9px] bg-slate-200 text-slate-500 uppercase tracking-wider">No Data</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($hasData)
                                    @if($data['is_approved'])
                                        <div class="inline-flex flex-col items-center">
                                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                            <span class="text-[10px] text-emerald-600 mt-1 font-bold">Approved</span>
                                        </div>
                                    @else
                                        <div class="inline-flex flex-col items-center">
                                            <i class="fas fa-clock text-amber-500 text-lg"></i>
                                            <span class="text-[10px] text-amber-600 mt-1 font-bold">Pending</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right font-medium text-slate-700">
                                {{ $hasData ? number_format($data['target'], 0, ',', '.') : '-' }}
                            </td>
                            
                            <td class="px-6 py-4 text-right font-medium text-slate-700">
                                {{ $hasData ? number_format($data['realisasi'], 0, ',', '.') : '-' }}
                            </td>
                            
                            <td class="px-6 py-4 w-48">
                                @if($hasData)
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-black {{ $pctColor }}">{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                @else
                                    <div class="text-xs text-slate-400 text-center italic">Belum dilaporkan</div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    @endif
</div>
@endsection
