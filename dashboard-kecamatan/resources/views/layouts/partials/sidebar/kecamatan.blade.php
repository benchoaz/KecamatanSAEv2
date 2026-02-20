<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon bg-transparent text-white">
                @if(appProfile()->logo_path)
                    <img src="{{ asset('storage/' . appProfile()->logo_path) }}" class="img-fluid"
                        style="max-height: 48px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));">
                @else
                    <i class="fas fa-landmark"></i>
                @endif
            </div>
            <div class="logo-text">
                <span class="logo-title fw-bold text-uppercase">DASHBOARD</span>
                <span class="logo-subtitle tracking-wider">{{ strtoupper(appProfile()->full_region_name) }}</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">ADMINISTRASI & OTORITAS</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.dashboard') }}"
                        class="nav-link {{ request()->is('kecamatan/dashboard*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                        <span class="nav-text">Beranda Pusat</span>
                    </a>
                </li>

            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">BIDANG PELAYANAN</span>
            <ul class="nav-menu">
                <li class="nav-item has-submenu {{ request()->is('kecamatan/pelayanan/inbox*') ? 'open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link submenu-toggle {{ request()->is('kecamatan/pelayanan/inbox*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-inbox"></i></span>
                        <span class="nav-text">Inbox Terpadu</span>
                        <span class="ms-auto small"><i class="fas fa-chevron-right nav-arrow"></i></span>
                    </a>
                    <ul class="nav-submenu">
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pelayanan.inbox', ['category' => 'pelayanan']) }}"
                                class="nav-sublink {{ request()->query('category', 'pelayanan') == 'pelayanan' ? 'active' : '' }}">
                                <i class="fas fa-comments me-2 small"></i> Layanan Warga
                            </a>
                        </li>
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pelayanan.inbox', ['category' => 'umkm']) }}"
                                class="nav-sublink {{ request()->query('category') == 'umkm' ? 'active' : '' }}">
                                <i class="fas fa-store me-2 small"></i> Pendaftaran UMKM
                            </a>
                        </li>
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pelayanan.inbox', ['category' => 'loker']) }}"
                                class="nav-sublink {{ request()->query('category') == 'loker' ? 'active' : '' }}">
                                <i class="fas fa-briefcase me-2 small"></i> Info Loker
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.pelayanan.pengaduan') }}"
                        class="nav-link {{ request()->routeIs('kecamatan.pelayanan.pengaduan') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-bullhorn text-rose-500"></i></span>
                        <span class="nav-text">Pengaduan WhatsApp</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.pelayanan.visitor.index') }}"
                        class="nav-link {{ request()->is('kecamatan/pelayanan/visitor*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-clipboard-user"></i></span>
                        <span class="nav-text">Buku Tamu</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.pelayanan.faq.index') }}"
                        class="nav-link {{ request()->is('kecamatan/pelayanan/faq*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-robot"></i></span>
                        <span class="nav-text">FAQ Administrasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.pelayanan.layanan.index') }}"
                        class="nav-link {{ request()->is('kecamatan/pelayanan/layanan*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                        <span class="nav-text">Daftar Layanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.pelayanan.statistics') }}"
                        class="nav-link {{ request()->is('kecamatan/pelayanan/statistics*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="nav-text">Statistik Layanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.announcements.index') }}"
                        class="nav-link {{ request()->is('kecamatan/announcements*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-bullhorn text-amber-500"></i></span>
                        <span class="nav-text">Pengumuman</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">BIDANG PENGAWASAN</span>
            <ul class="nav-menu">
                <!-- Pemerintahan -->
                <!-- Pemerintahan -->
                <li class="nav-item has-submenu">
                    <a href="javascript:void(0)" class="nav-link submenu-toggle">
                        <span class="nav-icon"><i class="fas fa-shield-halved"></i></span>
                        <span class="nav-text">Pemerintahan</span>
                        <span class="ms-auto small"><i class="fas fa-chevron-right nav-arrow"></i></span>
                    </a>
                    <ul class="nav-submenu">
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pemerintahan.index') }}"
                                class="nav-sublink {{ request()->is('kecamatan/pemerintahan*') ? 'active' : '' }}">
                                <i class="fas fa-chart-line me-2 small"></i> Monev Tata Kelola
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Ekonomi & Pembangunan (Integrated Dropdown) -->
                <li class="nav-item has-submenu">
                    <a href="javascript:void(0)" class="nav-link submenu-toggle">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">Ekonomi & Pembangunan</span>
                        <span class="ms-auto small"><i class="fas fa-chevron-right nav-arrow"></i></span>
                    </a>
                    <ul class="nav-submenu">
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pembangunan.index') }}"
                                class="nav-sublink {{ request()->routeIs('kecamatan.pembangunan.index') || request()->routeIs('kecamatan.pembangunan.show') ? 'active' : '' }}">
                                <i class="fas fa-display me-2 small"></i> Monitoring Utama
                            </a>
                        </li>
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pembangunan.referensi.ssh.index') }}"
                                class="nav-sublink {{ request()->is('kecamatan/pembangunan/referensi/ssh*') ? 'active' : '' }}">
                                <i class="fas fa-tags me-2 small"></i> Master SSH
                            </a>
                        </li>
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.pembangunan.referensi.sbu.index') }}"
                                class="nav-sublink {{ request()->is('kecamatan/pembangunan/referensi/sbu*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar me-2 small"></i> Master SBU
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Kesejahteraan Rakyat -->
                <li class="nav-item">
                    <a href="{{ route('kecamatan.kesra.index') }}"
                        class="nav-link {{ request()->is('kecamatan/kesra*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-dove"></i></span>
                        <span class="nav-text">Kesejahteraan Sosial</span>
                    </a>
                </li>

                <!-- Trantibum (Integrated Dropdown) -->
                <li class="nav-item has-submenu">
                    <a href="javascript:void(0)" class="nav-link submenu-toggle">
                        <span class="nav-icon"><i class="fas fa-masks-theater"></i></span>
                        <span class="nav-text">Trantibum & Linmas</span>
                        <span class="ms-auto small"><i class="fas fa-chevron-right nav-arrow"></i></span>
                    </a>
                    <ul class="nav-submenu">
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.trantibum.index') }}"
                                class="nav-sublink {{ request()->routeIs('kecamatan.trantibum.index') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie me-2 small"></i> Dashboard Monitoring
                            </a>
                        </li>
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.trantibum.kejadian') }}"
                                class="nav-sublink {{ request()->routeIs('kecamatan.trantibum.kejadian') ? 'active' : '' }}">
                                <i class="fas fa-list-ul me-2 small"></i> Data Laporan
                            </a>
                        </li>
                        <li class="nav-submenu-item">
                            <a href="{{ route('kecamatan.trantibum.relawan') }}"
                                class="nav-sublink {{ request()->routeIs('kecamatan.trantibum.relawan') ? 'active' : '' }}">
                                <i class="fas fa-users-cog me-2 small"></i> Relawan Tangguh
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Modul Laporan -->
                <li class="nav-item">
                    <a href="{{ route('kecamatan.laporan.index') }}"
                        class="nav-link {{ request()->is('kecamatan/laporan*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-file-invoice"></i></span>
                        <span class="nav-text">Laporan Terpadu</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">PUBLIKASI & INFORMASI</span>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('kecamatan.berita.index') }}"
                        class="nav-link {{ request()->is('kecamatan/berita*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-newspaper text-info"></i></span>
                        <span class="nav-text">Berita & Artikel</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.umkm.index') }}"
                        class="nav-link {{ Route::is('kecamatan.umkm.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-store text-orange-500"></i></span>
                        <span class="nav-text">Etalase UMKM</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kecamatan.loker.index') }}"
                        class="nav-link {{ Route::is('kecamatan.loker.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-briefcase text-blue-500"></i></span>
                        <span class="nav-text">Lowongan Kerja</span>
                    </a>
                </li>
            </ul>
        </div>

        @if(auth()->user()->isSuperAdmin() || auth()->user()->isOperatorKecamatan())
            <div class="nav-section">
                <span class="nav-section-title">KONFIGURASI SISTEM</span>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.users.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.users.*') ? 'active' : '' }}">
                            <span class=" nav-icon"><i class="fas fa-user-gear"></i></span>
                            <span class="nav-text">Manajemen Pengguna</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.master.desa.index') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.master.desa.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-map-location-dot"></i></span>
                            <span class="nav-text">Data Master Desa</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.settings.geospasial') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.settings.geospasial') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-map-location-dot"></i></span>
                            <span class="nav-text">Geospasial Wilayah</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.settings.profile') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.settings.profile') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-sliders"></i></span>
                            <span class="nav-text">Pengaturan Aplikasi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kecamatan.settings.features') }}"
                            class="nav-link {{ request()->routeIs('kecamatan.settings.features') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-toggle-on"></i></span>
                            <span class="nav-text">Manajemen Fitur</span>
                        </a>
                    </li>
                    @if(auth()->user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.audit-logs.index') }}"
                                class="nav-link {{ request()->routeIs('kecamatan.audit-logs.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-file-invoice"></i></span>
                                <span class="nav-text">Audit Aktivitas</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-card border-0 shadow-sm" style="background: rgba(255,255,255,0.03);">
            <div class="user-avatar bg-brand-600 text-white"><i class="fas fa-user-tie"></i></div>
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
                class="btn btn-danger btn-sm w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm"
                onclick="return confirm('Konfirmasi Keluar\n\nApakah Anda yakin ingin keluar dari aplikasi?')"
                style="font-size: 13px;">
                <i class="fas fa-power-off"></i>
                <span>Keluar Aplikasi</span>
            </button>
        </form>
    </div>
</aside>