<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - Kecamatan SAE</title>

    <!-- Tailwind CSS + DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Navbar -->
    <div class="navbar bg-white shadow-sm px-6 py-4">
        <div class="navbar-start">
            <a href="/" class="flex items-center gap-3">
                <div class="w-12 h-12 bg-teal-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-landmark text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800">PROBOLINGGO</div>
                    <div class="text-xs text-gray-500">Home</div>
                </div>
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li><a href="#" class="font-medium text-gray-700 hover:text-teal-600">Layanan</a></li>
                <li><a href="#" class="font-medium text-gray-700 hover:text-teal-600">Pariwisata</a></li>
                <li><a href="#" class="font-medium text-gray-700 hover:text-teal-600">Berita</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <a href="{{ route('login') }}" class="btn btn-outline btn-success rounded-lg px-6">Masuk</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Card 1: Lapor Sae (Teal) -->
            <div class="card bg-teal-600 text-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-circle text-teal-600 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold">Lapor Sae</h3>
                    <p class="text-sm opacity-90">Laporkan kritik dan saran Anda disini.</p>
                </div>
            </div>

            <!-- Card 2: Kependudukan -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-heart text-red-500 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Kependudukan</h3>
                    <p class="text-sm text-gray-600">Urus KTP, KK, akta, dan administrasi warga</p>
                </div>
            </div>

            <!-- Card 3: Perizinan -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-seedling text-yellow-500 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Perizinan</h3>
                    <p class="text-sm text-gray-600">Pengajuan dan penerbitan izin usaha atau kegiatan</p>
                </div>
            </div>

            <!-- Card 4: Perpajakan -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-wallet text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Perpajakan</h3>
                    <p class="text-sm text-gray-600">Pembayaran pajak daerah dan retribusi layanan</p>
                </div>
            </div>

            <!-- Card 5: Tenaga Kerja -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-hard-hat text-orange-500 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Tenaga Kerja</h3>
                    <p class="text-sm text-gray-600">Kartu Kuning dan informasi lowongan pekerjaan</p>
                </div>
            </div>

            <!-- Card 6: Pendidikan -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-graduation-cap text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Pendidikan</h3>
                    <p class="text-sm text-gray-600">Pendaftaran sekolah dan administrasi pendidikan</p>
                </div>
            </div>

            <!-- Card 7: Kesehatan -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-plus text-red-500 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Kesehatan</h3>
                    <p class="text-sm text-gray-600">Akses layanan medis dasar hingga rujukan</p>
                </div>
            </div>

            <!-- Card 8: Gawat Darurat (Pink/Red) -->
            <div class="card bg-pink-600 text-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-crown text-pink-600 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold">Gawat Darurat</h3>
                    <p class="text-sm opacity-90">Respon cepat untuk ambulans, damkar, dan bencana.</p>
                </div>
            </div>

            <!-- Card 9: Layanan Lainnya -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-th text-gray-500 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Layanan Lainnya</h3>
                    <p class="text-sm text-gray-600">Temukan layanan lain</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Accessibility Button (like in reference) -->
    <div class="fixed bottom-6 left-6">
        <button class="btn btn-circle btn-primary btn-lg shadow-lg">
            <i class="fas fa-wheelchair text-xl"></i>
        </button>
    </div>

</body>

</html>