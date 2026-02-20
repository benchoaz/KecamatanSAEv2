<!DOCTYPE html>
<html lang="id" data-theme="emerald">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kecamatan SAE') - {{ appProfile()->app_name }}</title>

    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind & DaisyUI via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content flex flex-col min-h-screen">
            <!-- Topbar -->
            <header class="navbar glass-header sticky top-0 z-30 border-b border-base-200 px-4">
                <div class="flex-none lg:hidden">
                    <label for="main-drawer" class="btn btn-square btn-ghost overflow-visible drawer-button">
                        <i class="fas fa-bars fa-lg"></i>
                    </label>
                </div>
                <div class="flex-1 px-2 mx-2">
                    <div class="text-sm breadcrumbs">
                        <ul>
                            <li><a href="{{ route('desa.dashboard') }}">Dashboard</a></li>
                            @yield('breadcrumb')
                        </ul>
                    </div>
                </div>
                <div class="flex-none gap-2">
                    <!-- User Info -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-circle avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-full w-10">
                                <span>{{ substr(Auth::user()->nama ?? 'U', 0, 1) }}</span>
                            </div>
                        </label>
                        <ul tabindex="0"
                            class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                            <li><a class="justify-between">Profil <span class="badge badge-primary"> Baru</span></a>
                            </li>
                            <li><a>Pengaturan</a></li>
                            <li>
                                <form action="{{ url('/logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-4 lg:p-8 flex-1">
                @if(session('success'))
                    <div class="alert alert-success shadow-lg mb-6 rounded-2xl border-none text-white">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="footer footer-center p-4 bg-base-100 text-base-content border-t border-base-200">
                <aside>
                    <p>© {{ date('Y') }} {{ appProfile()->app_name }} - Semangat SAE (Saling Asah, Asih, Asuh)</p>
                </aside>
            </footer>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side z-40">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <ul class="menu p-4 w-80 min-h-screen bg-base-100 text-base-content border-r border-base-200 gap-1">
                <!-- Sidebar Header -->
                <div
                    class="flex items-center gap-3 px-4 py-6 mb-4 bg-primary rounded-2xl text-primary-content shadow-lg shadow-primary/20">
                    <div class="avatar placeholder">
                        <div class="bg-white text-primary rounded-full w-12">
                            <i class="fas fa-leaf fa-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight">Kecamatan SAE</h1>
                        <p class="text-xs opacity-80 uppercase font-bold tracking-widest">Operator Desa Domain</p>
                    </div>
                </div>

                <!-- Navigations -->
                <li class="menu-title px-4 mt-2">Pusat Layanan</li>
                <li><a href="{{ route('desa.dashboard') }}"
                        class="{{ Request::is('desa/dashboard*') ? 'active' : '' }}"><i class="fas fa-home w-5"></i>
                        Beranda</a></li>

                <li class="menu-title px-4 mt-4">Pembangunan & ADM</li>
                <li>
                    <details open>
                        <summary><i class="fas fa-hammer w-5"></i> Pengelolaan Kegiatan</summary>
                        <ul>
                            <li><a href="{{ route('desa.pembangunan.fisik.index') }}"><i class="fas fa-road w-4"></i>
                                    Fisik (Infrastruktur)</a></li>
                            <li><a href="{{ route('desa.pembangunan.non-fisik.index') }}"><i
                                        class="fas fa-users w-4"></i> Non-Fisik (Pemberdayaan)</a></li>
                            <li><a href="{{ route('desa.blt.index') }}"><i class="fas fa-heart w-4"></i> BLT Dana
                                    Desa</a></li>
                        </ul>
                    </details>
                </li>

                <li class="menu-title px-4 mt-4">Referensi Kerja (SAE Helper)</li>
                <li><a><i class="fas fa-book w-5"></i> Standard Harga (SSH)</a></li>
                <li><a><i class="fas fa-file-invoice-dollar w-5"></i> Batas Biaya (SBU)</a></li>

                <div class="divider"></div>

                <li><a><i class="fas fa-question-circle w-5"></i> Pusat Bantuan</a></li>
            </ul>
        </div>
    </div>

    @stack('scripts')
</body>

</html>