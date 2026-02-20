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
            gap: 0.75rem;
        }

        .desa-user-info {
            text-align: right;
        }

        .desa-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--desa-text);
            display: block;
        }

        .desa-user-desa {
            font-size: 11px;
            color: var(--desa-text-muted);
        }

        .desa-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--desa-primary), var(--desa-secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .desa-dropdown {
            position: relative;
        }

        .desa-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border: 1px solid var(--desa-border);
            border-radius: 8px;
            min-width: 180px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .desa-dropdown.show .desa-dropdown-menu {
            display: block;
        }

        .desa-dropdown-item {
            padding: 0.5rem 1rem;
            color: var(--desa-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
        }

        .desa-dropdown-item:hover {
            background: var(--desa-bg);
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

        // User Dropdown
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown) {
            userDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            document.addEventListener('click', () => {
                userDropdown.classList.remove('show');
            });
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