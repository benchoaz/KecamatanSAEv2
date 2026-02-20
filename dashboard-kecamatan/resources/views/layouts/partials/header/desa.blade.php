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
        <div class="desa-user-info">
            <span class="desa-user-name">{{ auth()->user()->nama_lengkap }}</span>
            <span class="desa-user-desa">Operator Desa</span>
        </div>
        <div class="desa-dropdown" id="userDropdown">
            <div class="desa-user-avatar" style="cursor: pointer;">
                {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 2)) }}
            </div>
            <div class="desa-dropdown-menu">
                <a href="{{ route('desa.profile.index') }}" class="desa-dropdown-item">
                    <i class="fas fa-user"></i> Profil Saya
                </a>
                <a href="{{ route('logout') }}" class="desa-dropdown-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</header>