@extends('layouts.kecamatan')

@section('title', 'Detail Pengaduan WhatsApp')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('kecamatan.pelayanan.pengaduan') }}" class="btn btn-link text-slate-500 text-decoration-none p-0 mb-2 d-inline-block">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pengaduan
                    </a>
                    <h1 class="text-slate-900 fw-bold fs-3 mb-1">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        Detail Pengaduan
                    </h1>
                    {{-- PIN Display - More Prominent --}}
                    <div class="bg-gradient-teal d-inline-flex px-3 py-2 rounded-3 text-white shadow-sm mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-key text-white-50"></i>
                            <span class="small text-white-70">PIN:</span>
                            <span class="fw-bold fs-5 font-monospace" style="letter-spacing: 2px;">{{ $pengaduan->tracking_code }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    @php
                        $statusConfig = [
                            'menunggu_verifikasi' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock', 'label' => 'Menunggu Verifikasi'],
                            'diproses' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-spinner', 'label' => 'Sedang Diproses'],
                            'selesai' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-check', 'label' => 'Selesai'],
                            'ditolak' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'icon' => 'fa-times', 'label' => 'Ditolak'],
                        ];
                        $cfg = $statusConfig[$pengaduan->status] ?? $statusConfig['menunggu_verifikasi'];
                    @endphp
                    <span class="badge {{ $cfg['bg'] }} {{ $cfg['text'] }} text-sm px-3 py-2 rounded-pill">
                        <i class="fas {{ $cfg['icon'] }} me-1"></i>
                        {{ $cfg['label'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Pengaduan Info -->
        <div class="col-lg-8">
            <!-- Pengirim Card -->
            <form class="card border-0 shadow-sm mb-4" action="{{ route('kecamatan.pelayanan.pengaduan.update-sender', $pengaduan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-slate-700">
                        <i class="fas fa-user-edit me-2"></i> Informasi Pengirim (Verifikasi)
                    </h6>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Data Verifikasi
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1 d-block">Nama Lengkap</label>
                            <input type="text" name="nama_pemohon" class="form-control form-control-sm" value="{{ $pengaduan->nama ?? '' }}" placeholder="Belum disebutkan">
                        </div>
                        <div class="col-md-6">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1 d-block">No. WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control form-control-sm" value="{{ $pengaduan->whatsapp ?? '' }}" placeholder="Belum disebutkan">
                            @if($pengaduan->whatsapp)
                            <div class="mt-1">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pengaduan->whatsapp) }}" target="_blank" class="text-success text-[11px] text-decoration-none">
                                    <i class="fab fa-whatsapp me-1"></i> Chat WhatsApp
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1 d-block">NIK KTP <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control form-control-sm" value="{{ $pengaduan->nik ?? '' }}" placeholder="Masukkan 16 digit NIK">
                        </div>
                        <div class="col-md-6">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1 d-block">Desa/Kelurahan</label>
                            <select name="desa_id" class="form-select form-select-sm">
                                <option value="">-- Pilih Desa --</option>
                                @foreach($desas ?? [] as $desa)
                                <option value="{{ $desa->id }}" {{ $pengaduan->desa_id == $desa->id ? 'selected' : '' }}>
                                    {{ $desa->nama_desa }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="p-2 bg-slate-50 rounded-3 text-[11px] text-slate-500">
                                <i class="fas fa-info-circle me-1 text-primary"></i> 
                                Pastikan Anda memverifikasi data diri pelapor sebelum memproses lebih lanjut untuk menghindari laporan palsu. Edit dan klik tombol <b>Simpan Data Verifikasi</b> di kanan atas kartu ini bila diperlukan.
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Isi Pengaduan Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-slate-700">
                        <i class="fas fa-comment-alt me-2"></i> Isi Pengaduan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Jenis Layanan</label>
                        <p class="text-slate-700 mb-0">
                            <span class="badge bg-slate-100 text-slate-600">
                                {{ $pengaduan->jenis_layanan ?? 'Umum' }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Uraian</label>
                        <div class="bg-slate-50 rounded-3 p-3 mt-1">
                            <p class="text-slate-700 mb-0 whitespace-pre-line">{{ $pengaduan->uraian }}</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Waktu Pengaduan</label>
                            <p class="text-slate-700 mb-0">
                                <i class="fas fa-calendar me-1 text-slate-400"></i>
                                {{ $pengaduan->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Sumber</label>
                            <p class="mb-0">
                                <span class="badge bg-success text-white">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp Bot
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lampiran -->
            @if($pengaduan->attachments && $pengaduan->attachments->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-slate-700">
                        <i class="fas fa-paperclip me-2"></i> Lampiran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($pengaduan->attachments as $attachment)
                        <div class="col-md-4">
                            <div class="border rounded-3 p-2 text-center">
                                @if($attachment->is_image)
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                                     class="img-fluid rounded-2 mb-2" style="max-height: 120px;">
                                @else
                                <i class="fas fa-file-pdf text-3xl text-rose-500 mb-2"></i>
                                @endif
                                <p class="text-[10px] text-slate-500 mb-0">{{ $attachment->original_name }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Actions -->
        <div class="col-lg-4">
            <!-- Update Status Card -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-slate-700">
                        <i class="fas fa-edit me-2"></i> Tindak Lanjut
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kecamatan.pelayanan.pengaduan.update-status', $pengaduan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                                Status
                            </label>
                            <select name="status" class="form-select form-select-sm rounded-3">
                                <option value="{{ \App\Models\PublicService::STATUS_MENUNGGU }}" 
                                    {{ $pengaduan->status == \App\Models\PublicService::STATUS_MENUNGGU ? 'selected' : '' }}>
                                    Menunggu Verifikasi
                                </option>
                                <option value="{{ \App\Models\PublicService::STATUS_DIPROSES }}" 
                                    {{ $pengaduan->status == \App\Models\PublicService::STATUS_DIPROSES ? 'selected' : '' }}>
                                    Sedang Diproses
                                </option>
                                <option value="{{ \App\Models\PublicService::STATUS_SELESAI }}" 
                                    {{ $pengaduan->status == \App\Models\PublicService::STATUS_SELESAI ? 'selected' : '' }}>
                                    Selesai
                                </option>
                                <option value="{{ \App\Models\PublicService::STATUS_DITOLAK }}" 
                                    {{ $pengaduan->status == \App\Models\PublicService::STATUS_DITOLAK ? 'selected' : '' }}>
                                    Ditolak / Tidak Valid
                                </option>
                            </select>
                        </div>

                        <!-- Respon Publik -->
                        <div class="mb-3">
                            <label class="form-label text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                                Respon untuk Warga
                            </label>
                            <textarea name="public_response" class="form-control form-control-sm rounded-3" rows="3"
                                placeholder="Tulis respon yang akan dikirim ke warga via WhatsApp...">{{ old('public_response', $pengaduan->public_response) }}</textarea>
                            <p class="text-[9px] text-slate-400 mt-1 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Respon ini akan dikirim ke WhatsApp pengirim
                            </p>
                        </div>

                        <!-- Catatan Internal -->
                        <div class="mb-3">
                            <label class="form-label text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                                Catatan Internal
                            </label>
                            <textarea name="internal_notes" class="form-control form-control-sm rounded-3" rows="2"
                                placeholder="Catatan internal (tidak terlihat warga)...">{{ old('internal_notes', $pengaduan->internal_notes) }}</textarea>
                        </div>

                        <!-- WhatsApp Notification -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="send_whatsapp" value="1" id="sendWhatsapp" 
                                    class="form-check-input" {{ $pengaduan->whatsapp ? 'checked' : '' }}>
                                <label for="sendWhatsapp" class="form-check-label text-[11px] text-slate-600">
                                    <i class="fab fa-whatsapp text-success me-1"></i>
                                    Kirim notifikasi ke WhatsApp pengirim
                                </label>
                            </div>
                        </div>

                        <!-- Handler Info -->
                        @if($pengaduan->handler)
                        <div class="bg-slate-50 rounded-3 p-2 mb-3">
                            <p class="text-[10px] text-slate-400 mb-1">Ditangani oleh:</p>
                            <p class="text-[11px] text-slate-700 mb-0">
                                {{ $pengaduan->handler->nama_lengkap }}
                                <span class="text-slate-400">· {{ $pengaduan->handled_at?->format('d M Y H:i') }}</span>
                            </p>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary w-100 rounded-3 py-2">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-slate-700">
                        <i class="fas fa-bolt me-2"></i> Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    @if($pengaduan->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pengaduan->whatsapp) }}" 
                       target="_blank" class="btn btn-outline-success btn-sm w-100 rounded-3 mb-2">
                        <i class="fab fa-whatsapp me-1"></i> Hubungi via WhatsApp
                    </a>
                    @endif
                    <a href="{{ route('receipt.preview', $pengaduan->uuid) }}" 
                       class="btn btn-outline-slate btn-sm w-100 rounded-3">
                        <i class="fas fa-receipt me-1"></i> Lihat Struk
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection