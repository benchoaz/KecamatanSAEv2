<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Loker') - {{ appProfile()->region_name }}</title>
    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/min/dashboard.min.css?v=2.1') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-premium.css') }}">
    
    <!-- Loker Module Specific Styles -->
    <style>
        /* Loker Brand Colors */
        :root {
            --loker-primary: #3b82f6;
            --loker-secondary: #2563eb;
            --loker-accent: #60a5fa;
            --loker-light: #eff6ff;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%) !important;
        }
        
        .sidebar-header .logo-icon {
            background: rgba(255, 255, 255, 0.1) !important;
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15) !important;
            border-left-color: #bfdbfe !important;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1) !important;
        }
        
        .ticker-move-internal {
            display: inline-block;
            white-space: nowrap;
            padding-right: 100%;
            animation: ticker 30s linear infinite;
        }
        .hover\:pause-animation:hover { animation-play-state: paused; }
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
        .alert-emerald {
            background-color: var(--success-50);
            border: 1px solid rgba(22, 163, 74, 0.1) !important;
        }
        .icon-box-emerald {
            background-color: var(--success-500);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.2);
        }
        .text-emerald-900 { color: #064e3b; }
        .text-emerald-700 { color: #047857; }
        .alert-danger {
            background-color: var(--danger-50);
            border: 1px solid rgba(239, 68, 68, 0.1) !important;
        }
        .icon-box-danger {
            background-color: var(--danger-500);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }
        
        /* Loker Badge */
        .loker-badge {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Loker (Isolated) -->
        @include('layouts.partials.sidebar.loker')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            @include('layouts.partials.header')

            <!-- Page Content -->
            <div class="page-content">
                @if(isset($moduleAnnouncements) && $moduleAnnouncements->count() > 0)
                    <div class="px-4 mt-3">
                        @foreach($moduleAnnouncements as $ann)
                            @if($ann->display_mode == 'ticker')
                                <div class="bg-blue-50/50 border border-blue-100 rounded-3 overflow-hidden py-1 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="px-3 border-end border-blue-100 text-[10px] fw-bold text-blue-500 uppercase tracking-tighter">
                                            INFO LOKER
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden whitespace-nowrap">
                                            <div class="ticker-move-internal hover:pause-animation">
                                                <span class="text-slate-600 small fw-medium px-3">
                                                    {{ $ann->content }} &nbsp;&bull;&nbsp; {{ $ann->title }}
                                                </span>
                                                <span class="text-slate-600 small fw-medium px-3">
                                                    {{ $ann->content }} &nbsp;&bull;&nbsp; {{ $ann->title }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert {{ $ann->priority == 'important' ? 'alert-primary border-0 shadow-sm' : 'alert-info border-0 shadow-sm' }} d-flex align-items-center py-2 px-3 rounded-4 mb-3">
                                    <i class="fas {{ $ann->priority == 'important' ? 'fa-bullhorn' : 'fa-info-circle' }} me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong class="small">{{ $ann->title }}:</strong> 
                                        <span class="small">{{ $ann->content }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mt-4 mx-4">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js?v=2.1') }}"></script>
    @stack('scripts')

    {{-- Modal Section - Rendered outside of stacked context --}}
    @yield('modal')
</body>

</html>
