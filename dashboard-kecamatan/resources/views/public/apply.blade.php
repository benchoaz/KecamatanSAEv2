@extends('layouts.public')

@section('title', $context['title'] . ' - Portal Layanan')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 md:pt-32 pb-32">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
        {{-- Custom Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-8 animate__animated animate__fadeIn">
            <a href="{{ route('layanan') }}" class="hover:text-teal-600 transition-colors">Layanan</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600">{{ $context['title'] }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            {{-- Left: Requirements Sidebar --}}
            <div class="lg:col-span-4 space-y-6 animate__animated animate__fadeInLeft">
                <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] p-6 md:p-8 shadow-xl shadow-slate-200/50 border border-white">
                    <div class="w-16 h-16 bg-{{ $context['color'] }}-100 text-{{ $context['color'] }}-600 rounded-2xl flex items-center justify-center mb-6 text-2xl shadow-inner">
                        <i class="{{ $context['icon'] }}"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">{{ $context['title'] }}</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter mb-8">Persyaratan Berkas</p>

                    <div class="space-y-4">
                        @foreach($context['requirements'] as $req)
                        <div class="flex items-start gap-3">
                            <div class="mt-1 w-5 h-5 rounded-full bg-{{ $context['color'] }}-50 text-{{ $context['color'] }}-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                            <span class="text-sm font-medium text-slate-600 leading-tight">{{ $req }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-10 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-3 text-amber-600 mb-2">
                            <i class="fas fa-exclamation-triangle text-sm"></i>
                            <span class="text-[10px] font-black uppercase">Catatan Penting</span>
                        </div>
                        <p class="text-[10px] text-slate-500 leading-relaxed italic">
                            Pastikan foto/dokumen yang diunggah terbaca dengan jelas untuk mempercepat proses verifikasi petugas.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: Main Form --}}
            <div class="lg:col-span-8 animate__animated animate__fadeInRight animate__delay-1s">
                <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] md:rounded-[2.5rem] shadow-2xl shadow-slate-200/60 p-0.5 md:p-1 border border-white">
                    <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] p-6 md:p-12 overflow-hidden relative">
                        {{-- Step Indicator --}}
                        <div class="flex justify-between items-center mb-12">
                            <div class="flex gap-2">
                                <div id="step-dot-1" class="w-8 h-2 rounded-full bg-teal-500 transition-all duration-500"></div>
                                <div id="step-dot-2" class="w-2 h-2 rounded-full bg-slate-200 transition-all duration-500"></div>
                                <div id="step-dot-3" class="w-2 h-2 rounded-full bg-slate-200 transition-all duration-500"></div>
                            </div>
                            <span id="step-label" class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Langkah 1: Data Diri</span>
                        </div>

                        <form id="applyForm" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">

                            {{-- Step 1: Identity --}}
                            <div id="step-1" class="step-content space-y-6">
                                <div class="form-control">
                                    <label class="label mb-2">
                                        <span class="label-text font-bold text-slate-700">Nama Lengkap Sesuai KTP</span>
                                    </label>
                                    <input type="text" name="nama_pemohon" required placeholder="Contoh: Budi Santoso"
                                        class="input input-md md:input-lg w-full bg-slate-50 border-slate-100 focus:bg-white focus:border-teal-500 rounded-2xl transition-all font-medium" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                    <div class="form-control">
                                        <label class="label mb-1 md:mb-2">
                                            <span class="label-text font-bold text-slate-700 text-sm">NIK (16 Digit)</span>
                                        </label>
                                        <input type="text" name="nik" required minlength="16" maxlength="16" placeholder="3513..."
                                            class="input input-md md:input-lg w-full bg-slate-50 border-slate-100 focus:bg-white focus:border-teal-500 rounded-2xl transition-all font-medium" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label mb-1 md:mb-2">
                                            <span class="label-text font-bold text-slate-700 text-sm">WhatsApp Aktif</span>
                                        </label>
                                        <input type="text" name="whatsapp" required placeholder="0812..."
                                            class="input input-md md:input-lg w-full bg-slate-50 border-slate-100 focus:bg-white focus:border-teal-500 rounded-2xl transition-all font-medium" />
                                    </div>
                                </div>

                                <div class="form-control">
                                    <label class="label mb-1 md:mb-2">
                                        <span class="label-text font-bold text-slate-700 text-sm">Desa Domisili</span>
                                    </label>
                                    <select name="desa_id" required class="select select-md md:select-lg w-full bg-slate-50 border-slate-100 focus:bg-white focus:border-teal-500 rounded-2xl transition-all font-medium">
                                        <option value="" disabled selected>Pilih Desa...</option>
                                        @foreach($desas as $desa)
                                            <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Step 2: Documents --}}
                            <div id="step-2" class="step-content hidden space-y-6">
                                <h3 class="font-black text-slate-800 text-lg">Unggah Dokumen Pendukung</h3>
                                <p class="text-xs text-slate-500 mb-6">Format yang didukung: JPG, PNG, PDF (Maks. 5MB per file)</p>
                                
                                <div class="space-y-4" id="attachment-list">
                                    <div class="attachment-item p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200 group relative transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400">
                                                <i class="fas fa-file-upload text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="attachment_labels[]" value="Dokumen Utama" class="bg-transparent font-bold text-slate-700 text-sm focus:outline-none focus:text-teal-600 block w-full mb-1">
                                                <input type="file" name="attachments[]" required class="file-input file-input-ghost file-input-xs w-full text-slate-500" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-more-docs" class="btn btn-ghost btn-sm text-teal-600 hover:bg-teal-50 rounded-xl px-4 normal-case">
                                    <i class="fas fa-plus mr-2"></i> Tambah Dokumen Lain
                                </button>
                            </div>

                            {{-- Step 3: Confirmation --}}
                            <div id="step-3" class="step-content hidden space-y-6">
                                <div class="text-center py-8">
                                    <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-800 mb-2">Konfirmasi Pengajuan</h3>
                                    <p class="text-slate-500 text-sm max-w-sm mx-auto">
                                        Pastikan data yang Anda masukkan sudah benar. Setelah dikirim, Anda akan menerima PIN Lacak melalui WhatsApp.
                                    </p>
                                </div>

                                <div class="form-control">
                                    <label class="label mb-2">
                                        <span class="label-text font-bold text-slate-700 text-xs">Catatan Tambahan untuk Petugas (Opsional)</span>
                                    </label>
                                    <textarea name="uraian" placeholder="Tuliskan detail atau permintaan khusus Anda..."
                                        class="textarea textarea-lg w-full bg-slate-50 border-slate-100 focus:bg-white focus:border-teal-500 rounded-2xl h-32 transition-all font-medium"></textarea>
                                </div>

                                <label class="flex items-start gap-3 cursor-pointer group p-4 bg-slate-50 rounded-2xl hover:bg-white hover:shadow-lg transition-all border border-transparent hover:border-teal-100">
                                    <input type="checkbox" required class="checkbox checkbox-teal mt-1" />
                                    <span class="text-xs text-slate-600 font-medium leading-relaxed">
                                        Saya menyatakan bahwa data yang diberikan adalah benar dan sesuai dengan dokumen aslinya. Saya setuju data ini diproses untuk keperluan administrasi publik.
                                    </span>
                                </label>
                            </div>

                            {{-- Navigation --}}
                            <div class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-slate-50 gap-4">
                                <button type="button" id="prev-btn" class="btn btn-ghost invisible rounded-xl px-8 font-black text-slate-400 normal-case w-full sm:w-auto">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </button>
                                <button type="button" id="next-btn" class="btn bg-teal-800 hover:bg-teal-900 text-white border-0 rounded-xl px-12 font-black shadow-lg shadow-teal-900/20 normal-case w-full sm:w-auto">
                                    Lanjut <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                                </button>
                                <button type="submit" id="submit-btn" class="btn bg-teal-800 hover:bg-teal-900 text-white border-0 rounded-xl px-12 font-black shadow-lg shadow-teal-900/20 hidden normal-case w-full sm:w-auto">
                                    Kirim Sekarang <i class="fas fa-check-circle ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                {{-- Protection Banner --}}
                <div class="mt-8 flex items-center justify-center gap-6 text-slate-400 grayscale opacity-60">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-lg"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Secure Data SSL</span>
                    </div>
                    <div class="w-px h-4 bg-slate-300"></div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-lock text-lg"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Privacy Protected</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<dialog id="successModal" class="modal">
  <div class="modal-box bg-white rounded-[2.5rem] p-12 text-center shadow-2xl overflow-visible">
    <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 bg-teal-500 text-white rounded-full flex items-center justify-center text-4xl shadow-xl shadow-teal-500/30 border-8 border-white">
        <i class="fas fa-check"></i>
    </div>
    <h3 class="font-black text-3xl text-slate-800 mb-4 mt-6">Berhasil Terkirim!</h3>
    <p class="py-4 text-slate-500 font-medium">Pengajuan Anda telah diterima oleh sistem. Silakan simpan PIN Lacak berikut untuk memantau proses berkas Anda.</p>
    
    <div class="bg-slate-100 p-6 rounded-3xl mb-8 relative group overflow-hidden">
        <div class="text-4xl font-black text-teal-600 tracking-[0.5em] pl-4" id="tracking-pin-display">------</div>
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">PIN LACAK BERKAS</div>
    </div>

    <div class="flex flex-col gap-3">
        <a href="{{ route('layanan') }}" id="redirect-btn" class="btn bg-slate-900 border-0 text-white rounded-2xl h-16 font-black normal-case text-lg shadow-xl shadow-slate-900/20">
            Cek Status Sekarang <i class="fas fa-arrow-right ml-2 text-sm"></i>
        </a>
        <button onclick="successModal.close()" class="btn btn-ghost text-slate-400 font-bold normal-case">Kembali ke Beranda</button>
    </div>
  </div>
</dialog>

<script>
    let currentStep = 1;

    const steps = {
        1: { dot: 'step-dot-1', label: 'Langkah 1: Data Diri' },
        2: { dot: 'step-dot-2', label: 'Langkah 2: Unggah Berkas' },
        3: { dot: 'step-dot-3', label: 'Langkah 3: Konfirmasi' }
    };

    function updateNav() {
        // Dot visibility
        for(let s=1; s<=3; s++) {
            const dot = document.getElementById(steps[s].dot);
            if(s <= currentStep) {
                dot.classList.add('bg-teal-500', 'w-8');
                dot.classList.remove('bg-slate-200', 'w-2');
            } else {
                dot.classList.remove('bg-teal-500', 'w-12');
                dot.classList.add('bg-slate-200', 'w-2');
            }
        }

        // Label update
        document.getElementById('step-label').innerText = steps[currentStep].label;

        // Content visibility
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');

        // Button visibility
        document.getElementById('prev-btn').style.visibility = currentStep === 1 ? 'hidden' : 'visible';
        
        if(currentStep === 3) {
            document.getElementById('next-btn').classList.add('hidden');
            document.getElementById('submit-btn').classList.remove('hidden');
        } else {
            document.getElementById('next-btn').classList.remove('hidden');
            document.getElementById('submit-btn').classList.add('hidden');
        }
    }

    document.getElementById('next-btn').addEventListener('click', () => {
        // Validation for each step could be added here
        if(currentStep < 3) {
            currentStep++;
            updateNav();
        }
    });

    document.getElementById('prev-btn').addEventListener('click', () => {
        if(currentStep > 1) {
            currentStep--;
            updateNav();
        }
    });

    // Add more attachments logic
    document.getElementById('add-more-docs').addEventListener('click', () => {
        const list = document.getElementById('attachment-list');
        const newItem = document.createElement('div');
        newItem.className = 'attachment-item p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200 group relative transition-all animate__animated animate__fadeInUp';
        newItem.innerHTML = `
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400">
                    <i class="fas fa-file-upload text-xl"></i>
                </div>
                <div class="flex-1">
                    <input type="text" name="attachment_labels[]" placeholder="Nama Dokumen (Contoh: Akta Lahir)" class="bg-transparent font-bold text-slate-700 text-sm focus:outline-none focus:text-teal-600 block w-full mb-1">
                    <input type="file" name="attachments[]" class="file-input file-input-ghost file-input-xs w-full text-slate-500" />
                </div>
                <button type="button" class="btn btn-circle btn-xs btn-ghost text-rose-400 hover:text-rose-600 hover:bg-rose-50 opacity-0 group-hover:opacity-100 transition-opacity" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        list.appendChild(newItem);
    });

    // Handle Submission
    document.getElementById('applyForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.innerHTML;

        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading loading-spinner"></span> Mengirim...';

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("apply.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();

            if(data.success) {
                document.getElementById('tracking-pin-display').innerText = data.tracking_code;
                document.getElementById('redirect-btn').href = data.redirect;
                successModal.showModal();
            } else {
                alert(data.message || 'Gagal mengirim pengajuan.');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan sistem.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    window.onload = updateNav;
</script>

<style>
    .step-content {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
