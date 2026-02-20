<!-- Sidebar -->
<aside class="desa-sidebar" id="desaSidebar">
    <div class="desa-sidebar-header">
        <div class="desa-sidebar-logo">
            {{ auth()->user()->desa->nama_desa ?? 'Operator Desa' }}
        </div>
        <div class="desa-sidebar-subtitle">{{ appProfile()->region_name }}</div>
    </div>

    <nav class="desa-sidebar-nav">
        <div class="desa-nav-section">
            <div class="desa-nav-title">Menu Utama</div>
            <ul class="desa-nav-menu">
                <li class="desa-nav-item">
                    <a href="{{ route('desa.dashboard') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.dashboard') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="desa-nav-section">
            <div class="desa-nav-title">Administrasi</div>
            <ul class="desa-nav-menu">
                <li class="desa-nav-item">
                    <a href="{{ route('desa.administrasi.index') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.administrasi.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-folder-open"></i>
                        <span>Administrasi Desa</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="desa-nav-section">
            <div class="desa-nav-title">Perencanaan</div>
            <ul class="desa-nav-menu">
                <li class="desa-nav-item">
                    <a href="{{ route('desa.musdes.index') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.musdes.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-gavel"></i>
                        <span>Musyawarah Desa</span>
                    </a>
                </li>
                <li class="desa-nav-item">
                    <a href="{{ route('desa.pemerintahan.detail.perencanaan.index') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.pemerintahan.detail.perencanaan.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-file-invoice"></i>
                        <span>Dokumen Perencanaan</span>
                    </a>
                </li>
            </ul>
        </div>

        @php
            $ekbangMenu = \App\Models\Menu::where('kode_menu', 'ekbang')->first();
        @endphp

        @if($ekbangMenu && $ekbangMenu->is_active)
            <div class="desa-nav-section">
                <div class="desa-nav-title">Pembangunan & BLT</div>
                <ul class="desa-nav-menu">
                    <li class="desa-nav-item">
                        <a href="{{ route('desa.pembangunan.pagu.index') }}"
                            class="desa-nav-link {{ request()->routeIs('desa.pembangunan.pagu.*') ? 'active' : '' }}">
                            <i class="desa-nav-icon fas fa-coins text-warning"></i>
                            <span>Anggaran Desa</span>
                        </a>
                    </li>
                    <li class="desa-nav-item">
                        <a href="{{ route('desa.pembangunan.fisik.index') }}"
                            class="desa-nav-link {{ request()->routeIs('desa.pembangunan.fisik.*') ? 'active' : '' }}">
                            <i class="desa-nav-icon fas fa-trowel-bricks"></i>
                            <span>Pembangunan Fisik</span>
                        </a>
                    </li>
                    <li class="desa-nav-item">
                        <a href="{{ route('desa.pembangunan.non-fisik.index') }}"
                            class="desa-nav-link {{ request()->routeIs('desa.pembangunan.non-fisik.*') ? 'active' : '' }}">
                            <i class="desa-nav-icon fas fa-users-gear"></i>
                            <span>Kegiatan Non-Fisik</span>
                        </a>
                    </li>
                    <li class="desa-nav-item">
                        <a href="{{ route('desa.blt.index') }}"
                            class="desa-nav-link {{ request()->routeIs('desa.blt.*') ? 'active' : '' }}">
                            <i class="desa-nav-icon fas fa-hand-holding-dollar"></i>
                            <span>BLT Desa</span>
                        </a>
                    </li>
                    <li class="desa-nav-item">
                        <a href="{{ route('desa.pembangunan.administrasi.index') }}"
                            class="desa-nav-link {{ request()->routeIs('desa.pembangunan.administrasi.*') ? 'active' : '' }}">
                            <i class="desa-nav-icon fas fa-file-signature text-info"></i>
                            <span>Bantuan Administrasi Kegiatan</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        <div class="desa-nav-section">
            <div class="desa-nav-title">Trantibum</div>
            <ul class="desa-nav-menu">
                <li class="desa-nav-item">
                    <a href="{{ route('desa.trantibum.kejadian.index') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.trantibum.kejadian.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-shield-alt text-danger"></i>
                        <span>Laporan Trantibum</span>
                    </a>
                </li>
                <li class="desa-nav-item">
                    <a href="{{ route('desa.trantibum.relawan.index') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.trantibum.relawan.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-users-cog text-primary"></i>
                        <span>Tim Relawan</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="desa-nav-section">
            <div class="desa-nav-title">Laporan & Arsip</div>
            <ul class="desa-nav-menu">
                <li class="desa-nav-item">
                    <a href="{{ route('desa.submissions.index') }}"
                        class="desa-nav-link {{ request()->routeIs('desa.submissions.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-file-alt"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="desa-nav-item">
                    <a href="#" class="desa-nav-link {{ request()->routeIs('desa.riwayat.*') ? 'active' : '' }}">
                        <i class="desa-nav-icon fas fa-history"></i>
                        <span>Riwayat</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>