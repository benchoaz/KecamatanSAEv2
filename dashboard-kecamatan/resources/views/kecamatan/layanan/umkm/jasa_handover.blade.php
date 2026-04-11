@extends('layouts.kecamatan')

@section('title', 'Serah Terima Jasa Digital (Fasilitator)')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" class="text-decoration-none text-slate-500 small fw-bold">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                        <h4 class="fw-bold text-slate-800 mt-2">Serah Terima Digital</h4>
                    </div>
                    <span class="badge bg-emerald-100 text-emerald-700 px-3 py-2 rounded-pill fw-bold small">
                        <i class="fas fa-check-circle me-1"></i> Terdaftar Berhasil
                    </span>
                </div>

                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-5 text-center">
                        <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4 border border-emerald-100 shadow-sm">
                            <i class="fas fa-magic fa-2x"></i>
                        </div>
                        <h4 class="fw-black text-slate-800 mb-2">{{ $jasa->job_title }}</h4>
                        <p class="text-slate-500 mb-4 px-lg-5">Layanan telah terdaftar. Link di bawah ini adalah akses kunci bagi warga <strong>({{ $jasa->display_name }})</strong> untuk mengelola profil jasanya secara mandiri.</p>

                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-100 mb-4">
                            <div class="mb-3">
                                <span class="text-[10px] text-slate-400 fw-black uppercase tracking-widest block mb-2">Link Akses Langsung (Sekali Klik)</span>
                                <div class="input-group">
                                    <input type="text" id="copyLink" value="{{ $manageLink }}" class="form-control border-0 bg-transparent text-center fw-bold text-primary" readonly>
                                    <button onclick="copyToClipboard()" class="btn btn-primary rounded-3 ms-2 px-3 shadow-sm">
                                        <i class="fas fa-copy me-1"></i> Salin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="{{ $waUrl }}" target="_blank" class="btn btn-success p-3 rounded-4 fw-bold shadow-lg shadow-success/20">
                                <i class="fab fa-whatsapp me-2 fa-lg"></i> Kirim Link via WhatsApp ke Warga
                            </a>
                            <a href="{{ route('kecamatan.umkm.index', ['tab' => 'jasa']) }}" class="btn btn-slate-100 p-3 rounded-4 fw-bold text-slate-600">
                                Selesai / Ke Daftar Jasa
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-premium rounded-4 bg-amber-50 border border-amber-100">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-amber-800 mb-1 text-sm">Peringatan Keamanan</h6>
                                <p class="text-amber-700 small mb-0 opacity-80">Link ini memberikan akses penuh tanpa PIN. Pastikan Anda hanya mengirimkannya ke nomor WhatsApp warga yang bersangkutan. <strong>Jangan berikan link ini ke orang lain.</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("copyLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Link berhasil disalin!");
        }
    </script>
    @endpush
@endsection
