@extends('layouts.public')

@section('title')
    @yield('stat_title', 'Data Statistik') | {{ appProfile()->full_region_name }}
@endsection

@section('content')
    <!-- Announcements Integration -->
    @include('layouts.partials.public.announcements')

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 bg-slate-900">
            @if($isHeroActive && $heroImage)
                <img src="{{ asset('storage/' . $heroImage) }}" alt="{{ $heroImageAlt }}"
                    class="w-full h-full object-cover opacity-30">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <div
                class="inline-flex items-center gap-2 bg-rose-500/10 border border-rose-500/20 rounded-full px-4 py-1.5 mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                <span class="text-rose-300 text-[10px] font-bold uppercase tracking-widest">Pusat Data & Statistik</span>
            </div>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 tracking-tight">
                Laporan Demografi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">
                    {{ appProfile()->region_name }}
                </span>
            </h1>

            <p class="text-slate-300 max-w-2xl mx-auto text-lg font-medium leading-relaxed">
                Data kependudukan, rincian statistik, dan profil pembangunan seluruh desa se-kecamatan Besuk.
            </p>
        </div>
    </section>

    <!-- Map Section (Common to Wilayah) -->
    <div id="jelajah" class="py-24 bg-slate-50 overflow-hidden relative">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-teal-50 text-teal-700 px-4 py-2 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Peta Sebaran Desa</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-slate-800 mb-4">Sebaran {{ count($desas) }} Desa</h2>
                <p class="text-slate-500 max-w-2xl mx-auto font-medium leading-relaxed">
                    Klik area desa untuk zoom in. Double-klik untuk mengunjungi portal resmi desa.
                </p>
            </div>

            <div class="relative group mt-8">
                <!-- Map Container -->
                <div id="mapContainer"
                    class="w-full h-[400px] sm:h-[500px] md:h-[650px] rounded-[2rem] md:rounded-[3rem] shadow-xl md:shadow-2xl border-4 md:border-8 border-white overflow-hidden relative z-20">
                </div>

                <!-- Map Legend/Overlay -->
                <div class="absolute bottom-10 left-10 z-30 hidden md:block">
                    <div
                        class="bg-white/90 backdrop-blur-xl p-6 rounded-[2rem] shadow-2xl border border-white max-w-xs">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-sm">Peta Interaktif</h4>
                                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">
                                    {{ appProfile()->region_level }} {{ appProfile()->region_name }}
                                </p>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed mb-4">
                            Warna pada peta menunjukkan batasan wilayah masing-masing desa. Klik area desa untuk info
                            lebih lanjut.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Section -->
    <div id="demografi" class="py-20 bg-white relative overflow-hidden scroll-mt-20">
        <div class="container mx-auto px-6">
            <!-- Navigation Tabs (Server Side) -->
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <a href="{{ route('landing.statistik.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ request()->routeIs('landing.statistik.index') ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fas fa-globe"></i> Umum
                </a>
                <a href="{{ route('landing.statistik.pendidikan') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ request()->routeIs('landing.statistik.pendidikan') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fas fa-graduation-cap"></i> Pendidikan
                </a>
                <a href="{{ route('landing.statistik.pekerjaan') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ request()->routeIs('landing.statistik.pekerjaan') ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fas fa-briefcase"></i> Pekerjaan
                </a>
                <a href="{{ route('landing.statistik.agama') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ request()->routeIs('landing.statistik.agama') ? 'bg-fuchsia-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fas fa-book-quran"></i> Agama
                </a>
                <a href="{{ route('landing.statistik.kesehatan') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ request()->routeIs('landing.statistik.kesehatan') ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fas fa-heart-pulse"></i> Kesehatan
                </a>
                <a href="{{ route('landing.statistik.kesejahteraan') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ request()->routeIs('landing.statistik.kesejahteraan') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fas fa-chart-pie"></i> DTSEN
                </a>
            </div>

            <!-- Header and Content Slot -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 bg-slate-50 text-slate-700 px-4 py-2 rounded-full mb-4 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-table"></i> <span>@yield('stat_badge')</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-slate-800 mb-4">@yield('stat_header')</h2>
                <div class="max-w-3xl mx-auto">
                    @yield('stat_description')
                </div>
            </div>

            @yield('stat_content')
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="{{ asset('css/min/common-map.min.css') }}">
<style>
    .scroll-mt-20 { scroll-margin-top: 5rem; }
    #mapContainer { height: 500px; }

    /* Custom Leaflet Controls */
    .leaflet-control-reset {
        background-color: white;
        width: 34px;
        height: 34px;
        line-height: 34px;
        text-align: center;
        cursor: pointer;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        color: #0f766e;
        font-size: 14px;
        transition: all 0.2s;
    }
    .leaflet-control-reset:hover {
        background-color: #f8fafc;
        color: #0d9488;
        transform: scale(1.05);
    }

    /* Professional Popups */
    .leaflet-popup-content-wrapper {
        border-radius: 1.5rem;
        padding: 5px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    .leaflet-popup-tip {
        display: none;
    }

    /* Permanent Village Labels */
    .village-label-tooltip {
        background: transparent;
        border: none;
        box-shadow: none;
        color: #1e293b;
        font-weight: 800;
        text-shadow: 0 0 4px white, 0 0 4px white, 0 0 4px white;
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof L !== 'undefined') {
            const villageColors = [
                '#0f766e', '#0369a1', '#1d4ed8', '#4338ca', '#6d28d9',
                '#7e22ce', '#a21caf', '#be185d', '#b91c1c', '#c2410c',
                '#b45309', '#a16207', '#4d7c0f', '#15803d', '#166534',
                '#3f6212', '#115e59'
            ];

            // Initialize Map
            const map = L.map('mapContainer', {
                center: [{{ appProfile()->map_latitude ?? -7.78 }}, {{ appProfile()->map_longitude ?? 113.47 }}],
                zoom: 13,
                scrollWheelZoom: false,
                attributionControl: false,
                doubleClickZoom: false,
                zoomControl: false
            });

            L.control.zoom({ position: 'topright' }).addTo(map);

            // --- RESET VIEW CONTROL ---
            let initialBounds = null;
            const ResetControl = L.Control.extend({
                options: { position: 'topright' },
                onAdd: function (map) {
                    const container = L.DomUtil.create('div', 'leaflet-control-reset');
                    container.title = "Reset Zoom & Posisi";
                    container.innerHTML = '<i class="fas fa-sync-alt"></i>';
                    L.DomEvent.on(container, 'click', function (e) {
                        L.DomEvent.stopPropagation(e);
                        if (initialBounds) {
                            map.flyToBounds(initialBounds, { padding: [50, 50], duration: 1.5 });
                        } else {
                            map.flyTo([{{ appProfile()->map_latitude ?? -7.78 }}, {{ appProfile()->map_longitude ?? 113.47 }}], 13, { duration: 1.5 });
                        }
                    });
                    return container;
                }
            });
            map.addControl(new ResetControl());

            // Premium Tiles (CartoDB Voyager)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', { maxZoom: 19, opacity: 0.6 }).addTo(map);

            const geoBaseDir = "/data/geo";
            const activeRegionName = "{{ strtoupper(appProfile()->region_name) }}";

            // Helper to find name in properties
            const getGeoName = (props) => (props.name || props.NAMOBJ || props.village_name || props.NAME || "").toUpperCase();

            // Website Mapping from Database
            const villageUrls = {
                @foreach($desas as $desa)
                    "{{ strtoupper($desa->nama_desa) }}": "{{ $desa->website ?? '#' }}",
                @endforeach
            };

            // Layer 1: Kecamatan Boundary
            fetch(`${geoBaseDir}/layer_kecamatan.geojson`)
                .then(res => res.json())
                .then(data => {
                    const filtered = data.features.filter(f => {
                        const name = getGeoName(f.properties);
                        return name.includes(activeRegionName) || activeRegionName.includes(name);
                    });
                    const renderData = filtered.length > 0 ? { ...data, features: filtered } : data;
                    
                    L.geoJSON(renderData, {
                        style: { color: '#0f766e', weight: 4, opacity: 0.8, dashArray: '1, 10', fill: false, interactive: false }
                    }).addTo(map);

                    if (filtered.length > 0) {
                        initialBounds = L.geoJSON(renderData).getBounds();
                        map.fitBounds(initialBounds, { padding: [50, 50] });
                    }
                });

            // Layer 2: Village Boundaries
            fetch(`${geoBaseDir}/layer_desa.geojson`)
                .then(res => res.json())
                .then(data => {
                    L.geoJSON(data, {
                        style: function (feature) {
                            const idx = data.features.indexOf(feature) % villageColors.length;
                            return { fillColor: villageColors[idx], weight: 2, opacity: 1, color: 'white', fillOpacity: 0.4 };
                        },
                        onEachFeature: function (feature, layer) {
                            const name = getGeoName(feature.properties);
                            // Always use tatadesa.com pattern (lowercase, no spaces)
                            const slug = name.toLowerCase().replace(/\s+/g, '');
                            const url = `https://${slug}.tatadesa.com`;

                            // Permanent Label
                            layer.bindTooltip(`<span class="font-black text-[9px] uppercase tracking-tighter text-slate-700">${name}</span>`, {
                                permanent: true,
                                direction: 'center',
                                className: 'village-label-tooltip'
                            });

                            // Disable any default popup
                            layer.unbindPopup();

                            layer.on({
                                click: (e) => {
                                    // Prevent any popup from opening
                                    e.originalEvent.stopPropagation();
                                    map.flyToBounds(e.target.getBounds(), {
                                        padding: [80, 80],
                                        duration: 1.2
                                    });
                                },
                                dblclick: (e) => {
                                    e.originalEvent.stopPropagation();
                                    window.open(url, '_blank');
                                },
                                mouseover: (e) => {
                                    const l = e.target;
                                    l.setStyle({ fillOpacity: 0.7, weight: 3 });
                                },
                                mouseout: (e) => {
                                    const l = e.target;
                                    l.setStyle({ fillOpacity: 0.4, weight: 2 });
                                }
                            });
                        }
                    }).addTo(map);

                    // Close any existing popups when clicking the map
                    map.on('popupopen', function(e) {
                        map.closePopup();
                    });
                });

            // Marker Sync from Database
            const villageMarkers = L.featureGroup();
            @foreach($desas as $desa)
                @if($desa->latitude && $desa->longitude)
                    L.marker([{{ $desa->latitude }}, {{ $desa->longitude }}], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div style='background-color:#0f766e; width:10px; height:10px; border-radius:50%; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.2);'></div>",
                            iconSize: [10, 10],
                            iconAnchor: [5, 5]
                        })
                    }).addTo(villageMarkers);
                @endif
            @endforeach
            villageMarkers.addTo(map);

            setTimeout(() => map.invalidateSize(), 800);
        }
    });
</script>
@endpush
