<!-- Top Bar -->
<header class="desa-topbar">
    <div class="desa-topbar-left">
        <button class="desa-hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="desa-breadcrumb">
            @yield('breadcrumb', 'Dashboard')
        </div>
    </div>

    <div class="desa-topbar-user">
        <div class="desa-dropdown" id="userDropdown">
            <div class="desa-dropdown-trigger">
                <div class="desa-user-info d-none d-sm-block text-end me-2" style="line-height: 1.2;">
                    <span class="desa-user-name fw-bold d-block" style="font-size: 13px; color: var(--desa-text);">{{ auth()->user()->nama_lengkap }}</span>
                    <span class="desa-user-desa d-block" style="font-size: 11px; color: var(--desa-text-muted);">Operator Desa</span>
                </div>
                <div class="desa-user-avatar" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--desa-primary), var(--desa-secondary)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 2)) }}
                </div>
                <i class="fas fa-chevron-down ms-1 small text-muted"></i>
            </div>
            <div class="desa-dropdown-menu shadow-lg border-0">
                <div class="px-3 py-2 border-bottom bg-light d-sm-none">
                    <span class="d-block fw-bold small">{{ auth()->user()->nama_lengkap }}</span>
                    <span class="d-block text-muted" style="font-size: 10px;">Operator Desa</span>
                </div>
                <a href="{{ route('desa.profile.index') }}" class="desa-dropdown-item">
                    <i class="fas fa-user-circle text-primary"></i> Profil Saya
                </a>
                <a href="{{ route('desa.profile.index') }}#password-section" class="desa-dropdown-item">
                    <i class="fas fa-lock text-info"></i> Ubah Password
                </a>
                <hr class="dropdown-divider my-1 opacity-50">
                <a href="{{ route('logout') }}" class="desa-dropdown-item desa-dropdown-item--logout text-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-power-off"></i> Keluar
                </a>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</header>