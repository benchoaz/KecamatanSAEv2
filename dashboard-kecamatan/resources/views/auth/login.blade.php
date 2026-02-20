<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ appProfile()->app_name }}</title>
    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Tailwind CSS + DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .login-card-blur {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .premium-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .side-image-overlay {
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.8) 0%, rgba(30, 41, 59, 0.8) 100%);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <!-- Background Decoration -->
    <div
        class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 bg-gradient-to-br from-slate-50 via-white to-teal-50/40">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-teal-200/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[30%] h-[30%] bg-blue-200/20 rounded-full blur-[120px]"></div>
    </div>

    <div
        class="container max-w-5xl mx-auto flex flex-col md:flex-row bg-white rounded-3xl overflow-hidden premium-shadow min-h-[650px] fade-in-up">

        <!-- Left Side: Visual & Info (Hidden on small screens) -->
        <div class="md:w-1/2 relative hidden md:block">
            <img src="{{ asset('media/login_side_image.png') }}" alt="Login Visual"
                class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 side-image-overlay flex flex-col justify-center p-12 text-white">
                <div class="mb-8">
                    <div class="group hover:scale-110 transition-transform duration-500 flex-shrink-0">
                        @if(appProfile()->logo_path)
                            <img src="{{ asset('storage/' . appProfile()->logo_path) }}"
                                style="height: 120px; width: auto; object-fit: contain;"
                                class="drop-shadow-2xl brightness-110" alt="Logo">
                        @else
                            <i class="fas fa-landmark text-white text-6xl opacity-80"></i>
                        @endif
                    </div>
                </div>
                <h1 class="text-4xl font-bold mb-4 leading-tight">Selamat Datang di Portal Internal <span
                        class="text-teal-300">{{ appProfile()->app_name }}</span></h1>
                <p class="text-teal-50/80 mb-8 max-w-md leading-relaxed">
                    {{ appProfile()->tagline ?? 'Sistem administrasi terpadu untuk efisiensi pelayanan masyarakat.' }}
                </p>
                <div class="flex items-center gap-4 mt-auto">
                    <div class="flex -space-x-3">
                        <div
                            class="w-8 h-8 rounded-full border-2 border-white/30 bg-teal-500 overflow-hidden flex items-center justify-center text-[10px] font-bold">
                            ASN</div>
                        <div
                            class="w-8 h-8 rounded-full border-2 border-white/30 bg-slate-700 overflow-hidden flex items-center justify-center text-[10px] font-bold">
                            KAS</div>
                        <div
                            class="w-8 h-8 rounded-full border-2 border-white/30 bg-blue-600 overflow-hidden flex items-center justify-center text-[10px] font-bold">
                            ADM</div>
                    </div>
                    <span class="text-xs text-teal-200/80">Sistem Informasi Manajemen
                        {{ appProfile()->region_name }}</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col justify-center bg-white">
            <div class="mb-10 text-center md:text-left">
                <!-- Mobile Logo -->
                <div class="md:hidden flex justify-center mb-6">
                    <div class="p-2">
                        @if(appProfile()->logo_path)
                            <img src="{{ asset('storage/' . appProfile()->logo_path) }}"
                                style="height: 90px; width: auto; object-fit: contain;" class="flex-shrink-0" alt="Logo">
                        @else
                            <i class="fas fa-landmark text-teal-600 text-4xl"></i>
                        @endif
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 mb-2">Masuk ke Sistem</h2>
                <p class="text-slate-500">Kelola administrasi kewilayahan dengan lebih mudah</p>
            </div>

            <!-- Notifications -->
            @if (session('logout_success'))
                <div
                    class="alert bg-teal-50 border border-teal-100 text-teal-700 mb-6 rounded-2xl py-3 px-4 flex items-center gap-3">
                    <i class="fas fa-check-circle text-teal-500"></i>
                    <div class="text-sm font-medium">Logout Berhasil! <span
                            class="block text-xs font-normal opacity-80 mt-0.5">Sesi Anda telah berakhir dengan aman.</span>
                    </div>
                </div>
            @endif

            @if (isset($errors) && is_object($errors) && $errors->any())
                <div
                    class="alert bg-rose-50 border border-rose-100 text-rose-700 mb-6 rounded-2xl py-3 px-4 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <div class="text-sm">
                        <span class="font-bold">Gagal Masuk</span>
                        @foreach ($errors->all() as $error)
                            <div class="text-xs mt-0.5">{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @elseif(isset($errors) && is_array($errors) && count($errors) > 0)
                <div
                    class="alert bg-rose-50 border border-rose-100 text-rose-700 mb-6 rounded-2xl py-3 px-4 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <div class="text-sm">
                        <span class="font-bold">Gagal Masuk</span>
                        @foreach ($errors as $error)
                            <div class="text-xs mt-0.5">{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div class="form-control">
                    <label class="label mb-1">
                        <span class="label-text font-semibold text-slate-700">Username</span>
                    </label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-teal-600 transition-colors text-slate-400">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <input type="text" name="username" placeholder="admin_kec" value="{{ old('username') }}"
                            class="input input-bordered w-full pl-11 bg-slate-50 border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all rounded-2xl h-14"
                            required autofocus />
                    </div>
                </div>

                <div class="form-control">
                    <label class="label mb-1">
                        <span class="label-text font-semibold text-slate-700">Password</span>
                    </label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-teal-600 transition-colors text-slate-400">
                            <i class="fas fa-key"></i>
                        </div>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="input input-bordered w-full pl-11 pr-12 bg-slate-50 border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all rounded-2xl h-14"
                            required />
                        <button type="button"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-teal-600 focus:outline-none transition-colors toggle-password"
                            tabindex="-1">
                            <i class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between py-1">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-teal rounded-md" />
                        <span class="label-text text-slate-500 text-sm">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm font-medium text-teal-600 hover:text-teal-700 hover:underline">Lupa
                        password?</a>
                </div>

                <button type="submit"
                    class="btn bg-teal-600 hover:bg-teal-700 text-white border-none w-full h-14 rounded-2xl shadow-lg shadow-teal-600/20 text-base font-semibold group">
                    <span>Masuk ke Dashboard</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ url('/') }}"
                    class="text-sm font-medium text-slate-500 hover:text-teal-600 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>

            <p class="mt-auto pt-8 text-center text-[10px] text-slate-400 uppercase tracking-widest">
                Internal Gov System • Version 2.0
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle icon
                    const icon = this.querySelector('i');
                    if (type === 'text') {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
</body>

</html>