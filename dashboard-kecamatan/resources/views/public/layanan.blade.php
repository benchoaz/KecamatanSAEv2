@extends('layouts.public')

@section('title', 'Portal Layanan Publik - ' . appProfile()->region_level . ' ' . appProfile()->region_name)

@section('content')
    <div class="min-h-screen bg-gradient-to-tr from-slate-50 via-teal-50/20 to-blue-50/20 py-12 md:py-20">
        <div class="container mx-auto px-6">
            {{-- Header --}}
            <div class="max-w-4xl mx-auto text-center mb-16 animate__animated animate__fadeIn">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-xl mb-6 text-teal-600">
                    <i class="fas fa-hand-holding-heart text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">Portal Layanan Publik</h1>
                <p class="text-slate-500 text-lg font-medium leading-relaxed">
                    Akses berbagai layanan administrasi dan informasi publik {{ appProfile()->region_level }} {{ appProfile()->region_name }} secara mudah, cepat, dan transparan.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
                {{-- Left: Tracking Section --}}
                <div class="lg:col-span-2 space-y-8 animate__animated animate__fadeInLeft">
                    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 p-1 border border-white">
                        <div class="bg-slate-50/50 rounded-[2rem] p-8 md:p-10 border border-slate-100">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-teal-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                                    <i class="fas fa-search-location text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-slate-800">Lacak Status Berkas</h2>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Real-time Tracking</p>
                                </div>
                            </div>

                            <form id="trackingForm" class="space-y-6">
                                <div class="form-control w-full">
                                    <label class="label mb-2">
                                        <span class="label-text font-bold text-slate-700">Nomor WhatsApp atau ID Berkas</span>
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-teal-500 transition-colors">
                                            <i class="fab fa-whatsapp text-xl"></i>
                                        </div>
                                        <input type="text" id="identifier" name="identifier" required
                                            placeholder="Contoh: 08123456789 atau PIN berkas"
                                            class="input input-lg w-full pl-14 bg-white border-slate-200 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-2xl transition-all font-medium text-slate-700" />
                                    </div>
                                    <label class="label mt-2">
                                        <span class="label-text-alt text-slate-400 italic">
                                            <i class="fas fa-info-circle mr-1"></i> Masukkan nomor WA yang digunakan saat pendaftaran.
                                        </span>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-lg h-16 w-full bg-teal-600 hover:bg-teal-700 text-white border-0 rounded-2xl font-black text-lg shadow-xl shadow-teal-600/20 group uppercase tracking-wider">
                                    Cek Status Sekarang
                                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </form>

                            {{-- Result Placeholder --}}
                            <div id="resultContainer" class="hidden mt-10 animate__animated animate__fadeIn">
                                <div class="h-px bg-slate-200 w-full mb-8"></div>
                                <div id="resultContent" class="space-y-6 text-slate-700">
                                    {{-- Dynamic content from JS --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Direct Contact & Help --}}
                <div class="space-y-8 animate__animated animate__fadeInRight">
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-teal-500/20 rounded-full blur-3xl"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-4">Butuh Bantuan?</h3>
                            <p class="text-slate-400 text-sm mb-8 leading-relaxed">
                                Jika Anda mengalami kesulitan atau memiliki pertanyaan lebih lanjut, hubungi petugas layanan kami melalui WhatsApp.
                            </p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', appProfile()->whatsapp_bot ?? '628123456789') }}" target="_blank" class="flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 rounded-2xl transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xs font-black">Admin Pelayanan</div>
                                        <div class="text-[10px] text-slate-400">Online Hari Kerja</div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-slate-100">
                        <h4 class="font-black text-slate-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-teal-500"></i>
                            Status Update
                        </h4>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="w-1 h-12 rounded-full bg-teal-500"></div>
                                <div class="flex-1">
                                    <div class="text-xs font-black text-slate-800">Menunggu</div>
                                    <p class="text-[10px] text-slate-500">Berkas Anda telah diterima & masuk antrean verifikasi.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-1 h-12 rounded-full bg-blue-500"></div>
                                <div class="flex-1">
                                    <div class="text-xs font-black text-slate-800">Proses</div>
                                    <p class="text-[10px] text-slate-500">Berkas sedang diproses oleh petugas teknis.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-1 h-12 rounded-full bg-emerald-500"></div>
                                <div class="flex-1">
                                    <div class="text-xs font-black text-slate-800">Selesai</div>
                                    <p class="text-[10px] text-slate-500">Berkas telah selesai & dapat diambil/didownload.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🆕 Service Catalog Section --}}
            <div class="mt-24 animate__animated animate__fadeIn animate__delay-1s">
                <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                    <div class="max-w-xl">
                        <h2 class="text-3xl font-black text-slate-800 mb-4">Katalog Layanan Online</h2>
                        <p class="text-slate-500 font-medium">Bikin berkas jadi lebih mudah. Pilih layanan di bawah ini untuk memulai pengajuan secara digital tanpa harus antre.</p>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-white rounded-2xl shadow-sm border border-slate-100">
                        <span class="text-[10px] font-black uppercase text-slate-400 ml-3">Butuh Bantuan?</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', appProfile()->whatsapp_bot ?? '628123456789') }}" class="btn btn-sm bg-teal-600 hover:bg-teal-700 text-white border-0 rounded-xl px-6 normal-case font-bold">Tanya Admin</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($masterLayanan as $svc)
                        <div class="group bg-white rounded-[2rem] p-8 shadow-xl shadow-slate-200/40 border border-white hover:border-teal-100 hover:scale-[1.02] transition-all duration-500 flex flex-col h-full">
                            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r {{ $svc->warna_bg ?? 'from-teal-500 to-teal-600' }} opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="w-14 h-14 rounded-2xl {{ $svc->warna_bg ?? 'bg-teal-50' }} {{ $svc->warna_text ?? 'text-teal-600' }} flex items-center justify-center mb-6 shadow-inner group-hover:rotate-6 transition-transform">
                                <i class="fas {{ $svc->ikon ?? 'fa-file-alt' }} text-xl"></i>
                            </div>

                            <h3 class="text-xl font-black text-slate-800 mb-2 group-hover:text-teal-700 transition-colors">{{ $svc->nama_layanan }}</h3>
                            
                            <div class="flex items-center gap-2 mb-6">
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    {{ $svc->estimasi_waktu ?? '3 Hari' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 font-medium leading-relaxed mb-8 flex-grow">
                                {{ Str::limit($svc->deskripsi_syarat ?? 'Persyaratan umum berlaku.', 80) }}
                            </p>

                            @if($svc->slug === 'pengaduan')
                                <button onclick="document.getElementById('complaintModal').showModal()" class="btn btn-sm w-full bg-slate-900 hover:bg-black text-white border-0 rounded-xl normal-case font-black">Lapor Sekarang</button>
                            @else
                                <a href="{{ route('apply.form', $svc->slug) }}" class="btn btn-sm w-full bg-teal-600 hover:bg-teal-700 text-white border-0 rounded-xl normal-case font-black shadow-lg shadow-teal-500/10">Ajukan Online</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

                {{-- Right: Direct Contact & Help --}}
                <div class="space-y-8 animate__animated animate__fadeInRight">
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-teal-500/20 rounded-full blur-3xl"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-4">Butuh Bantuan?</h3>
                            <p class="text-slate-400 text-sm mb-8 leading-relaxed">
                                Jika Anda mengalami kesulitan atau memiliki pertanyaan lebih lanjut, hubungi petugas layanan kami melalui WhatsApp.
                            </p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', appProfile()->whatsapp_bot ?? '628123456789') }}" target="_blank" class="flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 rounded-2xl transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xs font-black">Admin Pelayanan</div>
                                        <div class="text-[10px] text-slate-400">Online Hari Kerja</div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-slate-100">
                        <h4 class="font-black text-slate-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-teal-500"></i>
                            Status Update
                        </h4>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="w-1 h-12 rounded-full bg-teal-500"></div>
                                <div>
                                    <div class="text-xs font-black text-slate-800">Menunggu</div>
                                    <p class="text-[10px] text-slate-500">Berkas Anda telah diterima & masuk antrean verifikasi.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-1 h-12 rounded-full bg-blue-500"></div>
                                <div>
                                    <div class="text-xs font-black text-slate-800">Proses</div>
                                    <p class="text-[10px] text-slate-500">Berkas sedang diproses oleh petugas teknis.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-1 h-12 rounded-full bg-emerald-500"></div>
                                <div>
                                    <div class="text-xs font-black text-slate-800">Selesai</div>
                                    <p class="text-[10px] text-slate-500">Berkas telah selesai & dapat diambil/didownload.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-trigger search if query param 'q' exists
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('q');
            if (query) {
                document.getElementById('identifier').value = query;
                document.getElementById('trackingForm').dispatchEvent(new Event('submit'));
            }
        });

        document.getElementById('trackingForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const identifier = document.getElementById('identifier').value;
            const resultContainer = document.getElementById('resultContainer');
            const resultContent = document.getElementById('resultContent');

            // Hide previous results
            resultContainer.classList.add('hidden');
            resultContent.innerHTML = '<div class="flex justify-center p-8"><div class="loading loading-spinner loading-lg text-teal-500"></div></div>';
            resultContainer.classList.remove('hidden');

            try {
                const response = await fetch('{{ route('public.tracking.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ identifier })
                });

                const data = await response.json();

                if (data.found) {
                    let html = `
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status Saat Ini</span>
                                    <div class="mt-1">
                                        <span class="px-4 py-2 bg-${data.status_color === 'emerald' ? 'emerald' : data.status_color}-100 text-${data.status_color === 'emerald' ? 'emerald' : data.status_color}-700 rounded-full text-xs font-black">
                                            ${data.status_label}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">ID Berkas</span>
                                    <div class="text-xs font-bold text-slate-800">#${data.tracking_code || data.uuid.substring(0,8)}</div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between pb-3 border-b border-slate-50">
                                    <span class="text-xs font-medium text-slate-500">Layanan</span>
                                    <span class="text-xs font-bold text-slate-800">${data.jenis_layanan}</span>
                                </div>
                                <div class="flex justify-between pb-3 border-b border-slate-50">
                                    <span class="text-xs font-medium text-slate-500">Diajukan</span>
                                    <span class="text-xs font-bold text-slate-800">${data.created_at}</span>
                                </div>
                                ${data.public_response ? `
                                    <div class="p-4 bg-slate-50 rounded-2xl">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Pesan Petugas:</span>
                                        <p class="text-xs text-slate-700 leading-relaxed font-medium">${data.public_response}</p>
                                    </div>
                                ` : ''}
                            </div>

                            ${data.download_url ? `
                                <div class="mt-6">
                                    <a href="${data.download_url}" target="_blank" class="btn btn-lg w-full bg-emerald-600 hover:bg-emerald-700 text-white border-0 rounded-2xl font-black">
                                        <i class="fas fa-download mr-2"></i> Download Berkas Selesai
                                    </a>
                                </div>
                            ` : ''}

                            ${data.pickup_info ? `
                                <div class="mt-6 p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4">
                                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex flex-shrink-0 items-center justify-center">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest block mb-1">Ambil di Kantor:</span>
                                        <p class="text-xs text-slate-700 font-bold leading-none mb-1">Siap: ${data.pickup_info.ready_at || 'Segera'}</p>
                                        <p class="text-[10px] text-slate-500 font-medium">${data.pickup_info.pickup_notes || 'Silakan datang ke loket pelayanan pada jam kerja.'}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    resultContent.innerHTML = html;
                } else {
                    resultContent.innerHTML = `
                        <div class="p-8 text-center bg-rose-50 rounded-[2rem] border border-rose-100">
                            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search-minus text-2xl"></i>
                            </div>
                            <h4 class="font-black text-slate-800 mb-2">Berkas Tidak Ditemukan</h4>
                            <p class="text-xs font-medium text-slate-500 leading-relaxed">
                                Mohon periksa kembali nomor WA atau PIN Anda. Pastikan berkas sudah terdaftar di sistem.
                            </p>
                        </div>
                    `;
                }
            } catch (error) {
                resultContent.innerHTML = '<div class="alert alert-error">Gagal menghubungi server. Silakan coba lagi.</div>';
            }
        });
    </script>
@endsection
