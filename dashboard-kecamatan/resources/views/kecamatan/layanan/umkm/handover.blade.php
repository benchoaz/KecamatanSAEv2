@extends('layouts.kecamatan')

@section('title', 'Serah Terima Akun UMKM')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Success State -->
                <div class="text-center mb-4">
                    <div class="bg-emerald-100 text-emerald-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h4 class="fw-bold text-slate-800">Pendaftaran Berhasil!</h4>
                    <p class="text-slate-500">Akun UMKM <strong>{{ $umkm->nama_usaha }}</strong> telah dibuat.</p>
                </div>

                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-slate-50 py-3 px-4 border-bottom border-slate-100">
                        <h6 class="mb-0 fw-bold text-slate-700 text-uppercase tracking-wide small">Langkah Serah Terima
                            Digital</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning border-0 d-flex gap-3 mb-4">
                            <i class="fas fa-key text-warning mt-1"></i>
                            <div class="text-sm">
                                <strong>Kunci Akses Pribadi.</strong> Link di bawah ini adalah kunci masuk ke lapak UMKM.
                                Berikan hanya kepada pemilik ({{ $umkm->nama_pemilik }}).
                            </div>
                        </div>

                        <!-- Opsi 1: Kirim WA -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-slate-700 small text-uppercase">Opsi 1: Kirim via WhatsApp
                                (Disarankan)</label>
                            <a href="{{ $waUrl }}" target="_blank"
                                class="btn btn-success w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fab fa-whatsapp fa-lg"></i> Kirim Link Akses ke {{ $umkm->no_wa }}
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-3 my-4">
                            <div class="flex-grow-1 border-top border-slate-200"></div>
                            <div class="small text-slate-400 fw-bold">ATAU</div>
                            <div class="flex-grow-1 border-top border-slate-200"></div>
                        </div>

                        <!-- Opsi 2: Copy Link -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-slate-700 small text-uppercase">Opsi 2: Salin Link
                                Manual</label>
                            <div class="input-group">
                                <input type="text" id="accessLink"
                                    class="form-control bg-slate-50 border-slate-200 font-monospace text-sm"
                                    value="{{ $manageLink }}" readonly>
                                <button class="btn btn-light border border-slate-200 text-slate-600" onclick="copyLink()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="form-text text-xs mt-2">Salin link ini dan kirimkan secara manual jika WhatsApp
                                tidak tersedia.</div>
                        </div>
                    </div>
                    <div class="card-footer bg-slate-50 p-4 text-center">
                        <a href="{{ route('kecamatan.umkm.index') }}"
                            class="btn btn-link text-slate-500 text-decoration-none fw-bold small">
                            Kembali ke Daftar UMKM
                        </a>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="text-center">
                    <p class="small text-slate-400 mb-2">Preview Tampilan Warga:</p>
                    <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm border border-slate-200 text-start"
                        style="max-width: 300px;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-teal-100 p-2 rounded-circle text-teal-600"><i class="fas fa-store"></i></div>
                            <div class="small fw-bold text-slate-700">Manajemen Toko</div>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-3 text-xs text-slate-500 mb-2">
                            Selamat datang, {{ $umkm->nama_pemilik }}! Klik tombol di bawah untuk melengkapi profil usaha
                            Anda.
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-sm rounded-pill disabled">Kelola Toko Saya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyLink() {
                var copyText = document.getElementById("accessLink");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);

                Swal.fire({
                    icon: 'success',
                    title: 'Link Disalin!',
                    text: 'Silakan tempel (paste) link ini ke pesan untuk warga.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        </script>
    @endpush
@endsection