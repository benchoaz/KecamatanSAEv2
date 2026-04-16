@extends('layouts.kecamatan')

@section('title', 'Verifikasi ' . ($complaint->category == 'umkm' ? 'Lapak UMKM' : 'Pekerjaan & Jasa'))

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('kecamatan.pelayanan.inbox', ['category' => 'ekonomi']) }}" class="btn btn-link text-slate-500 text-decoration-none p-0">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Inbox Ekonomi
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-emerald border-0 shadow-sm rounded-4 p-3 mb-4">
            <p class="mb-0 text-emerald-700 small fw-medium"><i class="fas fa-check-circle me-1"></i>
                {{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-rose border-0 shadow-sm rounded-4 p-3 mb-4">
            <ul class="mb-0 text-rose-700 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Review Data UMKM/Jasa -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-premium rounded-4 overflow-hidden mb-4 border border-slate-100">
                <div class="card-header @if($complaint->category == 'umkm') bg-gradient-blue @else bg-gradient-teal @endif py-4 px-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-white text-{{ $complaint->category == 'umkm' ? 'blue' : 'teal' }}-600 xs rounded-circle" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas {{ $complaint->category == 'umkm' ? 'fa-store' : 'fa-tools' }}"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold fs-6 text-white">Verifikasi Pendaftaran {{ $complaint->category == 'umkm' ? 'UMKM' : 'Jasa' }}</h5>
                                <p class="text-[10px] text-white-50 mb-0 uppercase tracking-wider">
                                    Diajukan pada: {{ $complaint->created_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>
                        <div class="bg-white/20 px-3 py-2 rounded-3 text-white shadow-sm backdrop-blur-md">
                            <span class="small fw-bold">{{ $complaint->status_label }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($workDir)
                        <div class="row g-4 border-bottom border-slate-100 pb-4 mb-4">
                            <div class="col-md-6">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Nama Usaha / Jasa</label>
                                <p class="fw-bold text-slate-800 mb-0">{{ $workDir->job_title }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Kategori</label>
                                <span class="badge bg-slate-100 text-slate-600">{{ $workDir->job_category }}</span>
                                <span class="badge bg-slate-100 text-slate-600">{{ $workDir->job_type }}</span>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Nama Pemilik / Pendaftar</label>
                                <p class="fw-medium text-slate-700 mb-0"><i class="fas fa-user-circle text-slate-300 me-2"></i>{{ $workDir->display_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Nomor WhatsApp</label>
                                <p class="fw-medium text-emerald-600 mb-0">
                                    <i class="fab fa-whatsapp me-2"></i>{{ $workDir->contact_phone }}
                                </p>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Area Layanan / Desa</label>
                                <p class="fw-medium text-slate-700 mb-0"><i class="fas fa-map-marker-alt text-rose-300 me-2"></i>{{ $workDir->service_area ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-1 d-block">Waktu Operasional</label>
                                <p class="fw-medium text-slate-700 mb-0"><i class="fas fa-clock text-blue-300 me-2"></i>{{ $workDir->service_time ?? '-' }}</p>
                            </div>

                            <div class="col-12">
                                <label class="text-[10px] text-slate-400 uppercase fw-bold tracking-wider mb-2 d-block">Deskripsi Singkat</label>
                                <div class="bg-slate-50 p-3 rounded-3 text-slate-700 text-sm border border-slate-100">
                                    {{ $workDir->short_description ?? 'Tidak ada deskripsi yang diberikan.' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Data detail usaha tidak ditemukan. Ini mungkin pendaftaran manual atau terjadi kesalahan sistem.
                            <br>
                            Uraian Pendaftaran: {{ $complaint->uraian }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action / Tndak Lanjut Panel -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 border border-slate-100 mb-4 sticky-top" style="top: 2rem;">
                <div class="card-header bg-slate-50 py-3 px-4 border-bottom border-slate-100">
                    <h5 class="mb-0 fw-bold fs-6 text-slate-800"><i class="fas fa-gavel text-slate-400 me-2"></i> Keputusan Verifikasi</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kecamatan.pelayanan.update-status', $complaint->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label text-sm fw-bold text-slate-700">Tindakan</label>
                            <select name="status" id="statusSelect" class="form-select border-slate-200 shadow-sm mb-3">
                                <option value="{{ \App\Models\PublicService::STATUS_MENUNGGU }}" {{ $complaint->status == \App\Models\PublicService::STATUS_MENUNGGU ? 'selected' : '' }}>⏳ Menunggu (Pending)</option>
                                <option value="{{ \App\Models\PublicService::STATUS_SELESAI }}" {{ $complaint->status == \App\Models\PublicService::STATUS_SELESAI ? 'selected' : '' }}>✅ SETUJUI (Aktifkan Jasa/UMKM)</option>
                                <option value="{{ \App\Models\PublicService::STATUS_DITOLAK }}" {{ $complaint->status == \App\Models\PublicService::STATUS_DITOLAK ? 'selected' : '' }}>❌ TOLAK Pendaftaran</option>
                            </select>
                        </div>
                        
                        <div class="mb-4" id="catatanContainer">
                            <label class="form-label text-sm fw-bold text-slate-700">Catatan Penolakan / Persetujuan</label>
                            <textarea name="public_response" id="public_response" rows="3" class="form-control border-slate-200 shadow-sm text-sm" placeholder="Opsional: Tuliskan pesan tambahan untuk warga..."></textarea>
                            <div class="form-text text-[11px] mt-1 text-slate-400">Pesan ini akan disisipkan di dalam WhatsApp otomatis yang dikirim ke warga.</div>
                        </div>

                        <div class="alert bg-blue-50 border border-blue-100 p-3 rounded-3 mb-4">
                            <div class="form-check form-switch d-flex align-items-center mb-0 gap-2">
                                <input class="form-check-input mt-0" type="checkbox" name="send_whatsapp_notification" id="sendWaCheck" value="1" checked style="width: 2.5em; height: 1.25em;">
                                <label class="form-check-label text-sm fw-medium text-blue-800" for="sendWaCheck">
                                    Kirim Notifikasi Otomatis (WhatsApp)
                                </label>
                            </div>
                            <p class="text-[10px] text-blue-600 mt-2 mb-0 ms-1">
                                Jika disetujui, warga otomatis menerima link profil toko via WhatsApp. Jika ditolak, warga mendapat pemberitahuan.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow-sm hover-up">
                            Simpan & Kirim Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
