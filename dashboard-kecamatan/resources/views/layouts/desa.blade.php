<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Desa') - {{ appProfile()->app_name }}</title>

    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Google Fonts - Inter (Clean & Modern) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --desa-primary: #16a34a;
            --desa-secondary: #059669;
            --desa-bg: #f8fafc;
            --desa-sidebar: #ffffff;
            --desa-text: #1e293b;
            --desa-text-muted: #64748b;
            --desa-border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--desa-bg);
            color: var(--desa-text);
            font-size: 14px;
        }

        /* Layout Structure */
        .desa-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .desa-sidebar {
            width: 260px;
            background: var(--desa-sidebar);
            border-right: 1px solid var(--desa-border);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .desa-sidebar-header {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--desa-border);
            background: linear-gradient(135deg, var(--desa-primary) 0%, var(--desa-secondary) 100%);
            color: white;
        }

        .desa-sidebar-logo {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .desa-sidebar-subtitle {
            font-size: 11px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .desa-sidebar-nav {
            padding: 1rem 0;
        }

        .desa-nav-section {
            margin-bottom: 1.5rem;
        }

        .desa-nav-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--desa-text-muted);
            padding: 0 1rem;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .desa-nav-menu {
            list-style: none;
            padding: 0;
        }

        .desa-nav-item {
            margin: 0;
        }

        .desa-nav-link {
            display: flex;
            align-items: center;
            padding: 0.65rem 1rem;
            color: var(--desa-text);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .desa-nav-link:hover {
            background: #f1f5f9;
            color: var(--desa-primary);
        }

        .desa-nav-link.active {
            background: #ecfdf5;
            color: var(--desa-primary);
            border-left-color: var(--desa-primary);
            font-weight: 600;
        }

        .desa-nav-icon {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        /* Main Content Area */
        .desa-main {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .desa-topbar {
            background: white;
            border-bottom: 1px solid var(--desa-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .desa-topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .desa-hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--desa-text);
            cursor: pointer;
        }

        .desa-breadcrumb {
            font-size: 13px;
            color: var(--desa-text-muted);
        }

        .desa-topbar-user {
            display: flex;
            align-items: center;
        }

        .desa-dropdown {
            position: relative;
        }

        .desa-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.5rem 0.625rem;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 1px solid transparent;
            user-select: none;
        }

        .desa-dropdown-trigger:hover {
            background: rgba(22, 163, 74, 0.04);
        }

        .desa-dropdown.show .desa-dropdown-trigger {
            background: #ffffff;
            border-color: var(--desa-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .desa-dropdown-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border: 1px solid var(--desa-border);
            border-radius: 12px;
            min-width: 200px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: none;
            overflow: hidden;
            z-index: 1001;
            transform-origin: top right;
            animation: dropdownFadeIn 0.2s ease-out;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .desa-dropdown.show .desa-dropdown-menu {
            display: block;
        }

        .desa-dropdown-item {
            padding: 0.75rem 1rem;
            color: var(--desa-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 13px;
            transition: all 0.15s ease;
        }

        .desa-dropdown-item:hover {
            background: #f8fafc;
            color: var(--desa-primary);
        }

        .desa-dropdown-item i {
            width: 16px;
            font-size: 14px;
            opacity: 0.7;
        }

        .desa-dropdown-item--logout:hover {
            background: #fff1f2;
            color: #dc2626;
        }

        .desa-content {
            flex: 1;
            padding: 1.5rem;
        }

        .desa-footer {
            background: white;
            border-top: 1px solid var(--desa-border);
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 12px;
            color: var(--desa-text-muted);
        }

        /* Flash Messages */
        .desa-flash {
            margin: 1rem 0;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 14px;
            border-left: 4px solid;
        }

        .desa-flash-success {
            background: #ecfdf5;
            border-color: #16a34a;
            color: #14532d;
        }

        .desa-flash-error {
            background: #fff1f2;
            border-color: #dc2626;
            color: #7f1d1d;
        }

        .desa-flash-warning {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #78350f;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .desa-sidebar {
                transform: translateX(-100%);
            }

            .desa-sidebar.show {
                transform: translateX(0);
            }

            .desa-main {
                margin-left: 0;
            }

            .desa-hamburger {
                display: block;
            }

            .desa-user-info {
                display: none;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Badge Colors (Consistent System) */
        .badge-draft {
            background-color: #f1f5f9;
            color: #475569;
        }

        .badge-submitted {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge-returned {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Ticker Animation for Announcements */
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
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Utility classes for ticker/announcements if Tailwind is not present */
        .bg-blue-50\/50 { background-color: rgba(239, 246, 255, 0.5); }
        .border-blue-100 { border-color: #dbeafe !important; }
        .text-blue-500 { color: #3b82f6; }
        .text-slate-600 { color: #475569; }
        .whitespace-nowrap { white-space: nowrap; }
        .tracking-tighter { letter-spacing: -0.05em; }
    </style>

    <!-- Premium Forms CSS -->
    <link rel="stylesheet" href="{{ asset('css/premium-forms.css') }}">

    @stack('styles')
</head>

<body>
    <div class="desa-layout">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar.desa')

        <!-- Main Content -->
        <main class="desa-main">
            <!-- Top Bar -->
            @include('layouts.partials.header.desa')

            <!-- Content Area -->
            <div class="desa-content">
                <!-- Kecamatan Announcements -->
                @if(isset($internalAnnouncements) && $internalAnnouncements->count() > 0)
                    <div class="mb-4">
                        @foreach($internalAnnouncements as $ann)
                            @if($ann->display_mode == 'ticker')
                                <div class="bg-blue-50/50 border border-blue-100 rounded-3 overflow-hidden py-1 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="px-3 border-end border-blue-100 text-[10px] fw-bold text-blue-500 uppercase tracking-tighter">
                                            INFO KECAMATAN
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
                                <div class="alert {{ $ann->priority == 'important' ? 'alert-danger border-0 shadow-sm' : 'alert-primary border-0 shadow-sm' }} d-flex align-items-center py-2 px-3 rounded-4 mb-3">
                                    <i class="fas {{ $ann->priority == 'important' ? 'fa-exclamation-circle' : 'fa-info-circle' }} me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong class="small">{{ $ann->title }}:</strong> 
                                        <span class="small">{{ $ann->content }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="desa-flash desa-flash-success">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="desa-flash desa-flash-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="desa-flash desa-flash-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>{{ session('warning') }}</div>
                    </div>
                @endif

                <!-- Main Content Slot -->
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="desa-footer">
                © {{ date('Y') }} {{ appProfile()->app_name }} - Operator Desa
            </footer>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle for Mobile
        const hamburger = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('desaSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (hamburger) {
            hamburger.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // User Dropdown - Premium Toggle
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown) {
            userDropdown.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            // Close when clicking outside
            document.addEventListener('click', () => {
                userDropdown.classList.remove('show');
            });

            // Prevent closing when clicking inside menu
            const dropdownMenu = userDropdown.querySelector('.desa-dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
        }

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashes = document.querySelectorAll('.desa-flash');
            flashes.forEach(flash => {
                flash.style.transition = 'opacity 0.3s ease';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 300);
            });
        }, 5000);
    </script>

    @stack('scripts')

    {{-- Modal Section - Rendered outside of stacked context --}}
    @yield('modal')
</body>

</html>