@extends('layouts.public')

@section('title', 'Daftar Usaha & Jasa - ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('content')
    <div class="min-h-screen bg-gradient-to-tr from-slate-50 via-teal-50/30 to-emerald-50/30 py-12 md:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                {{-- Header --}}
                <div class="text-center mb-12 animate__animated animate__fadeIn">
                    <div
                        class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-3xl shadow-xl shadow-teal-200/50 mb-6 group hover:rotate-6 transition-transform duration-500">
                        <i class="fas fa-briefcase text-4xl text-teal-500 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">Daftar Usaha & Jasa
                    </h1>
                    <p class="text-slate-500 text-lg font-medium max-w-md mx-auto leading-relaxed">
                        Tawarkan keahlian Anda kepada warga {{ appProfile()->region_level }}
                        {{ appProfile()->region_name }}.
                        <span class="text-teal-600 font-bold italic">Gratis & Terverifikasi.</span>
                    </p>
                </div>

                {{-- Form Card --}}
                <div
                    class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-white p-2 md:p-3 animate__animated animate__fadeInUp">
                    <div class="bg-slate-50/50 rounded-[2rem] border border-slate-100 p-8 md:p-12">
                        <form action="{{ route('economy.store') }}" method="POST" class="space-y-10">
                            @csrf

                            {{-- Section 1: Data Diri --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center text-white text-sm font-bold">
                                        1</div>
                                    <h2 class="text-xl font-bold text-slate-800">Siapa Anda?</h2>
                                </div>

                                <div class="form-control w-full">
                                    <label class="label mb-1">
                                        <span class="label-text font-bold text-slate-700">Nama Lengkap / Nama Usaha <span
                                                class="text-rose-500">*</span></span>
                                    </label>
                                    <div class="relative group">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-teal-500 text-slate-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input type="text" name="display_name" required
                                            placeholder="Contoh: Budi Santoso atau Toko Bangunan Budi"
                                            class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                    </div>
                                    <label class="label mt-1">
                                        <span class="label-text-alt text-slate-400">Nama yang akan ditampilkan di
                                            direktori.</span>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- No WhatsApp --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">No. WhatsApp <span
                                                    class="text-rose-500">*</span></span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-teal-500">
                                                <i class="fab fa-whatsapp text-lg"></i>
                                            </div>
                                            <input type="tel" name="contact_phone" required placeholder="Contoh: 081234..."
                                                class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                        </div>
                                        <label class="label mt-1">
                                            <span class="label-text-alt text-slate-400">Nomor aktif untuk dihubungi.</span>
                                        </label>
                                    </div>

                                    {{-- Tipe --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Tipe Pekerjaan <span
                                                    class="text-rose-500">*</span></span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 text-slate-400 group-focus-within:text-teal-500">
                                                <i class="fas fa-tags"></i>
                                            </div>
                                            <select name="job_type" required
                                                class="select select-lg w-full pl-12 bg-white border-slate-200 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-2xl transition-all font-medium text-slate-700">
                                                <option disabled selected value="">Pilih Tipe...</option>
                                                @foreach($jobTypes as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="h-px bg-slate-200 w-full"></div>

                            {{-- Section 2: Detail Pekerjaan --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                        2</div>
                                    <h2 class="text-xl font-bold text-slate-800">Detail Usaha / Jasa</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-6">
                                    {{-- Judul Jasa --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Judul Usaha / Jasa <span
                                                    class="text-rose-500">*</span></span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                                                <i class="fas fa-hammer"></i>
                                            </div>
                                            <input type="text" name="job_title" required
                                                placeholder="Contoh: Tukang Bangunan specializing in Rumah"
                                                class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Kategori --}}
                                        <div class="form-control w-full">
                                            <label class="label mb-1">
                                                <span class="label-text font-bold text-slate-700">Kategori Usaha / Jasa <span
                                                        class="text-rose-500">*</span></span>
                                            </label>
                                            <div class="relative group">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 text-slate-400 group-focus-within:text-blue-500">
                                                    <i class="fas fa-folder"></i>
                                                </div>
                                                <select name="job_category" required
                                                    class="select select-lg w-full pl-12 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700">
                                                    <option disabled selected value="">Pilih Kategori...</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Wilayah Layanan --}}
                                        <div class="form-control w-full">
                                            <label class="label mb-1">
                                                <span class="label-text font-bold text-slate-700">Wilayah Layanan</span>
                                            </label>
                                            <div class="relative group">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 text-slate-400 group-focus-within:text-blue-500">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <input type="text" name="service_area"
                                                    placeholder="Contoh: Desa Sukajadi & Sekitar"
                                                    class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Jam Layanan</span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <input type="text" name="service_time" id="service_time_input"
                                                placeholder="Contoh: Senin-Sabtu 07.00-17.00"
                                                class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <button type="button" onclick="setServiceTime('Setiap Hari (24 Jam)')" class="badge badge-outline border-slate-300 hover:border-blue-500 hover:bg-blue-50 py-3 px-4 rounded-xl cursor-pointer text-[10px] font-bold text-slate-500 transition-all">24 Jam</button>
                                            <button type="button" onclick="setServiceTime('Senin - Jumat (08:00 - 16:00)')" class="badge badge-outline border-slate-300 hover:border-blue-500 hover:bg-blue-50 py-3 px-4 rounded-xl cursor-pointer text-[10px] font-bold text-slate-500 transition-all">Jam Kerja</button>
                                            <button type="button" onclick="setServiceTime('Weekend (09:00 - 17:00)')" class="badge badge-outline border-slate-300 hover:border-blue-500 hover:bg-blue-50 py-3 px-4 rounded-xl cursor-pointer text-[10px] font-bold text-slate-500 transition-all">Weekend</button>
                                            <button type="button" onclick="setServiceTime('Sesuai Perjanjian')" class="badge badge-outline border-slate-300 hover:border-blue-500 hover:bg-blue-50 py-3 px-4 rounded-xl cursor-pointer text-[10px] font-bold text-slate-500 transition-all">Sesuai Janji</button>
                                        </div>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Deskripsi Singkat</span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute top-4 left-4 pointer-events-none text-slate-400 group-focus-within:text-blue-500">
                                                <i class="fas fa-align-left"></i>
                                            </div>
                                            <textarea name="short_description" rows="4"
                                                placeholder="Jelaskan keahlian, pengalaman, atau layanan yang Anda tawarkan..."
                                                class="textarea textarea-lg w-full pl-12 pt-4 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300 leading-relaxed"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notice --}}
                            <div class="bg-slate-800 text-slate-200 rounded-3xl p-6 flex gap-4 shadow-xl">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-700 flex items-center justify-center text-teal-400 flex-shrink-0 shadow-inner">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="text-sm leading-relaxed">
                                    <span class="font-bold text-white block mb-0.5 italic">Verifikasi Admin:</span>
                                    Data Anda akan ditinjau oleh operator kecamatan sebelum tampil di direktori publik.
                                    Pastikan data benar demi keamanan bersama.
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                                <button type="submit"
                                    class="btn btn-lg h-16 bg-teal-500 hover:bg-teal-600 border-none text-white rounded-2xl font-black text-lg shadow-xl shadow-teal-200/50 group">
                                    Kirim Sekarang
                                    <i
                                        class="fas fa-paper-plane ml-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                </button>
                                <a href="{{ route('economy.index', ['tab' => 'jasa']) }}"
                                    class="btn btn-lg h-16 bg-white hover:bg-slate-100 border-slate-200 text-slate-500 rounded-2xl font-bold">
                                    Batal & Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Footer Text --}}
                <p class="text-center mt-12 text-slate-400 text-sm font-medium">
                    &copy; {{ date('Y') }} Layanan {{ appProfile()->region_level }} {{ appProfile()->region_name }}.
                    <br class="md:hidden"> Powered by Dashboard Kecamatan.
                </p>
            </div>
        </div>
    </div>

    <style>
        .input-lg,
        .select-lg,
        .textarea-lg {
            min-height: 4rem;
        }

        .select:focus,
        .input:focus,
        .textarea:focus {
            outline: none;
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

        .animate__fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate__fadeIn {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>

    <script>
        function setServiceTime(value) {
            document.getElementById('service_time_input').value = value;
        }
    </script>
@endsection