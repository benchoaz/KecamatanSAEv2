@use('Illuminate\Support\Facades\Storage')
@extends('layouts.desa')

@section('title', 'Detail Musyawarah Desa')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center mb-4 justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('desa.musdes.index') }}" class="btn btn-light rounded-circle shadow-sm me-3 border">
                        <i class="fas fa-arrow-left text-secondary"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $submission->judul }}</h4>
                        <span class="badge bg-slate-100 text-slate-600 border me-1"><i class="fas fa-calendar me-1"></i> TA
                            {{ $submission->periode }}</span>
                        @if($submission->status == 'submitted')
                            <span class="badge bg-sky-100 text-sky-700">Terkirim:
                                {{ $submission->submitted_at->format('d M Y') }}</span>
                        @elseif($submission->status == 'completed')
                            <span class="badge bg-emerald-100 text-emerald-700">Verifikasi Selesai</span>
                        @endif
                    </div>
                </div>

                <!-- Jika completed, tampilkan sertifikat/tanda terima (opsional) -->
                @if($submission->status == 'completed')
                    <button class="btn btn-outline-success fw-bold rounded-pill">
                        <i class="fas fa-print me-2"></i> Cetak Bukti Lapor
                    </button>
                @endif
            </div>

            <div class="row g-4">
                <div class="col-md-8">
                    <!-- Detail Data -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-slate-800 mb-4 pb-2 border-bottom">Informasi Kegiatan</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold">Jenis Musdes</label>
                                    <p class="fw-bold fs-5 text-dark">{{ $details['jenis_musdes'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold">Tanggal Pelaksanaan</label>
                                    <p class="fw-bold fs-5 text-dark">
                                        {{ isset($details['tanggal_pelaksanaan']) ? \Carbon\Carbon::parse($details['tanggal_pelaksanaan'])->isoFormat('dddd, D MMMM Y') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-5">
                                    <label class="small text-muted text-uppercase fw-bold">Lokasi</label>
                                    <p class="fw-bold text-dark">{{ $details['lokasi'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted text-uppercase fw-bold">Undangan</label>
                                    <p class="fw-bold text-dark">{{ $details['jumlah_undangan'] ?? '-' }} Orang</p>
                                </div>
                                @if(!empty($details['keterangan']))
                                    <div class="col-md-12">
                                        <label class="small text-muted text-uppercase fw-bold">Keterangan</label>
                                        <p class="text-secondary bg-light p-3 rounded-3">{{ $details['keterangan'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Galeri Foto -->
                    @if($submission->files->where('file_type', 'foto_kegiatan')->count() > 0)
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-800 mb-3">Dokumentasi Foto</h6>
                                <div class="row g-2">
                                    @foreach($submission->files->where('file_type', 'foto_kegiatan') as $file)
                                        <div class="col-md-4 col-6">
                                            <div class="ratio ratio-4x3 bg-light rounded-3 overflow-hidden border">
                                                <a href="{{ Storage::url($file->file_path) }}" target="_blank">
                                                    <img src="{{ Storage::url($file->file_path) }}"
                                                        class="object-fit-cover w-100 h-100 hover-zoom" alt="Foto">
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <!-- Dokumen -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-slate-800 mb-4">Dokumen Lampiran</h6>

                            <div class="list-group list-group-flush">
                                <!-- Helper Function for display -->
                                @foreach($submission->files->whereIn('file_type', ['berita_acara', 'daftar_hadir']) as $file)
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 px-2 border-bottom-0">
                                        <div class="bg-light p-2 rounded-circle">
                                            @if(str_contains($file->file_type, 'berita'))
                                                <i class="fas fa-file-pdf text-danger fs-5"></i>
                                            @else
                                                <i class="fas fa-file-image text-primary fs-5"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-dark small">
                                                {{ ucwords(str_replace('_', ' ', $file->file_type)) }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Klik untuk lihat</div>
                                        </div>
                                        <i class="fas fa-external-link-alt text-slate-300 small"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- History Log (Timeline) -->
                    @if($submission->logs->count() > 0)
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-slate-800 mb-4">Riwayat Status</h6>
                                <div class="timeline ps-2 border-start ms-2">
                                    @foreach($submission->logs as $log)
                                        <div class="position-relative ps-4 mb-4">
                                            <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white border border-2 border-primary"
                                                style="width: 12px; height: 12px; margin-left: -1px;"></div>
                                            <p class="mb-0 fw-bold small text-dark">{{ ucfirst($log->action) }}</p>
                                            <p class="text-muted small mb-1">{{ $log->created_at->format('d M Y H:i') }}</p>
                                            @if($log->note)
                                                <div class="p-2 bg-rose-50 text-rose-700 rounded small mt-1 border border-rose-100">
                                                    {{ $log->note }}
                                                </div>
                                            @endif
                                            <div class="small text-slate-400 fst-italic">Oleh:
                                                {{ $log->user->nama_lengkap ?? 'Sistem' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-zoom:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .bg-sky-100 {
            background-color: #e0f2fe;
        }

        .text-sky-700 {
            color: #0369a1;
        }

        .bg-emerald-100 {
            background-color: #d1fae5;
        }

        .text-emerald-700 {
            color: #047857;
        }
    </style>
@endsection