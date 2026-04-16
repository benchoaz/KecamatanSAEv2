<aside class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                @php $profile = \App\Models\AppProfile::first(); @endphp
                @if($profile && $profile->logo_path)
                    <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo" class="img-fluid"
                        style="max-height: 52px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));">
                @else
                    <i class="fas fa-landmark"></i>
                @endif
            </div>
            <div class="logo-text">
                <span class="logo-title">Dashboard</span>
                <span class="logo-subtitle">Kecamatan</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <div class="nav-section">
            <span class="nav-section-title">
                @auth
                    Menu Utama
                @else
                    Portal Publik
                @endauth
            </span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                @canany(['submission.verify', 'submission.approve'])
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.verifikasi.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.verifikasi.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-clipboard-check"></i></span>
                            <span class="nav-text">Verifikasi</span>
                            @php
                                $pendingCount = \App\Models\Submission::whereIn('status', [
                                    \App\Models\Submission::STATUS_SUBMITTED,
                                    \App\Models\Submission::STATUS_REVIEWED
                                ])->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="nav-badge">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endcanany
            </ul>
        </div>

        <!-- Ekonomi & Kreatif -->
        <div class="nav-section">
            <span class="nav-section-title">Ekonomi & Kreatif</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('umkm_rakyat.index') }}"
                        class="nav-link {{ request()->routeIs('umkm_rakyat.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-store"></i></span>
                        <span class="nav-text">Portal UMKM</span>
                    </a>
                </li>
                @auth
                    @if(auth()->user()->isOperatorKecamatan() || auth()->user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.umkm.index') }}"
                                class="nav-link {{ request()->is('kecamatan/layanan/umkm*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-tasks-alt"></i></span>
                                <span class="nav-text">Kelola UMKM</span>
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>

        <!-- Domain Menus -->
        @auth
            @if(auth()->user()->isOperatorKecamatan())
                <div class="nav-section">
                    <span class="nav-section-title">Bidang Kesra (Pasal 439)</span>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.index') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                                <span class="nav-text">Dashboard Kesra</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.stunting.analysis') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra/stunting*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-shield-virus text-emerald-500"></i></span>
                                <span class="nav-text text-emerald-600 font-medium">Analisis Stunting</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.bansos.index') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra/bansos*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-hand-holding-heart"></i></span>
                                <span class="nav-text">Verifikasi Bansos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.pendidikan.index') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra/pendidikan*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-graduation-cap"></i></span>
                                <span class="nav-text">Monitoring Pendidikan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.kesehatan.index') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra/kesehatan*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-heartbeat"></i></span>
                                <span class="nav-text">Monitoring Kesehatan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.sosial-budaya.index') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra/sosial-budaya*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-praying-hands"></i></span>
                                <span class="nav-text">Sosial & Budaya</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.kesra.rekomendasi.index') }}"
                                class="nav-link {{ request()->is('kecamatan/kesra/rekomendasi*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-check-double"></i></span>
                                <span class="nav-text">Rekomendasi Camat</span>
                            </a>
                        </li>
                    </ul>
                </div>
            @else
                @canany(['dashboard.view_desa', 'dashboard.view_kecamatan'])
                    <div class="nav-section">
                        <span class="nav-section-title">Seksi</span>
                        <ul class="nav-menu">

                            {{-- MODE DESA: Link langsung ke Administrasi --}}
                            @if(auth()->user()->desa_id)
                                <li class="nav-item">
                                    <a href="{{ route('desa.administrasi.index') }}"
                                        class="nav-link {{ request()->routeIs('desa.administrasi.*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-folder-open"></i></span>
                                        <span class="nav-text">Administrasi Desa</span>
                                    </a>
                                </li>

                                {{-- MODE KECAMATAN: Dropdown Menu --}}
                            @else
                                <!-- Pemerintahan -->
                                <li class="nav-item has-submenu {{ request()->is('pemerintahan*') ? 'open' : '' }}">
                                    <a href="#" class="nav-link {{ request()->is('pemerintahan*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-building-columns"></i></span>
                                        <span class="nav-text">Administrasi Desa</span>
                                        <span class="nav-arrow"><i class="fas fa-chevron-right"></i></span>
                                    </a>
                                    <ul class="nav-submenu">
                                        <li><a href="{{ route('kecamatan.pemerintahan.index') }}" class="nav-sublink">Buku Induk
                                                Desa</a></li>
                                    </ul>
                                </li>
                            @endif


                            <!-- Ekonomi & Pembangunan (Kecamatan Domain Check) -->
                            @if(auth()->user()->isOperatorKecamatan() || auth()->user()->isSuperAdmin())
                                <li class="nav-item {{ request()->is('ekbang*') ? 'open' : '' }}">
                                    <a href="{{ route('kecamatan.ekbang.index') }}"
                                        class="nav-link {{ request()->is('kecamatan/ekbang*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-hand-holding-dollar"></i></span>
                                        <span class="nav-text">Ekonomi & Pembangunan (Monitoring)</span>
                                    </a>
                                </li>
                            @endif

                            @can('musrenbang.create')
                                <li class="nav-item">
                                    <a href="{{ route('kecamatan.pemerintahan.perencanaan.index') }}"
                                        class="nav-link {{ request()->routeIs('kecamatan.pemerintahan.perencanaan.*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                                        <span class="nav-text">Perencanaan</span>
                                    </a>
                                </li>
                            @endcan

                            @can('submission.create')
                                <li class="nav-item">
                                    <a href="{{ route('desa.submissions.create') }}"
                                        class="nav-link {{ request()->routeIs('desa.submissions.*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-file-export"></i></span>
                                        <span class="nav-text">Laporan Baru</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                @endcanany
            @endif
        @endauth

        @auth
            <!-- Analytics / History -->
            <div class="nav-section">
                <span class="nav-section-title">Monitoring</span>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('desa.submissions.index') }}"
                            class="nav-link {{ request()->routeIs('desa.submissions.index') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                            <span class="nav-text">Riwayat & Status</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endauth

        @auth
            <!-- Settings (Admin & Kecamatan) -->
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isOperatorKecamatan())
                <div class="nav-section">
                    <span class="nav-section-title">Pengaturan</span>
                    <ul class="nav-menu">
                        @if(auth()->user()->isSuperAdmin())
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                                    <span class="nav-text">Pengaturan Sistem</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.users.index') }}"
                                class="nav-link {{ request()->routeIs('kecamatan.users.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                                <span class="nav-text">Manajemen User</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.master.desa.index') }}"
                                class="nav-link {{ request()->routeIs('kecamatan.master.desa.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-map-marked-alt"></i></span>
                                <span class="nav-text">Master Data Desa</span>
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        @endauth
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        @auth
            <div class="user-card">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->nama_lengkap ?? 'Administrator' }}</span>
                    <span class="user-role">{{ optional(auth()->user()->role)->nama_role ?? 'Sistem' }}</span>
                </div>
                <a href="#" class="user-logout" title="Logout"
                    onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @else
            <div class="user-card">
                <div class="user-avatar bg-slate-100 text-slate-400">
                    <i class="fas fa-user-secret"></i>
                </div>
                <div class="user-info">
                    <span class="user-name">Tamu</span>
                    <span class="user-role">Public User</span>
                </div>
                <a href="{{ route('login') }}" class="user-logout text-teal-600" title="Login">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            </div>
        @endauth
    </div>
</aside>