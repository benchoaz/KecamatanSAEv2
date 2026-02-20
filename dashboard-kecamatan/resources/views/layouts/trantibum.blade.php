<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Trantibum') - {{ appProfile()->region_name }}</title>
    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
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
    
    <style>
        /* Sage Green Theme - Modern & Clean */
        :root {
            --sage-50: #f6f7f6;
            --sage-100: #e3e7e3;
            --sage-200: #c7d0c7;
            --sage-300: #a3b1a3;
            --sage-400: #7d8f7d;
            --sage-500: #5f735f;
            --sage-600: #4b5c4b;
            --sage-700: #3d4a3d;
            --sage-800: #333d33;
            --sage-900: #2b342b;
            --sage-950: #151b15;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8faf8 0%, #f0f4f0 100%);
        }
        
        /* Modern Sidebar with Sage Green */
        .sidebar {
            background: linear-gradient(180deg, var(--sage-800) 0%, var(--sage-950) 100%) !important;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 100%);
        }
        
        .sidebar-header .logo-icon {
            background: linear-gradient(135deg, var(--sage-400) 0%, var(--sage-600) 100%) !important;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }
        
        .logo-title {
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            background: linear-gradient(135deg, #fff 0%, var(--sage-200) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-subtitle {
            color: var(--sage-300) !important;
            font-size: 0.65rem;
            letter-spacing: 0.05em;
        }
        
        /* Navigation Styling */
        .nav-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: var(--sage-400);
            padding: 1rem 1.25rem 0.5rem;
            text-transform: uppercase;
        }
        
        .nav-link {
            padding: 0.75rem 1.25rem;
            margin: 0.15rem 0.75rem;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(4px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, rgba(163, 177, 163, 0.2) 0%, rgba(163, 177, 163, 0.1) 100%);
            color: #fff;
            border-left-color: var(--sage-400) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .nav-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            margin-right: 0.75rem;
            font-size: 0.9rem;
        }
        
        .nav-link.active .nav-icon {
            background: linear-gradient(135deg, var(--sage-400) 0%, var(--sage-500) 100%);
            box-shadow: 0 4px 12px rgba(95, 115, 95, 0.3);
        }
        
        /* Sidebar Footer */
        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
        }
        
        .user-card {
            background: rgba(255, 255, 255, 0.05) !important;
            border-radius: 16px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--sage-400) 0%, var(--sage-600) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .user-role {
            font-size: 0.7rem;
            color: var(--sage-300) !important;
        }
        
        /* Modern Button */
        .btn-sage {
            background: linear-gradient(135deg, var(--sage-500) 0%, var(--sage-700) 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
        }
        
        .btn-sage:hover {
            background: linear-gradient(135deg, var(--sage-600) 0%, var(--sage-800) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(95, 115, 95, 0.3);
            color: white;
        }
        
        /* Main Content Area */
        .main-content {
            background: transparent;
        }
        
        .page-content {
            padding: 1.5rem;
        }
        
        /* Modern Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        /* Announcement Ticker */
        .ticker-move-internal {
            display: inline-block;
            white-space: nowrap;
            padding-right: 100%;
            animation: ticker 30s linear infinite;
        }
        
        .hover\:pause-animation:hover { 
            animation-play-state: paused; 
        }
        
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
        
        /* Sage Announcement Banner */
        .announcement-banner {
            background: linear-gradient(135deg, rgba(163, 177, 163, 0.15) 0%, rgba(195, 208, 195, 0.1) 100%);
            border: 1px solid rgba(163, 177, 163, 0.3);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .announcement-banner .badge-section {
            background: linear-gradient(135deg, var(--sage-500) 0%, var(--sage-700) 100%);
            color: white;
            font-weight: 700;
            font-size: 0.65rem;
            letter-spacing: 0.1em;
        }
        
        /* Alert Styling */
        .alert-sage {
            background: linear-gradient(135deg, rgba(163, 177, 163, 0.15) 0%, rgba(195, 208, 195, 0.1) 100%);
            border: 1px solid rgba(163, 177, 163, 0.3);
            border-radius: 12px;
        }
        
        /* Badge */
        .sage-badge {
            background: linear-gradient(135deg, var(--sage-500) 0%, var(--sage-700) 100%);
            color: white;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--sage-100);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--sage-400);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--sage-500);
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Trantibum (Isolated) -->
        @include('layouts.partials.sidebar.trantibum')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            @include('layouts.partials.header')

            <!-- Page Content -->
            <div class="page-content">
                @if(isset($moduleAnnouncements) && $moduleAnnouncements->count() > 0)
                    <div class="mb-4">
                        @foreach($moduleAnnouncements as $ann)
                            @if($ann->display_mode == 'ticker')
                                <div class="announcement-banner mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="badge-section px-3 py-2">
                                            <i class="fas fa-shield-halved me-1"></i> INFO TRANTIBUM
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden py-2 px-3">
                                            <div class="ticker-move-internal hover:pause-animation">
                                                <span class="text-slate-700 small fw-medium">
                                                    {{ $ann->content }} &nbsp;&bull;&nbsp; {{ $ann->title }}
                                                </span>
                                                <span class="text-slate-700 small fw-medium ms-4">
                                                    {{ $ann->content }} &nbsp;&bull;&nbsp; {{ $ann->title }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert {{ $ann->priority == 'important' ? 'alert-sage' : 'alert-sage' }} d-flex align-items-center py-3 px-4 rounded-4 mb-3">
                                    <div class="me-3">
                                        <div class="nav-icon bg-gradient" style="background: linear-gradient(135deg, var(--sage-400) 0%, var(--sage-600) 100%);">
                                            <i class="fas {{ $ann->priority == 'important' ? 'fa-exclamation-triangle' : 'fa-info-circle' }} text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong class="small fw-semibold">{{ $ann->title }}:</strong> 
                                        <span class="small text-slate-600">{{ $ann->content }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mb-4 rounded-4 border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="nav-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            </div>
                            <span class="fw-medium">{{ session('success') }}</span>
                        </div>
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
