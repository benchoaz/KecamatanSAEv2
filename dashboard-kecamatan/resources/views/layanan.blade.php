<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ appProfile()->full_region_name }} – Pusat Layanan</title>
    @if(appProfile()->logo_path)
        <link rel="icon" href="{{ asset('storage/' . appProfile()->logo_path) }}" type="image/png">
    @endif

    <!-- Tailwind CSS + DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.6.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts - Poppins (lebih mirip referensi) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .service-card {
            scroll-margin-top: 100px;
        }

        .highlight-service {
            animation: pulse-border 2s infinite;
            border: 3px solid #0d9488;
        }

        @keyframes pulse-border {

            0%,
            100% {
                border-color: #0d9488;
            }

            50% {
                border-color: #14b8a6;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Navbar -->
    <div class="navbar bg-white shadow-sm px-6 py-4 sticky top-0 z-50">
        <div class="navbar-start">
            <a href="/" class="flex items-center gap-4">
                @if(appProfile()->logo_path)
                    <img src="{{ asset('storage/' . appProfile()->logo_path) }}" alt="Logo"
                        class="h-16 w-auto object-contain">
                @else
                    <div class="w-12 h-12 bg-teal-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-landmark text-white text-xl"></i>
                    </div>
                @endif
                <div>
                    <div class="text-xl font-black text-slate-800 tracking-tight uppercase">
                        {{ appProfile()->full_region_name }}
                    </div>
                    <div class="text-sm font-medium text-slate-500">{{ appProfile()->tagline ?? 'Besuk BERKARYA' }}
                    </div>
                </div>
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li><a href="/" class="font-medium text-gray-700 hover:text-teal-600">Beranda</a></li>
                <li><a href="/layanan" class="font-medium text-teal-600">Layanan</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <a href="{{ route('login') }}" class="btn btn-outline btn-success rounded-lg px-6">Masuk</a>
        </div>
    </div>

    <!-- Info Ticker (Directly Matched with Dashboard Style) -->
    @if(isset($publicAnnouncements) && $publicAnnouncements->count() > 0)
        <div
            class="relative bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border-b border-slate-700 overflow-hidden z-40">
            <div
                class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
            </div>
            <div class="container mx-auto max-w-7xl flex items-center h-10">
                <!-- Label (Slanted "Info" badge) -->
                <div
                    class="relative z-10 flex items-center h-full bg-rose-600 px-6 transform -skew-x-12 -ml-4 shadow-lg shadow-rose-900/50">
                    <div
                        class="transform skew-x-12 flex items-center gap-2 text-white font-black text-[10px] uppercase tracking-widest">
                        <span class="animate-pulse w-2 h-2 bg-white rounded-full"></span>
                        Info
                    </div>
                </div>

                <!-- Ticker Content -->
                <div class="flex-1 overflow-hidden relative h-full flex items-center pl-6">
                    <div class="ticker-wrap w-full">
                        <div class="ticker-move inline-block whitespace-nowrap hover:pause-animation">
                            <!-- Internal Status (Related to Dashboard) -->
                            <div class="inline-flex items-center mx-8">
                                <span class="text-emerald-400 mr-2 text-xs"><i class="fas fa-signal"></i></span>
                                <span class="text-xs font-bold text-slate-300">Status Koneksi: <span
                                        class="text-emerald-500">Online</span></span>
                            </div>
                            <div class="inline-flex items-center mx-8">
                                <span class="text-blue-400 mr-2 text-xs"><i class="fas fa-microchip"></i></span>
                                <span class="text-xs font-bold text-slate-300">Sistem: <span
                                        class="text-blue-400">Siap</span></span>
                            </div>

                            @foreach($publicAnnouncements as $ann)
                                <div class="inline-flex items-center mx-8 group cursor-pointer"
                                    onclick="openBotWithQuery('{{ $ann->content }}')">
                                    <span class="text-rose-400 mr-2 text-xs"><i class="fas fa-chevron-right"></i></span>
                                    <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors">
                                        {{ $ann->content }}
                                    </span>
                                    <span
                                        class="ml-3 text-[9px] font-bold text-slate-500 border border-slate-700 px-1.5 rounded bg-slate-800/50">
                                        {{ $ann->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                            <!-- Duplicate for infinite scroll -->
                            <div class="inline-flex items-center mx-8">
                                <span class="text-emerald-400 mr-2 text-xs"><i class="fas fa-signal"></i></span>
                                <span class="text-xs font-bold text-slate-300">Status Koneksi: <span
                                        class="text-emerald-500">Online</span></span>
                            </div>
                            <div class="inline-flex items-center mx-8">
                                <span class="text-blue-400 mr-2 text-xs"><i class="fas fa-microchip"></i></span>
                                <span class="text-xs font-bold text-slate-300">Sistem: <span
                                        class="text-blue-400">Siap</span></span>
                            </div>
                            @foreach($publicAnnouncements as $ann)
                                <div class="inline-flex items-center mx-8 group cursor-pointer"
                                    onclick="openBotWithQuery('{{ $ann->content }}')">
                                    <span class="text-rose-400 mr-2 text-xs"><i class="fas fa-chevron-right"></i></span>
                                    <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors">
                                        {{ $ann->content }}
                                    </span>
                                    <span
                                        class="ml-3 text-[9px] font-bold text-slate-500 border border-slate-700 px-1.5 rounded bg-slate-800/50">
                                        {{ $ann->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes ticker-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .ticker-move {
            display: inline-block;
            animation: ticker-scroll 30s linear infinite;
        }

        .hover\:pause-animation:hover {
            animation-play-state: paused;
        }
    </style>

    <!-- Content Header (Simple) -->
    <div class="bg-white border-b border-slate-100 py-12">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-black text-slate-800">Pusat Layanan</h1>
            <p class="text-slate-500 font-medium">Pilih jenis layanan yang Anda butuhkan di bawah ini.</p>
        </div>
    </div>

    <!-- Services Grid (Dynamic from MasterLayanan) -->
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @forelse($masterLayanan as $svc)
                <div id="{{ $svc->slug }}"
                    class="group bg-white rounded-[2.5rem] p-8 border border-slate-100 hover:border-teal-100 transition-all duration-500 hover:shadow-[0_20px_50px_-12px_rgba(13,148,136,0.12)] relative overflow-hidden flex flex-col h-full {{ isset($jenis) && $jenis == $svc->slug ? 'highlight-service' : '' }}">
                    <!-- Top Accent -->
                    <div
                        class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r {{ $svc->warna_bg ?? 'from-teal-500 to-teal-600' }} opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>

                    <div class="flex items-start gap-6 mb-6">
                        <div
                            class="w-16 h-16 rounded-2xl {{ $svc->warna_bg ?? 'bg-teal-50' }} {{ $svc->warna_text ?? 'text-teal-600' }} flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <i class="fas {{ $svc->ikon ?? 'fa-file-alt' }} text-2xl"></i>
                        </div>
                        <div>
                            <h3
                                class="text-xl font-black text-slate-800 mb-1 group-hover:text-teal-700 transition-colors leading-tight">
                                {{ $svc->nama_layanan }}
                            </h3>
                            <div
                                class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <i class="far fa-clock text-teal-500"></i>
                                <span>Estimasi: {{ $svc->estimasi_waktu ?? '15 Menit' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8 flex-grow">
                        <div
                            class="bg-slate-50 rounded-2xl p-4 border border-slate-100 group-hover:bg-white group-hover:border-teal-50 transition-colors">
                            <p
                                class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fas fa-list-check text-[10px]"></i> Persyaratan
                            </p>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                {{ $svc->deskripsi_syarat }}
                            </p>
                        </div>
                    </div>

                    <button
                        onclick="openSubmissionModal('{{ $svc->nama_layanan }}', '{{ str_replace(["\r", "\n"], ' ', addslashes($svc->deskripsi_syarat)) }}', {{ json_encode($svc->attachment_requirements ?? []) }})"
                        class="btn btn-sm bg-teal-600 hover:bg-teal-700 border-none text-white rounded-xl px-6 w-full group-hover:shadow-md transition-all py-3 h-auto font-black uppercase tracking-widest text-[10px]">
                        Ajukan / Hubungi
                    </button>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-folder-open text-6xl text-slate-200 mb-4"></i>
                    <p class="text-slate-500">Belum ada layanan tersedia.</p>
                </div>
            @endforelse

            <!-- Special Card: Pengaduan -->
            <div class="card bg-teal-600 text-white shadow-lg hover:shadow-xl transition-all">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-comment-dots text-teal-600 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold">Lapor / Pengaduan</h3>
                    <p class="text-sm opacity-90">Sampaikan kritik, saran, dan aspirasi Anda</p>
                    <div class="card-actions mt-4">
                        <button class="btn btn-sm btn-white rounded-full" onclick="openModal('pengaduan')">Lapor
                            Sekarang</button>
                    </div>
                </div>
            </div>

            <!-- Special Card: Lainnya -->
            <div class="card bg-white shadow-lg hover:shadow-xl transition-all">
                <div class="card-body items-center text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-ellipsis-h text-gray-500 text-3xl"></i>
                    </div>
                    <h3 class="card-title text-lg font-semibold text-gray-800">Layanan Lainnya</h3>
                    <p class="text-sm text-gray-600">Layanan administrasi lainnya</p>
                    <div class="card-actions mt-4">
                        <button class="btn btn-sm btn-outline btn-primary rounded-full"
                            onclick="openModal('lainnya')">Hubungi Kami</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Form -->
    <dialog id="serviceModal" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4" id="modalTitle">Ajukan Layanan</h3>

            <form id="serviceForm" method="POST" action="{{ route('public.service.submit') }}"
                enctype="multipart/form-data">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" name="jenis_layanan" id="jenisLayanan" value="">
                <input type="hidden" name="category" id="category" value="pelayanan">
                <!-- Honeypot -->
                <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                <!-- Service Info -->
                <div class="bg-teal-50 p-4 rounded-lg mb-4">
                    <p class="text-sm text-teal-700" id="serviceInfo">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="serviceName">Layanan</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Pemohon -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Nama Lengkap <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" name="nama_pemohon" class="input input-bordered w-full" required
                            placeholder="Nama lengkap Anda">
                    </div>

                    <!-- NIK -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">NIK</span>
                        </label>
                        <input type="text" name="nik" class="input input-bordered w-full" maxlength="16"
                            placeholder="16 digit NIK">
                    </div>

                    <!-- No WhatsApp -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">No WhatsApp <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" name="whatsapp" class="input input-bordered w-full" required
                            placeholder="628xxxxx" value="62">
                    </div>

                    <!-- Desa -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Desa/Kelurahan</span>
                        </label>
                        <select name="desa_id" class="select select-bordered w-full">
                            <option value="">Pilih Desa...</option>
                            @if(isset($desas))
                                @foreach($desas as $desa)
                                    <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                                @endforeach
                            @endif
                            <option value="999">Luar Wilayah</option>
                        </select>
                    </div>
                </div>

                <!-- Uraian -->
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Uraian Permintaan <span
                                class="text-red-500">*</span></span>
                    </label>
                    <textarea name="uraian" class="textarea textarea-bordered" required
                        placeholder="Jelaskan keperluan Anda..." rows="4"></textarea>
                </div>

                <!-- Berkas Upload (Dynamic) -->
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Persyaratan Berkas</span>
                    </label>
                    <div id="dynamicAttachments" class="space-y-3">
                        <p class="text-sm text-gray-500 italic">Klik "Ajukan" pada layanan untuk melihat persyaratan
                            berkas.</p>
                    </div>
                </div>

                <!-- Agreement -->
                <div class="form-control mt-4">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_agreed" class="checkbox checkbox-primary" checked />
                        <span class="label-text">Data yang saya berikan adalah benar dan dapat
                            dipertanggungjawabkan</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">© {{ date('Y') }} {{ appProfile()->full_region_name }}. All rights reserved.</p>
            <p class="text-sm text-gray-500 mt-2">Hubungi kami melalui WhatsApp untuk bantuan lebih lanjut</p>
        </div>
    </footer>

    <!-- JavaScript for Modal -->
    <script>
        const serviceNames = {
            'ktp': 'KTP Elektronik',
            'kk': 'Kartu Keluarga',
            'akta': 'Akta Kelahiran',
            'sktm': 'Surat Keterangan Tidak Mampu',
            'domisili': 'Surat Keterangan Domisili',
            'nikah': 'Surat Pengantar Nikah',
            'bpjs': 'BPJS Kesehatan',
            'pengaduan': 'Pengaduan / Laporan Warga',
            'lainnya': 'Layanan Lainnya'
        };

        // Fungsi untuk dynamic cards dari MasterLayanan
        function openSubmissionModal(serviceName, requirements = '', attachmentsJson = []) {
            const modal = document.getElementById('serviceModal');
            const jenisInput = document.getElementById('jenisLayanan');
            const categoryInput = document.getElementById('category');
            const serviceNameEl = document.getElementById('serviceName');
            const serviceInfo = document.getElementById('serviceInfo');
            const dynamicAttachments = document.getElementById('dynamicAttachments');

            // Set service name and jenis_layanan
            serviceNameEl.textContent = serviceName;
            serviceInfo.innerHTML = '<i class="fas fa-info-circle mr-2"></i><span>' + serviceName + '</span> - ' + (requirements || 'Layanan Publik');
            jenisInput.value = serviceName;
            categoryInput.value = 'pelayanan';

            // Clear and populate dynamic attachments
            if (dynamicAttachments) {
                dynamicAttachments.innerHTML = '';

                let reqList = [];

                // 1. Priority: Use structured JSON if available (from MasterLayanan)
                if (Array.isArray(attachmentsJson) && attachmentsJson.length > 0) {
                    reqList = attachmentsJson;
                }
                // 2. Fallback: Parse from text requirements
                else if (requirements) {
                    let cleanReqs = requirements.replace(/^(Persyaratan|Syarat|SOP):\s*/i, '');
                    let splitters = [/\d+[\.\)]\s*/, /,\s*/, /;\s*/];
                    let currentList = [cleanReqs];

                    splitters.forEach(regex => {
                        let newList = [];
                        currentList.forEach(item => {
                            newList = newList.concat(item.split(regex).filter(s => s.trim().length > 3));
                        });
                        currentList = newList;
                    });
                    reqList = currentList.map(s => s.trim()).slice(0, 5);
                }

                if (reqList.length > 0) {
                    reqList.forEach(label => {
                        addAttachmentField(dynamicAttachments, label);
                    });
                } else {
                    // Default - no specific requirements
                    dynamicAttachments.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada persyaratan berkas khusus untuk layanan ini.</p>';
                }
            }

            modal.showModal();
        }

        function addAttachmentField(container, label = '') {
            const div = document.createElement('div');
            div.className = 'bg-white p-3 rounded-lg border border-gray-200';
            div.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-medium text-gray-700">${label || 'Berkas'}</label>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-red-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input type="file" name="foto[]" 
                    class="file-input file-input-bordered w-full text-sm" 
                    accept=".jpg,.jpeg,.png,.pdf" required>
                <input type="hidden" name="foto_labels[]" value="${label}">
            `;
            container.appendChild(div);
        }

        function openModal(service) {
            const modal = document.getElementById('serviceModal');
            const jenisInput = document.getElementById('jenisLayanan');
            const categoryInput = document.getElementById('category');
            const serviceName = document.getElementById('serviceName');

            // Set service name and jenis_layanan
            serviceName.textContent = serviceNames[service] || service;
            jenisInput.value = serviceNames[service] || service;

            // Set category based on service type
            if (service === 'pengaduan') {
                categoryInput.value = 'pengaduan';
            } else {
                categoryInput.value = 'pelayanan';
            }

            modal.showModal();
        }

        function closeModal() {
            const modal = document.getElementById('serviceModal');
            modal.close();
        }

        // Handle form submission
        document.getElementById('serviceForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner"></span> Mengirim...';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message || data.success) {
                        alert('Pengajuan berhasil dikirim! Kami akan memproses permintaan Anda.');
                        closeModal();
                        form.reset();
                    } else if (data.errors) {
                        alert('Error: ' + Object.values(data.errors).flat().join('\n'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Kirim Pengajuan';
                });
        });
    </script>

    <!-- Auto-scroll to service if jenis parameter exists -->
    @if(isset($jenis))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const serviceId = '{{ $jenis }}';
                const element = document.getElementById(serviceId);
                if (element) {
                    setTimeout(function () {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 500);
                }
            });
        </script>
    @endif

</body>

</html>