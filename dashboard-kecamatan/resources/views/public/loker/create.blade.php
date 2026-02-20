@extends('layouts.public')

@section('title', 'Pasang Info Kerja - ' . appProfile()->kecamatan_name)

@section('content')
    <div class="min-h-screen bg-gradient-to-tr from-slate-50 via-emerald-50/30 to-blue-50/30 py-12 md:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                {{-- Header & Progress --}}
                <div class="text-center mb-12 animate__animated animate__fadeIn">
                    <div
                        class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-3xl shadow-xl shadow-emerald-200/50 mb-6 group hover:rotate-6 transition-transform duration-500">
                        <i class="fas fa-bullhorn text-4xl text-emerald-500 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">Pasang Info Kerja</h1>
                    <p class="text-slate-500 text-lg font-medium max-w-md mx-auto leading-relaxed">
                        Sampaikan kebutuhan tenaga kerja Anda langsung ke warga Kecamatan
                        {{ appProfile()->kecamatan_name }}.
                        <span class="text-emerald-600 font-bold italic">Cepat & Gratis.</span>
                    </p>
                </div>

                {{-- Form Card --}}
                <div
                    class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-white p-2 md:p-3 animate__animated animate__fadeInUp">
                    <div class="bg-slate-50/50 rounded-[2rem] border border-slate-100 p-8 md:p-12">
                        <form action="{{ route('public.loker.store') }}" method="POST" class="space-y-10">
                            @csrf

                            {{-- Section 1: Informasi Utama --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white text-sm font-bold">
                                        1</div>
                                    <h2 class="text-xl font-bold text-slate-800">Apa yang Anda Butuhkan?</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-6">
                                    {{-- Judul --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Judul Pekerjaan <span
                                                    class="text-rose-500">*</span></span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-emerald-500 text-slate-400">
                                                <i class="fas fa-hammer"></i>
                                            </div>
                                            <input type="text" name="title" required
                                                placeholder="Contoh: Butuh Tukang Bangunan Harian"
                                                class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                        </div>
                                        <label class="label mt-1">
                                            <span class="label-text-alt text-slate-400">Gunakan judul yang jelas agar warga
                                                mudah mengerti.</span>
                                        </label>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Kategori --}}
                                        <div class="form-control w-full">
                                            <label class="label mb-1">
                                                <span class="label-text font-bold text-slate-700">Kategori <span
                                                        class="text-rose-500">*</span></span>
                                            </label>
                                            <div class="relative group">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 text-slate-400 group-focus-within:text-emerald-500">
                                                    <i class="fas fa-tags"></i>
                                                </div>
                                                <select name="job_category" required
                                                    class="select select-lg w-full pl-12 bg-white border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-2xl transition-all font-medium text-slate-700">
                                                    <option disabled selected value="">Pilih Kategori...</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- WhatsApp --}}
                                        <div class="form-control w-full">
                                            <label class="label mb-1">
                                                <span class="label-text font-bold text-slate-700">No. WhatsApp <span
                                                        class="text-rose-500">*</span></span>
                                            </label>
                                            <div class="relative group">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500">
                                                    <i class="fab fa-whatsapp text-lg"></i>
                                                </div>
                                                <input type="tel" name="contact_wa" required placeholder="Contoh: 081234..."
                                                    class="input input-lg w-full pl-12 bg-white border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300" />
                                            </div>
                                            <label class="label mt-1">
                                                <span class="label-text-alt text-slate-400">Pastikan nomor aktif untuk
                                                    dihubungi warga.</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="h-px bg-slate-200 w-full"></div>

                            {{-- Section 2: Detail Lokasi & Waktu --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                        2</div>
                                    <h2 class="text-xl font-bold text-slate-800">Lokasi & Waktu</h2>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Desa --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Desa Lokasi</span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 text-slate-400 group-focus-within:text-blue-500">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <select name="desa_id"
                                                class="select select-lg w-full pl-12 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700">
                                                <option value="">Semua Desa / Lainnya</option>
                                                @foreach($desas as $desa)
                                                    <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Waktu Kerja --}}
                                    <div class="form-control w-full">
                                        <label class="label mb-1">
                                            <span class="label-text font-bold text-slate-700">Sistem Kerja</span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 text-slate-400 group-focus-within:text-blue-500">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <select name="work_time"
                                                class="select select-lg w-full pl-12 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700">
                                                <option value="Harian">Harian</option>
                                                <option value="Mingguan">Mingguan</option>
                                                <option value="Borongan">Borongan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Keterangan --}}
                                <div class="form-control w-full">
                                    <label class="label mb-1">
                                        <span class="label-text font-bold text-slate-700">Keterangan Tambahan</span>
                                    </label>
                                    <div class="relative group">
                                        <div
                                            class="absolute top-4 left-4 pointer-events-none text-slate-400 group-focus-within:text-blue-500">
                                            <i class="fas fa-align-left"></i>
                                        </div>
                                        <textarea name="description" rows="4"
                                            placeholder="Jelaskan detail pekerjaan, keahlian yang dibutuhkan, atau upah yang ditawarkan (opsional)..."
                                            class="textarea textarea-lg w-full pl-12 pt-4 bg-white border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-slate-700 placeholder:text-slate-300 leading-relaxed"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- High Priority Toggle --}}
                            <div
                                class="bg-amber-50 border border-amber-100 rounded-3xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-amber-200/50 flex items-center justify-center text-amber-600 flex-shrink-0">
                                        <i class="fas fa-bolt text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-amber-900">Dibutuhkan Hari Ini?</h3>
                                        <p class="text-xs text-amber-700/70 font-medium">Beri tanda khusus jika butuh tenaga
                                            kerja secepatnya.</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="is_available_today" value="1"
                                    class="toggle toggle-warning toggle-lg shadow-sm" />
                            </div>

                            {{-- Notice --}}
                            <div class="bg-slate-800 text-slate-200 rounded-3xl p-6 flex gap-4 shadow-xl">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-700 flex items-center justify-center text-emerald-400 flex-shrink-0 shadow-inner">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="text-sm leading-relaxed">
                                    <span class="font-bold text-white block mb-0.5 italic">Verifikasi Admin:</span>
                                    Info kerja Anda akan ditinjau oleh operator kecamatan sebelum tampil publik.
                                    Pastikan data benar demi keamanan bersama.
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                                <button type="submit"
                                    class="btn btn-lg h-16 bg-emerald-500 hover:bg-emerald-600 border-none text-white rounded-2xl font-black text-lg shadow-xl shadow-emerald-200/50 group">
                                    Kirim Sekarang
                                    <i
                                        class="fas fa-paper-plane ml-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                </button>
                                <a href="{{ route('public.loker.index') }}"
                                    class="btn btn-lg h-16 bg-white hover:bg-slate-100 border-slate-200 text-slate-500 rounded-2xl font-bold">
                                    Batal & Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Footer Text --}}
                <p class="text-center mt-12 text-slate-400 text-sm font-medium">
                    &copy; {{ date('Y') }} Layanan Masyarakat Kecamatan {{ appProfile()->kecamatan_name }}.
                    <br class="md:hidden"> Powered by Dashboard Kecamatan.
                </p>
            </div>
        </div>
    </div>

    <style>
        .shadow-premium {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }

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

        /* Custom toggle size for daisyui */
        .toggle-lg {
            height: 2.5rem;
            width: 4.5rem;
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
@endsection