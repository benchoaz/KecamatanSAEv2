@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12 flex items-center justify-center">
        <div class="container mx-auto px-6">
            <div class="max-w-md mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div
                        class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl shadow-inner">
                        <i class="fas fa-whatsapp"></i>
                    </div>
                    <h1 class="text-2xl font-black text-slate-800 mb-2">Verifikasi Pemilik</h1>
                    <p class="text-slate-500 text-sm font-medium">Langkah terakhir untuk mengaktifkan etalase
                        <strong>{{ $umkm->nama_usaha }}</strong>.</p>
                </div>

                <!-- Modal-style Card -->
                <div
                    class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-8">
                        <div class="space-y-6">
                            <!-- Step 1: Click WA Link -->
                            <div class="bg-teal-50/50 p-6 rounded-3xl border border-teal-100 text-center">
                                <h3 class="text-xs font-black text-teal-800 uppercase tracking-widest mb-4">Langkah 1: Kirim
                                    Pesan WA</h3>
                                <a href="{{ $waUrl }}" target="_blank" onclick="showOtpInput()"
                                    class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#128C7E] text-white font-black px-6 py-4 rounded-2xl shadow-lg transition-all transform hover:-translate-y-1 active:scale-95">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                    <span>Verifikasi via WhatsApp</span>
                                </a>
                                <p class="text-[10px] text-teal-600 font-bold mt-4 uppercase tracking-tighter">GRATIS •
                                    TANPA BIAYA • TANPA TELEPON</p>
                            </div>

                            <!-- Step 2: Input OTP (Initially Hidden or Focused) -->
                            <div id="otpSection" class="space-y-4 pt-4">
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest text-center">Langkah
                                    2: Masukkan Kode OTP</h3>
                                <div class="flex justify-center gap-2">
                                    <input type="text" id="otpInput" maxlength="6" placeholder="KODE OTP"
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-center text-xl font-black tracking-[0.5em] text-slate-800 focus:border-teal-500 focus:ring-0 transition-all uppercase">
                                </div>
                                <button onclick="submitOtp()" id="btnSubmitOtp"
                                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-black py-4 rounded-2xl shadow-lg transition-all transform hover:-translate-y-1 active:scale-95">
                                    KONFIRMASI VERIFIKASI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="mt-8 text-center space-y-4">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        Pesan tidak masuk?
                        <a href="{{ route('umkm_rakyat.verify_step', $umkm->id) }}"
                            class="text-teal-600 hover:underline">Kirim Ulang</a>
                    </p>
                    <div class="pt-4 border-t border-slate-200">
                        <p class="text-[9px] text-slate-400 font-medium">
                            Masa berlaku kode: <span
                                class="text-slate-600 font-black">{{ $verification->expired_at->diffForHumans() }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showOtpInput() {
            // Just focus for better UX
            document.getElementById('otpInput').focus();
        }

        async function submitOtp() {
            const otp = document.getElementById('otpInput').value;
            const btn = document.getElementById('btnSubmitOtp');

            if (!otp || otp.length < 6) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Masukkan 6 karakter kode OTP Anda' });
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';

            try {
                const response = await fetch("{{ route('umkm_rakyat.process_verify', $umkm->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ otp: otp })
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = result.redirect;
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: error.message || 'Terjadi kesalahan sistem.' });
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'KONFIRMASI VERIFIKASI';
            }
        }
    </script>
@endsection