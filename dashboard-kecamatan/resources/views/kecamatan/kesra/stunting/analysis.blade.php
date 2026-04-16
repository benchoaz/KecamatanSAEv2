@extends('layouts.app')

@section('title', 'Analisis Konvergensi Stunting')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('kecamatan.kesra.index') }}" class="text-slate-400 hover:text-emerald-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Analisis Stunting</h1>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase tracking-wider">
                    Kemendagri
                </span>
            </div>
            <p class="text-slate-500 text-sm">Dashboard pemantauan capaian 8 Aksi Konvergensi Penurunan Stunting tingkat kecamatan.</p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('kecamatan.kesra.stunting.ranking') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-emerald-600 hover:border-emerald-200 transition-all text-sm font-bold shadow-sm">
                <i class="fas fa-trophy text-amber-500"></i> Peringkat Desa
            </a>
        </div>
    </div>

    @if(!$convergenceData)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
        <div class="w-16 h-16 bg-amber-100 text-amber-500 rounded-xl flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Data Belum Tersedia</h3>
        <p class="text-sm text-slate-600 mb-4">Sistem belum melakukan penarikan data dari Portal Kemendagri.</p>
        <p class="text-xs text-slate-500">Jalankan command <code>php artisan scrape:stunting-convergence</code> untuk sinkronisasi awal.</p>
    </div>
    @else
        @php
            $summary = $convergenceData['summary'] ?? [];
            $scrapedAt = $convergenceData['scraped_at'] ?? null;
            $aksi = $convergenceData['aksi_konvergensi'] ?? [];
            $capaian = $convergenceData['capaian_per_desa'] ?? [];
            
            $totalForm = $summary['total_form'] ?? 0;
            $approved = $summary['approved'] ?? 0;
            $pending = $summary['pending'] ?? 0;
            $pctOverall = $totalForm > 0 ? round(($approved / $totalForm) * 100) : 0;
        @endphp

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 text-emerald-600 text-8xl">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="text-sm font-bold text-slate-500 mb-1">Capaian Keseluruhan</div>
                <div class="text-3xl font-black text-emerald-600 mb-2">{{ $pctOverall }}%</div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pctOverall }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="text-sm font-bold text-slate-500 mb-1">Total Form Aksi</div>
                <div class="text-3xl font-black text-slate-800 mb-2">{{ $totalForm }}</div>
                <div class="text-xs text-slate-400">Total indikator pelaporan</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="text-sm font-bold text-slate-500 mb-1">Telah Disetujui</div>
                <div class="text-3xl font-black text-blue-600 mb-2">{{ $approved }}</div>
                <div class="text-xs text-slate-400">Tervalidasi di portal pusat</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="text-sm font-bold text-slate-500 mb-1">Butuh Perbaikan</div>
                <div class="text-3xl font-black text-rose-600 mb-2">{{ $pending }}</div>
                <div class="text-xs text-slate-400">Status pending / revisi</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Trend & Semesters -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Semester Split -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Komparasi Semester</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        @php
                            $s1Total = $summary['semester_1_total'] ?? 0;
                            $s1Approved = $summary['semester_1_approved'] ?? 0;
                            $s2Total = $summary['semester_2_total'] ?? 0;
                            $s2Approved = $summary['semester_2_approved'] ?? 0;
                            $pctS1 = $s1Total > 0 ? round(($s1Approved / $s1Total) * 100) : 0;
                            $pctS2 = $s2Total > 0 ? round(($s2Approved / $s2Total) * 100) : 0;
                        @endphp
                        
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <div class="font-bold text-slate-700">Semester 1</div>
                                    <div class="text-xs text-slate-400">{{ $s1Approved }} dari {{ $s1Total }} form</div>
                                </div>
                                <div class="text-xl font-black text-blue-600">{{ $pctS1 }}%</div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5">
                                <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $pctS1 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <div class="font-bold text-slate-700">Semester 2</div>
                                    <div class="text-xs text-slate-400">{{ $s2Approved }} dari {{ $s2Total }} form</div>
                                </div>
                                <div class="text-xl font-black text-emerald-600">{{ $pctS2 }}%</div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5">
                                <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $pctS2 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kelompok Aksi -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Berdasarkan Kelompok Aksi</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if(!empty($summary['per_kelompok']))
                            @foreach($summary['per_kelompok'] as $kelompok => $stat)
                            @php
                                $pct = $stat['total'] > 0 ? round(($stat['approved'] / $stat['total']) * 100) : 0;
                                $colorClass = $pct === 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-rose-500');
                                $textClass = $pct === 100 ? 'text-emerald-600' : ($pct >= 50 ? 'text-amber-600' : 'text-rose-600');
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-slate-600 truncate mr-2">{{ $kelompok }}</span>
                                    <span class="text-xs font-black {{ $textClass }}">{{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1">
                                    <div class="h-1.5 rounded-full {{ $colorClass }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="text-center">
                     <p class="text-xs text-slate-400">
                        Sinkronisasi terakhir: <br>
                        <strong>{{ $scrapedAt ? \Carbon\Carbon::parse($scrapedAt)->format('d F Y, H:i') : '-' }} WIB</strong>
                    </p>
                </div>
            </div>

            <!-- Right Column: Detail Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">Status Pelaporan Aksi Konvergensi</h3>
                        <span class="text-xs font-medium bg-white px-2 py-1 rounded border border-slate-200 shadow-sm">{{ count($aksi) }} form</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white border-b border-slate-100 text-slate-500 uppercase text-[10px] tracking-wider font-bold">
                                <tr>
                                    <th class="px-6 py-4">No</th>
                                    <th class="px-6 py-4">Nama Form</th>
                                    <th class="px-6 py-4">Kelompok</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($aksi as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3 text-slate-500">{{ $item['nomor'] }}</td>
                                    <td class="px-6 py-3 font-medium text-slate-800">
                                        {{ $item['nama_form'] }}
                                        @if($item['catatan'] && $item['catatan'] !== '-' && !$item['is_approved'])
                                            <div class="text-[10px] text-rose-500 mt-1 italic"><i class="fas fa-info-circle"></i> {{ $item['catatan'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-xs text-slate-500 whitespace-nowrap">{{ Str::limit($item['kelompok'], 20) }}</td>
                                    <td class="px-6 py-3 text-center">
                                        @if($item['is_approved'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <i class="fas fa-check mr-1"></i> Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800" title="{{ $item['status'] }}">
                                                <i class="fas fa-times mr-1"></i> {{ Str::limit($item['status'], 15) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">Tidak ada data aksi konvergensi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    @endif
</div>
@endsection
