@extends('layouts.kecamatan')

@section('title', 'Detail ' . $complaint->category_label . ' #' . $complaint->uuid)

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <a href="{{ route('kecamatan.pelayanan.inbox') }}" class="btn btn-link text-slate-500 text-decoration-none p-0">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Inbox
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-emerald border-0 shadow-sm rounded-4 p-3 mb-4">
                <p class="mb-0 text-emerald-700 small fw-medium"><i class="fas fa-check-circle me-1"></i>
                    {{ session('success') }}</p>
            </div>
        @endif

        <div class="row">
            <!-- Complaint Content -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4 border border-slate-100">
                    <div class="card-header bg-white py-4 px-4 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box icon-box-primary xs">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold fs-6">Dokumen {{ $complaint->category_label }}</h5>
                                    <p class="text-[10px] text-slate-400 mb-0 uppercase tracking-wider">
                                        UUID: {{ $complaint->uuid }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                {{-- PIN Display - More Prominent --}}
                                <div class="bg-gradient-teal px-3 py-2 rounded-3 text-white shadow-sm">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-key text-white-50"></i>
                                        <span class="small text-white-70">PIN:</span>
                                        <span class="fw-bold fs-5 font-monospace" style="letter-spacing: 2px;">{{ $complaint->tracking_code }}</span>
                                    </div>
                                </div>
                                {{-- PENDING FEATURE: Uncomment when receipt and qr routes are implemented
                                <a href="{{ route('receipt.download', $complaint->uuid) }}" 
                                   class="btn btn-sm btn-primary rounded-3" 
                                   target="_blank"
                                   title="Download Struk PDF">
                                    <i class="fas fa-download me-1"></i> Struk
                                </a>
                                <a href="{{ route('qr.generate', $complaint->uuid) }}" 
                                   class="btn btn-sm btn-outline-primary rounded-3" 
                                   target="_blank"
                                   title="Lihat QR Code">
                                    <i class="fas fa-qrcode"></i>
                                </a>
                                --}}
                                <span class="text-slate-400 small">
                                    {{ $complaint->created_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $rawUraian = $complaint->uraian;
                            
                            // Extract Instansi Tujuan
                            $tujuan = '-';
                            if (preg_match('/\[Tujuan:\s*(.*?)\]/', $rawUraian, $matches)) {
                                $tujuan = $matches[1];
                            }

                            // Extract Privacy flags
                            $isAnonim = str_contains($rawUraian, '[ANONIM]');
                            $isRahasia = str_contains($rawUraian, '[RAHASIA]');

                            // Clean Uraian (remove all tags at start)
                            $cleanUraian = preg_replace('/^(\[.*?\]\s*)+/', '', $rawUraian);
                        @endphp

                        <div class="row g-4 mb-5">
                            <div class="col-md-6 col-lg-3">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Nama Pemohon</label>
                                <p class="fw-bold text-teal-700 mb-0 small">
                                    {{ $complaint->nama_pemohon ?? 'Sistem Bot' }}
                                    @if($isAnonim)
                                        <span class="badge bg-slate-200 text-slate-600 text-[9px] ms-1">ANONIM</span>
                                    @endif
                                </p>
                                @if($complaint->nik)
                                    <small class="text-[9px] text-slate-500 font-medium">NIK: {{ $complaint->nik }}</small>
                                @endif
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Jenis Layanan & Tujuan</label>
                                <p class="fw-bold text-slate-800 mb-0 small">{{ $complaint->jenis_layanan }}</p>
                                <span class="text-[10px] text-slate-500 block mt-1"><i class="fas fa-building me-1 opacity-50"></i> {{ $tujuan }}</span>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Privasi & Keamanan</label>
                                <div class="d-flex gap-1">
                                    @if($isRahasia)
                                        <span class="badge bg-rose-100 text-rose-600 text-[9px] border border-rose-200"><i class="fas fa-lock me-1"></i> RAHASIA</span>
                                    @else
                                        <span class="badge bg-emerald-100 text-emerald-600 text-[9px] border border-emerald-200"><i class="fas fa-lock-open me-1"></i> PUBLIK</span>
                                    @endif
                                    @if($isAnonim)
                                        <span class="badge bg-slate-100 text-slate-600 text-[9px] border border-slate-200"><i class="fas fa-user-secret me-1"></i> SAMARKAN</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Kontak Pelapor</label>
                                @php
                                    $waRaw = preg_replace('/[^0-9]/', '', $complaint->whatsapp);
                                    // Normalize to 0-prefix for display (Indonesian style)
                                    $waDisplay = $waRaw;
                                    if (str_starts_with($waRaw, '62')) {
                                        $waDisplay = '0' . substr($waRaw, 2);
                                    }
                                    // Build wa.me link (needs 62 prefix)
                                    $waLink = str_starts_with($waRaw, '62') ? $waRaw : '62' . ltrim($waRaw, '0');
                                @endphp
                                <a href="https://wa.me/{{ $waLink }}" target="_blank"
                                    class="text-emerald-600 fw-bold text-decoration-none small">
                                    <i class="fab fa-whatsapp me-1"></i> {{ $waDisplay }}
                                </a>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-2 d-block">
                                {{ $complaint->category == 'pelayanan' ? 'Uraian Permohonan' : 'Uraian / Aspirasi Masyarakat' }}
                            </label>
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-4 text-slate-700 leading-relaxed fs-6">
                                {{ $cleanUraian }}
                            </div>
                        </div>

                        @php $allAttachments = $complaint->attachments; @endphp
                        @if($allAttachments->count() > 0)
                            <div class="mb-0">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-3 d-block">Lampiran Berkas (${{ $allAttachments->count() }})</label>
                                <div class="row g-3">
                                    @foreach($allAttachments as $attachment)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="card border-slate-100 shadow-sm rounded-4 overflow-hidden h-100">
                                                <div class="bg-slate-50 border-bottom p-2 text-center" style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                                    @php $isPdf = str_ends_with(strtolower($attachment->file_path), '.pdf'); @endphp
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                                        @if($isPdf)
                                                            <i class="fas fa-file-pdf text-rose-500 fs-1"></i>
                                                        @else
                                                            <img src="{{ asset('storage/' . $attachment->file_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 80px; width: auto; object-fit: contain;">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="p-2">
                                                    <p class="mb-0 text-[10px] fw-bold text-slate-700 truncate" title="{{ $attachment->label }}">
                                                        {{ $attachment->label }}
                                                    </p>
                                                    <small class="text-[8px] text-slate-400 truncate d-block">{{ $attachment->original_name }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($complaint->file_path_1 || $complaint->file_path_2)
                            <!-- Fallback for legacy files -->
                            <div class="mb-0">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-3 d-block">Lampiran Berkas (Legacy)</label>
                                <div class="d-flex gap-3">
                                    @foreach(['file_path_1', 'file_path_2'] as $field)
                                        @if($complaint->$field)
                                            @php 
                                                $path = $complaint->$field;
                                                $isPdf = str_ends_with(strtolower($path), '.pdf');
                                            @endphp
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                                class="attachment-preview shadow-sm rounded-4 border border-slate-200 overflow-hidden bg-slate-100 d-flex flex-column align-items-center justify-content-center text-decoration-none p-0">
                                                @if($isPdf)
                                                    <i class="fas fa-file-pdf text-rose-500 fs-2 mb-1"></i>
                                                    <span class="text-[8px] fw-bold text-slate-500 tracking-tighter">LIHAT PDF</span>
                                                @else
                                                    <img src="{{ asset('storage/' . $path) }}"
                                                        class="object-cover w-100 h-100">
                                                @endif
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($complaint->effective_public_response)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-emerald-100 bg-emerald-50/10">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-comment-dots text-emerald-500"></i>
                                <h6 class="mb-0 fw-bold text-emerald-900 small">Respon Publik (Yang Dilihat Masyarakat)</h6>
                            </div>
                            <p class="text-slate-700 mb-0 small leading-relaxed">{{ $complaint->effective_public_response }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Follow-up Sidebar -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4 border border-slate-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-pen-nib text-primary"></i>
                            <h6 class="mb-0 fw-bold fs-6">Form Tindak Lanjut</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('kecamatan.pelayanan.update-status', $complaint->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <select name="status" class="form-select bg-slate-50 border-slate-200 rounded-3 small"
                                    required>
                                    <option value="{{ \App\Models\PublicService::STATUS_MENUNGGU }}" {{ $complaint->status == \App\Models\PublicService::STATUS_MENUNGGU ? 'selected' : '' }}">
                                        Menunggu Verifikasi
                                    </option>
                                    <option value="{{ \App\Models\PublicService::STATUS_DIPROSES }}" {{ $complaint->status == \App\Models\PublicService::STATUS_DIPROSES ? 'selected' : '' }}>
                                        Sedang Diproses
                                    </option>
                                    <option value="{{ \App\Models\PublicService::STATUS_SELESAI }}" {{ $complaint->status == \App\Models\PublicService::STATUS_SELESAI ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                    <option value="{{ \App\Models\PublicService::STATUS_DITOLAK }}" {{ $complaint->status == \App\Models\PublicService::STATUS_DITOLAK ? 'selected' : '' }}>
                                        Ditolak / Tidak Valid
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-700">Tanggapan Untuk Publik</label>
                                <textarea name="public_response"
                                    class="form-control bg-slate-50 border-slate-200 rounded-3 h-24 small"
                                    placeholder="Tuliskan jawaban yang akan tampil di sisi publik/masyarakat...">{{ old('public_response', $complaint->public_response) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-700">Catatan Internal Petugas</label>
                                <textarea name="internal_notes"
                                    class="form-control bg-slate-50 border-slate-200 rounded-3 h-24 small"
                                    placeholder="Catatan koordinasi internal (tidak terlihat publik)...">{{ old('internal_notes', $complaint->internal_notes) }}</textarea>
                            </div>

                            {{-- Completion Tracking (NEW) --}}
                            <div class="border-top border-slate-100 pt-4 mb-4">
                                <label class="form-label small fw-bold text-slate-700 mb-3">
                                    <i class="fas fa-check-circle text-success me-1"></i> Penyelesaian Berkas
                                </label>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-slate-600">Jenis Penyelesaian</label>
                                    <select name="completion_type" id="completionType" class="form-select bg-slate-50 border-slate-200 rounded-3 small">
                                        <option value="">Belum Selesai</option>
                                        <option value="digital" {{ old('completion_type', $complaint->completion_type) == 'digital' ? 'selected' : '' }}>
                                            📄 Digital (Download PDF)
                                        </option>
                                        <option value="physical" {{ old('completion_type', $complaint->completion_type) == 'physical' ? 'selected' : '' }}>
                                            🏢 Fisik (Ambil di Kantor)
                                        </option>
                                        <option value="whatsapp" {{ old('completion_type', $complaint->completion_type) == 'whatsapp' ? 'selected' : '' }}>
                                            💬 WhatsApp (Jawaban via WA)
                                        </option>
                                    </select>
                                </div>

                                {{-- Digital: Upload PDF --}}
                                <div id="digitalSection" class="mb-3" style="display: {{ old('completion_type', $complaint->completion_type) == 'digital' ? 'block' : 'none' }}">
                                    <label class="form-label small fw-bold text-slate-600">Upload Hasil (PDF) <span id="pdfRequiredMark" class="text-danger" style="display:none;">*</span></label>
                                    <input type="file" name="result_file" accept=".pdf" class="form-control form-control-sm bg-slate-50 border-slate-200 rounded-3">
                                    @error('result_file')
                                        <div class="text-danger text-[11px] fw-bold mt-1"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                                    @enderror
                                    @if($complaint->result_file_path)
                                        <div class="mt-3 p-2 bg-white border border-emerald-100 rounded-3 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-file-pdf text-rose-500 fs-5"></i>
                                                <div>
                                                    <a href="{{ asset('storage/' . $complaint->result_file_path) }}" target="_blank" class="text-emerald-700 text-[11px] fw-bold text-decoration-none hover:underline">
                                                        Lihat File Saat Ini
                                                    </a>
                                                </div>
                                            </div>
                                            <span class="badge bg-emerald-50 text-emerald-600 text-[9px] border border-emerald-200">Tersimpan</span>
                                        </div>
                                        <small class="text-muted d-block mt-1 text-[10px]"><i class="fas fa-info-circle me-1"></i>Abaikan input file jika tidak ingin mengganti PDF lama. Mengunggah baru akan menimpa file sebelumnya.</small>
                                    @else
                                        <small class="text-muted d-block mt-1">File ini bisa didownload oleh masyarakat via chat WA.</small>
                                    @endif
                                </div>

                                {{-- WhatsApp: Text Answer --}}
                                <div id="whatsappSection" class="mb-3" style="display: {{ old('completion_type', $complaint->completion_type) == 'whatsapp' ? 'block' : 'none' }}">
                                    <div class="alert border-0 rounded-3 p-3 mb-3" style="background: #e7f8ef;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fab fa-whatsapp text-success fs-5"></i>
                                            <div>
                                                <p class="mb-0 fw-bold small text-success">Jawaban via WhatsApp</p>
                                                <p class="mb-0 text-[10px] text-slate-500">Teks di bawah akan dikirim langsung ke nomor WA pemohon sebagai jawaban resmi.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="form-label small fw-bold text-slate-600">Teks Jawaban WA <span class="text-danger">*</span></label>
                                    <textarea name="wa_reply_text" id="waReplyText" rows="5"
                                        class="form-control form-control-sm bg-slate-50 border-slate-200 rounded-3 font-monospace"
                                        placeholder="Tuliskan jawaban yang akan dikirim via WhatsApp...">{{ old('wa_reply_text', $complaint->completion_type == 'whatsapp' ? $complaint->public_response : '') }}</textarea>
                                    <small class="text-muted d-block mt-1 text-[10px]"><i class="fas fa-info-circle me-1"></i>Pastikan toggle notifikasi WA di bawah dalam keadaan aktif agar pesan terkirim.</small>
                                </div>

                                {{-- Physical: Pickup Info --}}
                                <div id="physicalSection" style="display: {{ old('completion_type', $complaint->completion_type) == 'physical' ? 'block' : 'none' }}">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-slate-600">Siap Diambil Tanggal</label>
                                        <input type="datetime-local" name="ready_at" 
                                            value="{{ old('ready_at', $complaint->ready_at?->format('Y-m-d\TH:i')) }}"
                                            class="form-control form-control-sm bg-slate-50 border-slate-200 rounded-3">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-slate-600">Ambil ke Petugas</label>
                                        <input type="text" name="pickup_person" 
                                            value="{{ old('pickup_person', $complaint->pickup_person) }}"
                                            placeholder="Contoh: Bapak Slamet (Loket 2)"
                                            class="form-control form-control-sm bg-slate-50 border-slate-200 rounded-3">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-slate-600">Catatan Pengambilan</label>
                                        <textarea name="pickup_notes" rows="2"
                                            class="form-control form-control-sm bg-slate-50 border-slate-200 rounded-3"
                                            placeholder="Contoh: Bawa KTP asli, Jam operasional 08:00-14:00">{{ old('pickup_notes', $complaint->pickup_notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- WhatsApp Notification Toggle --}}
                            <div class="mb-4 d-flex align-items-center justify-content-between p-3 bg-slate-50 border border-slate-200 rounded-3 shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box icon-box-emerald xs bg-emerald-100 text-emerald-600 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-slate-700 small">Kirim Notifikasi WA</h6>
                                        <p class="mb-0 text-[10px] text-slate-500">Beritahu warga pemohon pembaruan ini</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0 d-flex align-items-center">
                                    <input class="form-check-input mt-0" type="checkbox" name="send_whatsapp_notification" value="1" id="sendWaToggle" checked style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold shadow-sm py-2">
                                Simpan Tindak Lanjut
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Quick Action: Bantu Daftarkan UMKM --}}
                @if($complaint->category == 'umkm' || (str_contains(strtolower($complaint->jenis_layanan), 'umkm') && !str_contains(strtolower($complaint->jenis_layanan), 'loker')))
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-amber-100 bg-amber-50/10 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-store-alt text-amber-500"></i>
                                <h6 class="mb-0 fw-bold text-amber-900 small">Aksi Cepat: Bantuan UMKM</h6>
                            </div>
                            <p class="text-slate-600 mb-3 small">Permintaan ini terdeteksi sebagai bantuan pendaftaran UMKM. Klik tombol di bawah untuk langsung memproses dengan data yang sudah terisi otomatis.</p>
                            <a href="{{ route('kecamatan.umkm.create') }}?from_inbox={{ $complaint->id }}&nama={{ urlencode($complaint->nama_pemohon) }}&wa={{ $complaint->whatsapp }}&desa={{ $complaint->desa_id ?? '' }}" 
                                class="btn btn-warning w-100 rounded-3 fw-bold shadow-sm py-2">
                                <i class="fas fa-hands-helping me-2"></i> Bantu Daftarkan UMKM Sekarang
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Quick Action: Bantu Terbitkan Loker --}}
                @if($complaint->category == 'loker' || str_contains(strtolower($complaint->jenis_layanan), 'loker'))
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-emerald-100 bg-emerald-50/10 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-briefcase text-emerald-500"></i>
                                <h6 class="mb-0 fw-bold text-emerald-900 small">Aksi Cepat: Papan Loker</h6>
                            </div>
                            <p class="text-slate-600 mb-3 small">Permintaan ini adalah pendaftaran lowongan kerja. Klik tombol di bawah untuk menerbitkan ke papan informasi publik.</p>
                            <a href="{{ route('kecamatan.loker.create') }}?from_inbox={{ $complaint->id }}&title={{ urlencode($complaint->nama_pemohon) }}&wa={{ $complaint->whatsapp }}&desa_id={{ $complaint->desa_id ?? '' }}" 
                                class="btn btn-success w-100 rounded-3 fw-bold shadow-sm py-2">
                                <i class="fas fa-bullhorn me-2"></i> Bantu Terbitkan Loker Sekarang
                            </a>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-slate-100">
                    <div class="card-body p-4 text-center">
                        <div
                            class="w-12 h-12 bg-slate-100 rounded-circle d-flex align-items-center justify-center text-slate-400 mx-auto mb-3">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <p class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1">Dikelola Oleh</p>
                        <p class="fw-bold text-slate-800 mb-1 small">
                            {{ $complaint->handler ? $complaint->handler->name : 'Belum Diproses' }}
                        </p>
                        @if($complaint->handled_at)
                            <p class="text-[10px] text-slate-400 mb-0">
                                Update: {{ $complaint->handled_at->format('d M Y, H:i') }} WIB</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .attachment-preview {
            width: 100px;
            height: 100px;
            display: block;
            transition: transform 0.2s;
        }

        .attachment-preview:hover {
            transform: scale(1.05);
        }
    </style>

    <script>
        // Toggle completion sections
        const statusSelect = document.querySelector('select[name="status"]');
        const completionSelect = document.getElementById('completionType');
        const pdfInput = document.querySelector('input[name="result_file"]');
        const pdfRequiredMark = document.getElementById('pdfRequiredMark');
        const waReplyText = document.getElementById('waReplyText');
        const hasExistingPdf = {!! $complaint->result_file_path ? 'true' : 'false' !!};

        function checkRequirements() {
            const isSelesai = statusSelect.value === 'Selesai';
            const completionVal = completionSelect?.value;

            const digitalSection  = document.getElementById('digitalSection');
            const physicalSection = document.getElementById('physicalSection');
            const whatsappSection = document.getElementById('whatsappSection');

            // Hide all sections first
            digitalSection.style.display  = 'none';
            physicalSection.style.display = 'none';
            whatsappSection.style.display = 'none';
            pdfInput.removeAttribute('required');
            if (pdfRequiredMark) pdfRequiredMark.style.display = 'none';
            if (waReplyText) waReplyText.removeAttribute('required');

            if (completionVal === 'digital') {
                digitalSection.style.display = 'block';
                // Require PDF if Selesai + no existing PDF
                if (isSelesai && !hasExistingPdf) {
                    pdfInput.setAttribute('required', 'required');
                    if (pdfRequiredMark) pdfRequiredMark.style.display = 'inline';
                }
            } else if (completionVal === 'physical') {
                physicalSection.style.display = 'block';
            } else if (completionVal === 'whatsapp') {
                whatsappSection.style.display = 'block';
                if (waReplyText) waReplyText.setAttribute('required', 'required');
            }
        }

        completionSelect?.addEventListener('change', checkRequirements);
        statusSelect?.addEventListener('change', checkRequirements);
        checkRequirements(); // run once on load
    </script>
@endsection