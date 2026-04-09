<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Terkirim - Pusat Kendali Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 w-[800px] h-[800px] bg-teal-400/20 rounded-full blur-[100px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
    </div>

    <div class="w-full max-w-sm p-6 relative z-10 text-center">
        <!-- Success Icon -->
        <div class="w-24 h-24 bg-white rounded-full shadow-[0_0_40px_rgba(20,184,166,0.3)] mx-auto flex items-center justify-center mb-6 relative">
            <div class="absolute inset-0 rounded-full border-4 border-teal-500 border-dashed animate-[spin_10s_linear_infinite] opacity-20"></div>
            <i class="fas fa-check-circle text-5xl text-teal-500 drop-shadow-md"></i>
        </div>

        <h1 class="text-3xl font-black text-slate-800 mb-3 tracking-tight">Terkirim!</h1>
        <p class="text-slate-500 font-medium text-sm leading-relaxed mb-6">
            Tautan akses ke Dasbor Ekonomi telah kami kirimkan ke WhatsApp <strong class="text-slate-800">+{{ $phone }}</strong>.
        </p>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-start gap-4 text-left mb-8">
            <div class="mt-1 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 mb-1">Cek WhatsApp Anda</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Ketuk tautan di pesan tersebut untuk masuk secara otomatis dan aman seutuhnya tanpa memasukkan kata sandi atau PIN.</p>
            </div>
        </div>

        <a href="{{ route('landing') }}" class="font-bold text-slate-400 hover:text-slate-600 text-sm flex items-center justify-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>
