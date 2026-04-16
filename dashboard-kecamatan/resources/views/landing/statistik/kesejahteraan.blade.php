@extends('landing.statistik.layout')

@section('stat_title', 'Statistik Kesejahteraan')
@section('stat_badge', 'Data DTSEN')
@section('stat_header', 'Kesejahteraan Keluarga (DTSEN)')
@section('stat_description')
    Laporan rincian tingkat kesejahteraan keluarga berdasarkan data DTSEN yang dihimpun dari 17 desa di wilayah {{ appProfile()->full_region_name }}.
@endsection

@section('stat_content')
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="font-black text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line"></i>
                </div>
                Analisis Tren Desil Kesejahteraan (DTSEN)
            </h3>
            <div class="bg-slate-50 border border-slate-100 px-4 py-2 rounded-xl text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                Update Otomatis: {{ now()->translatedFormat('d F Y') }}
            </div>
        </div>

        <div class="relative overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/30">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-[10px] sticky left-0 z-10 bg-slate-800">Nama Desa</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Tahun Terakhir</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] bg-blue-600">Desil 1 (n)</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] bg-blue-700">Desil 1 (n-1)</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Tren</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-[10px] bg-slate-700 text-white">Total KK DTSEN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($desas as $desa)
                    @php
                        $jsonRaw = is_string($desa->stat_desil) ? $desa->stat_desil : json_encode($desa->stat_desil ?? []);
                        $stats = json_decode($jsonRaw, true);
                        $trend = $stats['yearlyTrend'] ?? [];
                        
                        // Sort trend by year desc
                        usort($trend, function($a, $b) { return $b['tahun'] - $a['tahun']; });
                        
                        $yearNow = $trend[0] ?? null;
                        $yearPrev = $trend[1] ?? null;
                        
                        $d1Now = $yearNow['desil1'] ?? ($stats['totalDesil1'] ?? 0);
                        $d1Prev = $yearPrev['desil1'] ?? 0;
                        
                        $delta = $d1Prev > 0 ? $d1Now - $d1Prev : 0;
                        $percent = $d1Prev > 0 ? ($delta / $d1Prev) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-blue-50/50 transition-colors group bg-white">
                        <td class="px-6 py-4 font-bold text-slate-700 sticky left-0 z-10 bg-white group-hover:bg-blue-50/50 border-r border-slate-100">
                            <button onclick="showDesilDetail('{{ addslashes($desa->nama_desa) }}', {{ $jsonRaw }})" 
                                    class="text-left hover:text-blue-600 transition-colors flex items-center gap-2 group/btn">
                                <span>{{ $desa->nama_desa }}</span>
                                <i class="fas fa-external-link-alt text-[8px] opacity-0 group-hover/btn:opacity-100 transition-opacity"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-slate-500 bg-slate-50/50">
                            {{ $yearNow['tahun'] ?? ($stats['latestTahun'] ?? '-') }}
                        </td>
                        <td class="px-6 py-4 text-center font-black text-blue-600 bg-blue-50">
                            {{ number_format($d1Now) }}
                        </td>
                        <td class="px-6 py-4 text-center font-medium text-slate-400">
                            {{ $d1Prev > 0 ? number_format($d1Prev) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($d1Prev > 0)
                                @if($delta < 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold">
                                        <i class="fas fa-arrow-down"></i> {{ number_format(abs($percent), 1) }}%
                                    </span>
                                @elseif($delta > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-rose-50 text-rose-600 text-[10px] font-bold">
                                        <i class="fas fa-arrow-up"></i> {{ number_format($percent, 1) }}%
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            @else
                                <span class="text-slate-300 italic text-[9px]">Data awal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-black text-slate-700 bg-slate-50/30">
                             {{ number_format($yearNow['total'] ?? ($stats['grandTotal'] ?? 0)) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 text-[10px] text-blue-800 leading-relaxed">
                <i class="fas fa-info-circle mr-1"></i> <strong>Definisi:</strong> Desil 1 adalah kelompok rumah tangga dengan tingkat kesejahteraan terendah (10% terbawah).
            </div>
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 text-[10px] text-amber-800 leading-relaxed">
                <i class="fas fa-chart-line mr-1"></i> <strong>Analisis Tren:</strong> Klik nama desa untuk melihat rincian Desil 1-10 secara mendalam.
            </div>
        </div>
    </div>

    <!-- Modal Rincian Desil -->
    <div id="desilModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeDesilModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden pointer-events-auto flex flex-col scale-95 opacity-0 transition-all duration-300 transform" id="modalContainer">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h4 class="text-xl font-black text-slate-800" id="modalVillageName">Rincian Desa</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Struktur Kesejahteraan (Desil 1-10)</p>
                    </div>
                    <button onclick="closeDesilModal()" class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-100 hover:bg-rose-50 transition-all shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-8 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Summary Cards -->
                        <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-xl shadow-blue-200">
                            <div class="text-[10px] font-bold uppercase tracking-wider opacity-80 mb-1">Total Keluarga (DTSEN)</div>
                            <div class="text-4xl font-black mb-1" id="modalTotalKk">0</div>
                            <div class="text-[10px] font-medium opacity-70">Kepala Keluarga Terdata</div>
                        </div>
                        <div class="bg-slate-800 rounded-3xl p-6 text-white shadow-xl shadow-slate-200">
                            <div class="text-[10px] font-bold uppercase tracking-wider opacity-80 mb-1">Penerima Manfaat (KPM)</div>
                            <div class="text-4xl font-black mb-1" id="modalTotalKpm">0</div>
                            <div class="text-[10px] font-medium opacity-70">Terintegrasi Program Sosial</div>
                        </div>
                    </div>

                    <!-- Desil Grid -->
                    <h5 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span>
                        Komposisi Desil Kesejahteraan
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8" id="desilGrid">
                        <!-- Will be populated by JS -->
                    </div>

                    <!-- Trend Table -->
                    <h5 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-teal-500 rounded-full"></span>
                        Riwayat Perubahan Tahunan
                    </h5>
                    <div class="rounded-2xl border border-slate-100 overflow-hidden bg-white shadow-sm">
                        <table class="w-full text-[11px]">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-4 py-3 text-left font-bold text-slate-500">Tahun</th>
                                    <th class="px-4 py-3 text-center font-bold text-slate-500">Desil 1</th>
                                    <th class="px-4 py-3 text-center font-bold text-slate-500">Total KK</th>
                                    <th class="px-4 py-3 text-center font-bold text-slate-500">KPM</th>
                                </tr>
                            </thead>
                            <tbody id="modalTrendBody">
                                <!-- Will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDesilDetail(name, data) {
            const modal = document.getElementById('desilModal');
            const container = document.getElementById('modalContainer');
            
            // Populate basic info
            document.getElementById('modalVillageName').innerText = 'Desa ' + name;
            document.getElementById('modalTotalKk').innerText = (data.grandTotal || 0).toLocaleString('id-ID');
            document.getElementById('modalTotalKpm').innerText = (data.totalKpm || 0).toLocaleString('id-ID');

            // Populate Desil Grid
            const grid = document.getElementById('desilGrid');
            const desils = [
                { label: 'Desil 1', val: data.totalDesil1 || 0, color: 'bg-blue-50 text-blue-700 border-blue-100' },
                { label: 'Desil 2', val: data.totalDesil2 || 0, color: 'bg-slate-50 text-slate-700 border-slate-100' },
                { label: 'Desil 3', val: data.totalDesil3 || 0, color: 'bg-slate-50 text-slate-700 border-slate-100' },
                { label: 'Desil 4', val: data.totalDesil4 || 0, color: 'bg-slate-50 text-slate-700 border-slate-100' },
                { label: 'Desil 5', val: data.totalDesil5 || 0, color: 'bg-slate-50 text-slate-700 border-slate-100' },
                { label: 'Desil 6-10', val: data.totalDesil6_10 || 0, color: 'bg-slate-50 text-slate-700 border-slate-100' }
            ];

            grid.innerHTML = desils.map(d => `
                <div class="p-4 rounded-2xl border ${d.color} flex flex-col items-center justify-center text-center">
                    <span class="text-[8px] font-black uppercase tracking-widest opacity-60 mb-1">${d.label}</span>
                    <span class="text-xl font-black">${d.val.toLocaleString('id-ID')}</span>
                </div>
            `).join('');

            // Populate Trend Table
            const trendBody = document.getElementById('modalTrendBody');
            const trends = data.yearlyTrend || [];
            
            // Sort trends by year desc
            trends.sort((a, b) => b.tahun - a.tahun);

            if (trends.length > 0) {
                trendBody.innerHTML = trends.map(t => `
                    <tr class="border-b border-slate-50">
                        <td class="px-4 py-3 font-bold text-slate-700">${t.tahun}</td>
                        <td class="px-4 py-3 text-center font-bold text-blue-600">${(t.desil1 || 0).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-3 text-center text-slate-500">${(t.total || 0).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-3 text-center text-slate-500">${(t.jumlahKpm || 0).toLocaleString('id-ID')}</td>
                    </tr>
                `).join('');
            } else {
                trendBody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">Data historis belum tersedia</td></tr>';
            }

            // Show animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeDesilModal() {
            const modal = document.getElementById('desilModal');
            const container = document.getElementById('modalContainer');
            
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close on ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") closeDesilModal();
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
@endsection
