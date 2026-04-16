@extends('landing.statistik.layout')

@section('stat_title', 'Dashboard Statistik Wilayah')
@section('stat_badge', 'Infografis Terpadu')
@section('stat_header', 'Dashboard Statistik 17 Desa')
@section('stat_description')
    Ringkasan integrasi data kependudukan, sosial, kesehatan, dan kesejahteraan seluruh desa di {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
@php
    // === AGGREGATE ALL STATS FROM DESAS ===

    // Demografi totals (from demografiStats already passed)
    $totalPenduduk = $demografiStats['total_penduduk'];
    $totalLaki     = $demografiStats['total_laki'];
    $totalPerempuan= $demografiStats['total_perempuan'];
    $totalKk       = $demografiStats['total_kk'];
    $totalLuas     = $demografiStats['total_luas'];

    // Agama
    $agamaAgg = [];
    foreach ($desas as $desa) {
        foreach ($desa->stat_agama ?? [] as $item) {
            $n = $item['nama'] ?? ''; if ($n) $agamaAgg[$n] = ($agamaAgg[$n] ?? 0) + ($item['jumlah'] ?? 0);
        }
    }
    arsort($agamaAgg);
    $topAgama = array_slice($agamaAgg, 0, 6, true);
    $agamaGrandTotal = array_sum($agamaAgg) ?: 1;

    // Pendidikan
    $pendAgg = [];
    foreach ($desas as $desa) {
        foreach ($desa->stat_pendidikan ?? [] as $item) {
            $n = $item['nama'] ?? ''; if ($n) $pendAgg[$n] = ($pendAgg[$n] ?? 0) + ($item['jumlah'] ?? 0);
        }
    }
    arsort($pendAgg);
    $topPend = array_slice($pendAgg, 0, 7, true);
    $pendGrandTotal = array_sum($pendAgg) ?: 1;

    // Kesehatan
    $totStunting = 0; $totGiziNormal = 0; $totGiziBuruk = 0;
    foreach ($desas as $desa) {
        $h = $desa->stat_kesehatan ?? [];
        $totStunting   += $h['totalStunting']    ?? 0;
        $totGiziNormal += $h['totalGiziNormal']  ?? 0;
        $totGiziBuruk  += $h['totalGiziBuruk']   ?? 0;
    }
    $totAnak = $totStunting + $totGiziNormal + $totGiziBuruk ?: 1;

    // Desil / DTSEN
    $d1=0; $d2=0; $d3=0; $d4=0; $d5=0; $d610=0; $dKpm=0; $dTotal=0;
    foreach ($desas as $desa) {
        $d = $desa->stat_desil ?? [];
        $d1    += $d['totalDesil1']   ?? 0;
        $d2    += $d['totalDesil2']   ?? 0;
        $d3    += $d['totalDesil3']   ?? 0;
        $d4    += $d['totalDesil4']   ?? 0;
        $d5    += $d['totalDesil5']   ?? 0;
        $d610  += $d['totalDesil6_10']?? 0;
        $dKpm  += $d['totalKpm']      ?? 0;
        $dTotal+= $d['grandTotal']    ?? 0;
    }
    $dTotal = $dTotal ?: 1;
@endphp

{{-- =====================================================
     ROW 1: 6 KPI HERO CARDS
     ===================================================== --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
    @php
    $kpis = [
        ['icon'=>'fa-users',           'val'=>number_format($totalPenduduk), 'label'=>'Total Penduduk',    'from'=>'from-indigo-600', 'to'=>'to-violet-700',  'shadow'=>'shadow-indigo-400/30'],
        ['icon'=>'fa-mars',            'val'=>number_format($totalLaki),     'label'=>'Laki-Laki',         'from'=>'from-blue-500',   'to'=>'to-blue-700',    'shadow'=>'shadow-blue-400/30'],
        ['icon'=>'fa-venus',           'val'=>number_format($totalPerempuan),'label'=>'Perempuan',          'from'=>'from-pink-500',   'to'=>'to-rose-600',    'shadow'=>'shadow-pink-400/30'],
        ['icon'=>'fa-house-chimney',   'val'=>number_format($totalKk),      'label'=>'Kepala Keluarga',    'from'=>'from-amber-500',  'to'=>'to-orange-600',  'shadow'=>'shadow-amber-400/30'],
        ['icon'=>'fa-heart-circle-exclamation','val'=>number_format($totStunting),'label'=>'Kasus Stunting','from'=>'from-red-500',  'to'=>'to-rose-700',    'shadow'=>'shadow-red-400/30'],
        ['icon'=>'fa-hand-holding-heart','val'=>number_format($d1),         'label'=>'Desil 1 (DTSEN)',     'from'=>'from-teal-500',   'to'=>'to-emerald-700', 'shadow'=>'shadow-teal-400/30'],
    ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="bg-gradient-to-br {{ $kpi['from'] }} {{ $kpi['to'] }} rounded-3xl p-5 text-white shadow-xl {{ $kpi['shadow'] }} flex flex-col justify-between min-h-[130px]">
        <i class="fas {{ $kpi['icon'] }} text-xl opacity-70"></i>
        <div>
            <p class="text-2xl font-black leading-none">{{ $kpi['val'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-75 mt-1">{{ $kpi['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- =====================================================
     ROW 2: GENDER DONUT + AGAMA BAR
     ===================================================== --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">

    {{-- Gender Donut --}}
    <div class="md:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-lg p-6 flex flex-col">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-5 bg-indigo-500 rounded-full"></span>
            <h4 class="font-black text-slate-800 text-sm">Komposisi Gender</h4>
        </div>
        <p class="text-[10px] text-slate-400 mb-4 ml-4">Rasio penduduk berdasarkan jenis kelamin</p>
        <div class="relative flex-1 flex items-center justify-center">
            <canvas id="chartGender" style="max-height:200px"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-2xl font-black text-slate-800">{{ $totalPenduduk > 0 ? number_format($totalLaki/$totalPenduduk*100,1) : 0 }}%</span>
                <span class="text-[9px] text-blue-500 font-bold uppercase">Laki-Laki</span>
            </div>
        </div>
        <div class="flex justify-center gap-6 mt-4 text-[10px] font-bold">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Laki-Laki</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-pink-500 inline-block"></span> Perempuan</span>
        </div>
    </div>

    {{-- Agama Horizontal Bar --}}
    <div class="md:col-span-3 bg-white rounded-[2rem] border border-slate-100 shadow-lg p-6 flex flex-col">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-5 bg-violet-500 rounded-full"></span>
            <h4 class="font-black text-slate-800 text-sm">Komposisi Agama</h4>
        </div>
        <p class="text-[10px] text-slate-400 mb-5 ml-4">Distribusi kepercayaan penduduk seluruh desa</p>
        <div class="space-y-3 flex-1">
            @foreach($topAgama as $nama => $jumlah)
            @php $pct = $jumlah / $agamaGrandTotal * 100; @endphp
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-bold text-slate-700">{{ $nama }}</span>
                    <span class="text-xs font-black text-slate-500">{{ number_format($jumlah) }} <span class="text-[10px] font-normal text-slate-400">({{ number_format($pct,1) }}%)</span></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full bg-gradient-to-r from-violet-500 to-purple-600 transition-all duration-700" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- =====================================================
     ROW 3: PENDIDIKAN CHART + KESEHATAN GAUGE
     ===================================================== --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">

    {{-- Pendidikan Horizontal Bars --}}
    <div class="md:col-span-3 bg-white rounded-[2rem] border border-slate-100 shadow-lg p-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-5 bg-emerald-500 rounded-full"></span>
            <h4 class="font-black text-slate-800 text-sm">Tingkat Pendidikan</h4>
        </div>
        <p class="text-[10px] text-slate-400 mb-4 ml-4">Jenjang pendidikan terakhir seluruh penduduk</p>
        <canvas id="chartPendidikan" style="max-height:230px"></canvas>
    </div>

    {{-- Kesehatan --}}
    <div class="md:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-lg p-6 flex flex-col">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-5 bg-rose-500 rounded-full"></span>
            <h4 class="font-black text-slate-800 text-sm">Indikator Kesehatan</h4>
        </div>
        <p class="text-[10px] text-slate-400 mb-5 ml-4">Status gizi & stunting anak balita</p>

        {{-- Pie Chart --}}
        <div class="relative flex-1 flex items-center justify-center">
            <canvas id="chartKesehatan" style="max-height:180px"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-lg font-black text-red-600">{{ number_format($totAnak > 1 ? $totStunting/($totAnak)*100 : 0, 1) }}%</span>
                <span class="text-[9px] text-red-400 font-bold uppercase">Stunting</span>
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-4 space-y-2">
            @php
            $healthItems = [
                ['label'=>'Gizi Normal', 'val'=>$totGiziNormal, 'color'=>'bg-emerald-500'],
                ['label'=>'Stunting',    'val'=>$totStunting,   'color'=>'bg-red-500'],
                ['label'=>'Gizi Buruk',  'val'=>$totGiziBuruk,  'color'=>'bg-amber-500'],
            ];
            @endphp
            @foreach($healthItems as $h)
            <div class="flex items-center justify-between text-[10px]">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full {{ $h['color'] }} inline-block"></span> {{ $h['label'] }}</span>
                <span class="font-black text-slate-700">{{ number_format($h['val']) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- =====================================================
     ROW 4: DTSEN/DESIL FULL WIDTH
     ===================================================== --}}
<div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] shadow-2xl p-8 mb-6 overflow-hidden relative">
    {{-- Decorative --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/5 rounded-full -mr-16 -mt-16"></div>
    <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-500/5 rounded-full -ml-10 -mb-10"></div>

    <div class="flex items-center gap-2 mb-1 relative z-10">
        <span class="w-2 h-5 bg-teal-400 rounded-full"></span>
        <h4 class="font-black text-white text-sm">Piramida Kesejahteraan — DTSEN Desil 1-10</h4>
        <span class="ml-auto bg-teal-500/20 text-teal-300 text-[9px] font-bold px-3 py-1 rounded-full border border-teal-500/30">{{ number_format($dKpm) }} KPM Teridentifikasi</span>
    </div>
    <p class="text-[10px] text-slate-400 mb-6 ml-4 relative z-10">Komposisi keluarga berdasarkan tingkat kesejahteraan. Desil 1 = termiskin, Desil 10 = terkaya.</p>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative z-10">
        {{-- Bar Chart --}}
        <div>
            <canvas id="chartDesil" style="max-height:250px"></canvas>
        </div>

        {{-- Desil Cards --}}
        <div class="grid grid-cols-3 gap-3">
            @php
            $desilData = [
                ['n'=>1, 'val'=>$d1,   'color'=>'from-red-600 to-rose-700',     'ring'=>'ring-red-500/30'],
                ['n'=>2, 'val'=>$d2,   'color'=>'from-orange-500 to-orange-700', 'ring'=>'ring-orange-500/30'],
                ['n'=>3, 'val'=>$d3,   'color'=>'from-amber-500 to-amber-700',   'ring'=>'ring-amber-500/30'],
                ['n'=>4, 'val'=>$d4,   'color'=>'from-yellow-500 to-yellow-700', 'ring'=>'ring-yellow-500/30'],
                ['n'=>5, 'val'=>$d5,   'color'=>'from-lime-500 to-lime-700',     'ring'=>'ring-lime-500/30'],
                ['n'=>'6-10', 'val'=>$d610, 'color'=>'from-teal-500 to-emerald-700', 'ring'=>'ring-teal-500/30'],
            ];
            @endphp
            @foreach($desilData as $item)
            @php $pctd = $dTotal > 0 ? ($item['val']/$dTotal*100) : 0; @endphp
            <div class="bg-white/5 rounded-2xl p-3 ring-1 {{ $item['ring'] }} flex flex-col gap-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase">Desil {{ $item['n'] }}</span>
                <span class="text-xl font-black text-white">{{ number_format($item['val']) }}</span>
                <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-1 rounded-full bg-gradient-to-r {{ $item['color'] }}" style="width:{{ $pctd }}%"></div>
                </div>
                <span class="text-[9px] text-slate-400">{{ number_format($pctd, 1) }}% dari total</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- =====================================================
     ROW 5: SUMMARY INSIGHT CARDS
     ===================================================== --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
    $ratioSex  = $totalPerempuan > 0 ? round($totalLaki/$totalPerempuan*100) : 0;
    $avgPerDesa= count($desas) > 0 ? round($totalPenduduk/count($desas)) : 0;
    $stuntIdx  = $totAnak > 1 ? round($totStunting/$totAnak*100, 1) : 0;
    $desil1Pct = $dTotal > 1 ? round($d1/$dTotal*100, 1) : 0;
    $insights = [
        ['label'=>'Sex Ratio', 'val'=>$ratioSex, 'unit'=>'per 100 ♀', 'icon'=>'fa-scale-balanced', 'note'=>$ratioSex >= 100 ? 'Dominan Laki-Laki' : 'Dominan Perempuan', 'color'=>'text-blue-600', 'bg'=>'bg-blue-50'],
        ['label'=>'Rata-rata Penduduk', 'val'=>number_format($avgPerDesa), 'unit'=>'jiwa/desa', 'icon'=>'fa-chart-simple', 'note'=>'Per desa rata-rata', 'color'=>'text-indigo-600', 'bg'=>'bg-indigo-50'],
        ['label'=>'Prevalensi Stunting', 'val'=>$stuntIdx.'%', 'unit'=>'dari anak terdata', 'icon'=>'fa-child-reaching', 'note'=>$stuntIdx > 20 ? '⚠ Perlu perhatian' : '✓ Terkendali', 'color'=>'text-rose-600', 'bg'=>'bg-rose-50'],
        ['label'=>'Coverage Desil 1', 'val'=>$desil1Pct.'%', 'unit'=>'dari total DTSEN', 'icon'=>'fa-circle-half-stroke', 'note'=>'Kelompok rentan', 'color'=>'text-teal-600', 'bg'=>'bg-teal-50'],
    ];
    @endphp
    @foreach($insights as $ins)
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 {{ $ins['bg'] }} {{ $ins['color'] }} rounded-xl flex items-center justify-center">
                <i class="fas {{ $ins['icon'] }} text-sm"></i>
            </div>
            <span class="text-[10px] text-slate-400 font-medium">{{ $ins['unit'] }}</span>
        </div>
        <p class="text-2xl font-black text-slate-800 leading-none mb-1">{{ $ins['val'] }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">{{ $ins['label'] }}</p>
        <div class="mt-3 pt-3 border-t border-slate-50 text-[10px] {{ $ins['color'] }} font-bold">{{ $ins['note'] }}</div>
    </div>
    @endforeach
</div>

{{-- =====================================================
     ROW 6: VILLAGE MATRIX TABLE (Enhanced)
     ===================================================== --}}
<div class="bg-white rounded-[2rem] border border-slate-100 shadow-lg p-6 overflow-hidden">
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2 h-5 bg-slate-700 rounded-full"></span>
        <h4 class="font-black text-slate-800 text-sm">Matriks Lengkap 17 Desa</h4>
        <span class="ml-auto text-[10px] text-slate-400">Klik kolom untuk navigasi ke topik tersebut</span>
    </div>
    <div class="relative overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="bg-slate-800 text-white">
                    <th class="px-4 py-3 text-left font-bold sticky left-0 z-10 bg-slate-800">#</th>
                    <th class="px-4 py-3 text-left font-bold sticky left-6 z-10 bg-slate-800 min-w-[130px]">Desa</th>
                    <th class="px-4 py-3 text-center font-bold bg-indigo-700">Penduduk</th>
                    <th class="px-4 py-3 text-center font-bold">KK</th>
                    <th class="px-4 py-3 text-center font-bold text-blue-300">♂</th>
                    <th class="px-4 py-3 text-center font-bold text-pink-300">♀</th>
                    <th class="px-4 py-3 text-center font-bold bg-rose-700">Stunting</th>
                    <th class="px-4 py-3 text-center font-bold bg-teal-700">Desil 1</th>
                    <th class="px-4 py-3 text-center font-bold">Luas (km²)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($desas as $i => $desa)
                @php
                    $h = $desa->stat_kesehatan ?? [];
                    $d = $desa->stat_desil ?? [];
                    $stunt = $h['totalStunting'] ?? 0;
                    $desilSatu = $d['totalDesil1'] ?? 0;
                    $maxPop = $desas->max('jumlah_penduduk') ?: 1;
                    $barW = $desa->jumlah_penduduk ? ($desa->jumlah_penduduk/$maxPop*100) : 0;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors group {{ $i % 2 == 0 ? 'bg-white' : 'bg-slate-50/40' }}">
                    <td class="px-4 py-3 text-slate-300 font-bold sticky left-0 bg-inherit group-hover:bg-slate-50">{{ $i+1 }}</td>
                    <td class="px-4 py-3 font-bold text-slate-700 sticky left-6 bg-inherit group-hover:bg-slate-50 border-r border-slate-100 min-w-[130px]">
                        {{ $desa->nama_desa }}
                    </td>
                    <td class="px-4 py-3 text-center bg-indigo-50/40">
                        <div class="flex items-center gap-2 justify-end">
                            <div class="flex-1 h-1.5 bg-indigo-100 rounded-full overflow-hidden w-12">
                                <div class="h-1.5 bg-indigo-500 rounded-full" style="width:{{ $barW }}%"></div>
                            </div>
                            <span class="font-bold text-indigo-700 min-w-[50px] text-right">{{ number_format($desa->jumlah_penduduk ?? 0) }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center font-medium text-slate-600">{{ number_format($desa->jumlah_kk ?? 0) }}</td>
                    <td class="px-4 py-3 text-center font-medium text-blue-500">{{ number_format($desa->jumlah_laki_laki ?? 0) }}</td>
                    <td class="px-4 py-3 text-center font-medium text-pink-500">{{ number_format($desa->jumlah_perempuan ?? 0) }}</td>
                    <td class="px-4 py-3 text-center bg-rose-50/40">
                        @if($stunt > 0)
                            <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-700 px-2 py-0.5 rounded-lg font-bold">{{ $stunt }}</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center bg-teal-50/40">
                        @if($desilSatu > 0)
                            <span class="inline-flex items-center gap-1 bg-teal-100 text-teal-700 px-2 py-0.5 rounded-lg font-bold">{{ number_format($desilSatu) }}</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-slate-400">{{ $desa->luas_wilayah ? number_format($desa->luas_wilayah, 2) : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-800 text-white font-black text-xs">
                    <td colspan="2" class="px-4 py-3 text-left sticky left-0 bg-slate-800">TOTAL / KECAMATAN</td>
                    <td class="px-4 py-3 text-center bg-indigo-700">{{ number_format($totalPenduduk) }}</td>
                    <td class="px-4 py-3 text-center">{{ number_format($totalKk) }}</td>
                    <td class="px-4 py-3 text-center text-blue-300">{{ number_format($totalLaki) }}</td>
                    <td class="px-4 py-3 text-center text-pink-300">{{ number_format($totalPerempuan) }}</td>
                    <td class="px-4 py-3 text-center bg-rose-700">{{ number_format($totStunting) }}</td>
                    <td class="px-4 py-3 text-center bg-teal-700">{{ number_format($d1) }}</td>
                    <td class="px-4 py-3 text-center">{{ number_format($totalLuas, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";

// 1. Gender Donut
new Chart(document.getElementById('chartGender'), {
    type: 'doughnut',
    data: {
        labels: ['Laki-Laki', 'Perempuan'],
        datasets: [{
            data: [{{ $totalLaki }}, {{ $totalPerempuan }}],
            backgroundColor: ['#3b82f6', '#ec4899'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '72%',
        plugins: { legend: { display: false }, tooltip: {
            callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} jiwa` }
        }},
        animation: { animateScale: true }
    }
});

// 2. Pendidikan Horizontal Bar
new Chart(document.getElementById('chartPendidikan'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($topPend)) !!},
        datasets: [{
            label: 'Jumlah',
            data: {!! json_encode(array_values($topPend)) !!},
            backgroundColor: [
                '#6366f1','#8b5cf6','#a78bfa','#34d399','#10b981','#059669','#047857'
            ],
            borderRadius: 10,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.raw.toLocaleString('id-ID')} orang` }}
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } },
            y: { grid: { display: false }, ticks: { color: '#475569', font: { size: 10, weight: 'bold' } } }
        }
    }
});

// 3. Kesehatan Donut
new Chart(document.getElementById('chartKesehatan'), {
    type: 'doughnut',
    data: {
        labels: ['Gizi Normal', 'Stunting', 'Gizi Buruk'],
        datasets: [{
            data: [{{ $totGiziNormal }}, {{ $totStunting }}, {{ $totGiziBuruk }}],
            backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '68%',
        plugins: { legend: { display: false }, tooltip: {
            callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')}` }
        }}
    }
});

// 4. Desil Bar
new Chart(document.getElementById('chartDesil'), {
    type: 'bar',
    data: {
        labels: ['Desil 1', 'Desil 2', 'Desil 3', 'Desil 4', 'Desil 5', 'Desil 6-10'],
        datasets: [{
            label: 'Jumlah KK',
            data: [{{ $d1 }}, {{ $d2 }}, {{ $d3 }}, {{ $d4 }}, {{ $d5 }}, {{ $d610 }}],
            backgroundColor: [
                'rgba(239,68,68,0.85)',
                'rgba(249,115,22,0.85)',
                'rgba(234,179,8,0.85)',
                'rgba(132,204,22,0.85)',
                'rgba(34,197,94,0.85)',
                'rgba(20,184,166,0.85)'
            ],
            borderRadius: 10,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.raw.toLocaleString('id-ID')} KK` } }
        },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', font: { size: 10, weight: 'bold' } } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', font: { size: 10 }, callback: v => v.toLocaleString('id-ID') } }
        }
    }
});
</script>
@endpush
@endsection
