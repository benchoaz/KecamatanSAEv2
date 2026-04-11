<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon bg-transparent text-white">
                @if(appProfile()->logo_path)
                    <img src="{{ asset('storage/' . appProfile()->logo_path) }}" class="img-fluid"
                        style="max-height: 48px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));">
                @else
                    <i class="fas fa-store"></i>
                @endif
            </div>
            <div class="logo-text">
                <span class="logo-title fw-bold text-uppercase">PORTAL EKONOMI</span>
                <span class="logo-subtitle tracking-wider">{{ strtoupper(appProfile()->full_region_name) }}</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">DASHBOARD EKONOMI</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.index') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.umkm.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">Dashboard Monitoring</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">KELOLA USAHA / JASA</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.index') }}?filter=pending"
                        class="nav-link {{ request()->query('filter') == 'pending' ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-clock"></i></span>
                        <span class="nav-text">Pendaftaran Baru</span>
                        @if(isset($pendingUmkmCount) && $pendingUmkmCount > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingUmkmCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.index') }}?filter=aktif"
                        class="nav-link {{ request()->query('filter') == 'aktif' ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-store"></i></span>
                        <span class="nav-text">UMKM Aktif</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.index') }}?filter=all"
                        class="nav-link {{ request()->query('filter') == 'all' ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-list"></i></span>
                        <span class="nav-text">Semua UMKM</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">BANTUAN & DUKUNGAN</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.bantuan') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.umkm.bantuan') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-hand-holding-heart"></i></span>
                        <span class="nav-text">Program Bantuan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.handover') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.umkm.handover') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-gift"></i></span>
                        <span class="nav-text">Serah Terima</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">LAPORAN</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.export') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.umkm.export') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-file-export"></i></span>
                        <span class="nav-text">Export Data</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.statistics') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.umkm.statistics') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                        <span class="nav-text">Statistik UMKM</span>
                    </a>
                </li>
            </ul>
        </div>

        @if(auth()->user()->isSuperAdmin())
            <div class="nav-section">
                <span class="nav-section-title">PENGATURAN SISTEM</span>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.users.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.users.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-user-gear"></i></span>
                            <span class="nav-text">Manajemen User UMKM</span>
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
        <div class="user-card border-0 shadow-sm" style="background: rgba(255,255,255,0.03);">
            <div class="user-avatar bg-warning text-white"><i class="fas fa-store"></i></div>
            <div class="user-info">
                <span class="user-name text-truncate text-white">{{ auth()->user()->nama_lengkap }}</span>
                <span
                    class="user-role small text-muted text-uppercase tracking-tighter">{{ optional(auth()->user()->role)->nama_role }}</span>
            </div>
        </div>

        <!-- Logout Button -->
        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit"
                class="btn btn-warning btn-sm w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm"
                onclick="return confirm('Konfirmasi Keluar\n\nApakah Anda yakin ingin keluar dari aplikasi?')"
                style="font-size: 13px;">
                <i class="fas fa-power-off"></i>
                <span>Keluar Aplikasi</span>
            </button>
        </form>
    </div>
</aside>