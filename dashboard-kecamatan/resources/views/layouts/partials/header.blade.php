<header class="header border-0 shadow-sm px-4">
    <div class="header-left">
        <button class="menu-toggle shadow-sm rounded-3 border-0 bg-white text-primary-900" id="menuToggle">
            <i class="fas fa-bars-staggered"></i>
        </button>

        <div class="breadcrumb ms-2 d-none d-lg-flex">
            <span class="breadcrumb-item">
                <i class="fas fa-house-chimney text-brand-600"></i>
            </span>
            <span class="breadcrumb-separator opacity-25">/</span>
            <span class="breadcrumb-item fw-bold text-primary-900">@yield('breadcrumb', 'Dashboard Kontrol')</span>
        </div>
    </div>

    <div class="header-right">
        <!-- Search -->
        <div class="header-search ps-3 border-start border-primary-50 d-none d-md-flex">
            <i class="fas fa-magnifying-glass text-tertiary"></i>
            <input type="text" placeholder="Cari data desa atau indikator..."
                class="search-input bg-primary-50 border-0 rounded-pill ps-5">
        </div>

        <!-- System Controls -->
        <div class="d-flex align-items-center gap-2 ps-3 border-start border-primary-50">


            <!-- Notifications Dropdown -->
            <div class="gov-profile ms-2" id="notificationDropdown">
                <button class="header-btn gov-profile__trigger border-0 bg-transparent text-secondary position-relative">
                    <i class="fas fa-bell"></i>
                    @php 
                        $announcements = $internalAnnouncements ?? collect([]);
                        $unreadServices = $unreadServiceCount ?? 0;
                        $totalNotifications = $announcements->count() + $unreadServices;
                    @endphp
                    @if($totalNotifications > 0)
                        <span class="position-absolute top-2 end-2 p-1 bg-danger border border-white rounded-circle"
                            style="width: 8px; height: 8px;"></span>
                    @endif
                </button>

                <div class="gov-profile__menu" style="width: 320px; right: 0;">
                    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary-900">Notifikasi</h6>
                        @if($totalNotifications > 0)
                            <span class="badge bg-brand-50 text-brand-600 rounded-pill">{{ $totalNotifications }} Baru</span>
                        @endif
                    </div>
                    <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                        {{-- New Service Submissions --}}
                        @foreach($recentUnreadServices ?? [] as $svc)
                            <a href="{{ route('kecamatan.pelayanan.show', $svc->id) }}" class="px-4 py-3 border-bottom hover-bg-light transition-all d-block text-decoration-none bg-teal-50/30">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0 bg-teal-50 text-teal-600 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-teal-900">Pengajuan Layanan baru</span>
                                            <span class="text-tertiary" style="font-size: 10px;">{{ $svc->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mb-0 text-slate-500 small lh-sm"><strong>{{ $svc->nama_pemohon }}</strong> mengajukan {{ $svc->jenis_layanan }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                        {{-- Announcements --}}
                        @forelse($announcements as $ann)
                            <div class="px-4 py-3 border-bottom hover-bg-light transition-all">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0 bg-{{ $ann->priority == 'important' ? 'danger' : 'info' }}-50 text-{{ $ann->priority == 'important' ? 'danger' : 'info' }}-600 rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px;">
                                        <i
                                            class="fas {{ $ann->priority == 'important' ? 'fa-exclamation-circle' : 'fa-info-circle' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-primary-900">{{ $ann->title }}</span>
                                            <span class="text-tertiary"
                                                style="font-size: 10px;">{{ $ann->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mb-0 text-tertiary small lh-sm">{{ Str::limit($ann->content, 80) }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            @if(count($recentUnreadServices ?? []) == 0)
                                <div class="text-center py-5 px-4">
                                    <i class="fas fa-bell-slash fa-2x text-primary-50 mb-3 d-block"></i>
                                    <p class="text-tertiary small mb-0">Belum ada notifikasi baru untuk Anda.</p>
                                </div>
                            @endif
                        @endforelse
                    </div>
                    @if($announcements->count() > 0)
                        <div class="p-2 border-top text-center">
                            <a href="#" class="small text-brand-600 fw-bold text-decoration-none p-2 d-block">Liat Semua
                                Pengumuman</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Profile (Standardized Gov Component) -->
        <!-- User Profile (Standardized Gov Component) -->
        @auth
            <div class="gov-profile" id="govProfileDropdown">
                <button class="gov-profile__trigger shadow-sm" type="button" aria-haspopup="true" aria-expanded="false"
                    onclick="document.getElementById('govProfileDropdown').classList.toggle('is-active'); event.stopPropagation();">
                    <div class="gov-profile__avatar bg-brand-600">
                        {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'U', 0, 2)) }}
                    </div>
                    <div class="gov-profile__name d-none d-sm-block">
                        {{ auth()->user()->nama_lengkap ?? 'User' }}
                    </div>
                    <i class="fas fa-chevron-down gov-profile__chevron"></i>
                </button>

                <div class="gov-profile__menu">
                    <div class="gov-profile__header">
                        <div class="gov-profile__avatar gov-profile__avatar--large shadow-sm">
                            {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'U', 0, 2)) }}
                        </div>
                        <div class="gov-profile__details">
                            <h6>{{ auth()->user()->nama_lengkap ?? 'User' }}</h6>
                            <span class="gov-profile__role">{{ optional(auth()->user()->role)->nama_role ?? 'Administrator' }}</span>
                        </div>
                    </div>

                    <ul class="gov-profile__list">
                        <li>
                            <a href="/admin/profile" class="gov-profile__item">
                                <i class="fas fa-user-circle"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <a href="/admin/profile" class="gov-profile__item">
                                <i class="fas fa-lock text-info"></i> Ubah Password
                            </a>
                        </li>
                        <li>
                            <button type="button" class="gov-profile__item gov-profile__item--logout"
                                data-form-id="logout-form-header">
                                <i class="fas fa-power-off"></i> Keluar Aplikasi
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        @else
            <!-- Guest View: Login Button -->
            <a href="{{ route('login') }}" class="btn btn-sm btn-brand-600 ms-3 rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </a>
        @endauth

        <!-- Hidden Logout Form -->
        <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</header>

<!-- Profile Component Scripts -->
<script src="{{ asset('js/components/profile.js') }}"></script>
<script>
    // Failsafe: Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const profileDropdown = document.getElementById('govProfileDropdown');
        if (profileDropdown && !e.target.closest('#govProfileDropdown')) {
            profileDropdown.classList.remove('is-active');
        }
    });
</script>
@stack('scripts')