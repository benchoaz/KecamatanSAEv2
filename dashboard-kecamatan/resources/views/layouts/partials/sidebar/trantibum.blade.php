<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon bg-transparent text-white">
                @if(appProfile()->logo_path)
                    <img src="{{ asset('storage/' . appProfile()->logo_path) }}" class="img-fluid"
                        style="max-height: 40px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));">
                @else
                    <i class="fas fa-shield-halved"></i>
                @endif
            </div>
            <div class="logo-text">
                <span class="logo-title fw-bold text-uppercase">TRANTIBUM</span>
                <span class="logo-subtitle tracking-wider">{{ strtoupper(appProfile()->full_region_name) }}</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">Dashboard</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.trantibum.index') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.trantibum.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">Dashboard Monitoring</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">Monitoring & Data</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.trantibum.kejadian') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.trantibum.kejadian') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="nav-text">Data Laporan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.trantibum.relawan') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.trantibum.relawan') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                        <span class="nav-text">Relawan Tangguh</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.trantibum.tagana.index') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.trantibum.tagana.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-hard-hat"></i></span>
                        <span class="nav-text">Data TAGANA</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.trantibum.emergency.index') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.trantibum.emergency.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-phone-volume"></i></span>
                        <span class="nav-text">Darurat & Tanggap</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">Laporan</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.trantibum.export') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.trantibum.export') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-file-export"></i></span>
                        <span class="nav-text">Export Data</span>
                    </a>
                </li>
            </ul>
        </div>

        @if(auth()->user()->isSuperAdmin())
            <div class="nav-section">
                <span class="nav-section-title">Pengaturan Sistem</span>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.users.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.users.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-user-gear"></i></span>
                            <span class="nav-text">Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.master.desa.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.master.desa.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-map-location-dot"></i></span>
                            <span class="nav-text">Master Data Desa</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.audit-logs.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.audit-logs.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                            <span class="nav-text">Log Aktivitas</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-card border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="user-avatar text-white me-3">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="user-name text-truncate text-white d-block">{{ auth()->user()->nama_lengkap }}</span>
                    <span class="user-role text-uppercase">{{ optional(auth()->user()->role)->nama_role }}</span>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-sage w-100 d-flex align-items-center justify-content-center gap-2 py-2"
                onclick="return confirm('Konfirmasi Keluar\n\nApakah Anda yakin ingin keluar dari aplikasi?')">
                <i class="fas fa-power-off"></i>
                <span>Keluar Aplikasi</span>
            </button>
        </form>
    </div>
</aside>