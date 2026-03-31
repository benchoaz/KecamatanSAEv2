@extends('layouts.desa')
@section('title', 'Penilaian Seleksi - ' . $vacancy->jabatan)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('recruitment.desa.show', $vacancy->id) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-0">Penilaian & Pemeringkatan</h4>
            <small class="text-muted">Kandidat Terverifikasi: {{ $applicants->count() }} Orang</small>
        </div>
        
        @if(in_array($vacancy->status, ['ranking','submitted_to_camat','camat_review','sk_generated','completed','approved_by_bupati']))
        <form action="{{ route('recruitment.selection.ranking', $vacancy->id) }}" method="POST" class="ms-auto" id="formGenerateRanking">
            @csrf
            {{-- Tombol Generate Ranking --}}
            <button type="button" class="btn btn-warning shadow-sm" onclick="if(confirm('Kalkulasi seluruh nilai dan buat pemeringkatan akhir? Tindakan ini akan mengunci input nilai.')) document.getElementById('formGenerateRanking').submit();">
                <i class="bi bi-sort-numeric-down me-1"></i>Kalkulasi & Kunci Ranking
            </button>
            <a href="{{ route('recruitment.selection.hasil_pdf', $vacancy->id) }}" class="btn btn-success shadow-sm ms-2" target="_blank">
                <i class="bi bi-file-pdf me-1"></i>Download Hasil
            </a>
        </form>
        @endif
    </div>

    @if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    {{-- Formula Alert --}}
    <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
        <div>
            <div class="fw-bold text-info">Formula Pembobotan Sistem</div>
            <small class="text-muted">Nilai Akhir = (70% × Nilai Tertulis) + (30% × Nilai Wawancara)</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4" width="8%">Peringkat</th>
                            <th width="30%">Nama Kandidat</th>
                            <th class="text-center">Nilai Tertulis<br><small>(70%)</small></th>
                            <th class="text-center">Nilai Wawancara<br><small>(30%)</small></th>
                            <th class="text-center bg-primary bg-opacity-10 fw-bold">NILAI AKHIR<br><small>(100%)</small></th>
                            <th class="text-end pe-4" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applicants as $index => $applicant)
                        <tr class="{{ $applicant->status === 'selected' ? 'bg-success bg-opacity-10' : '' }}">
                            <td class="ps-4 fw-bold fs-5 text-center {{ $applicant->rank === 1 ? 'text-warning' : 'text-muted' }}">
                                @if($vacancy->status === 'ranking' || in_array($vacancy->status, ['submitted_to_camat','camat_review','sk_generated','completed','approved_by_bupati']))
                                    @if($applicant->rank === 1) <i class="bi bi-trophy-fill me-1"></i> @endif
                                    #{{ $applicant->rank }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $applicant->name }}</div>
                                <span class="badge {{ $applicant->status_badge }}">{{ $applicant->status_label }}</span>
                            </td>
                            <td class="text-center fs-5">{{ $applicant->score_written ?? '0.00' }}</td>
                            <td class="text-center fs-5">{{ $applicant->score_interview ?? '0.00' }}</td>
                            <td class="text-center fs-5 fw-bold bg-primary bg-opacity-10 text-primary">
                                {{ $applicant->score_total ?? '0.00' }}
                            </td>
                            <td class="text-end pe-4">
                                @if($applicant->score?->bukti_ujian_path)
                                    <a href="{{ Storage::url($applicant->score->bukti_ujian_path) }}" target="_blank" class="btn btn-outline-danger btn-sm mb-1 me-1" title="Lihat Berita Acara / Bukti Ujian">
                                        <i class="bi bi-file-pdf"></i> Bukti
                                    </a>
                                @endif

                                @if(!in_array($vacancy->status, ['ranking','submitted_to_camat','camat_review','sk_generated','completed','approved_by_bupati']))
                                    <button class="btn btn-outline-primary btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#modalScore{{ $applicant->id }}">
                                        <i class="bi bi-pencil-square me-1"></i>Input Nilai
                                    </button>
                                @endif

                                {{-- Jika Ranking selesai --}}
                                @if(in_array($vacancy->status, ['ranking', 'submitted_to_camat', 'camat_review']) && $applicant->rank === 1)
                                    <form action="{{ route('recruitment.desa.applicant.select', [$vacancy->id, $applicant->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm" title="Usulkan menjadi Perangkat" onclick="return confirm('Pilih kandidat ini sebagai Perangkat Desa terpilih?')">
                                            <i class="bi bi-check-circle me-1"></i>Pilih Usulan SK
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        {{-- Modal Input Nilai --}}
                        <div class="modal fade" id="modalScore{{ $applicant->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fw-bold">Input Nilai: {{ $applicant->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('recruitment.selection.score', $applicant->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <p class="small text-muted mb-4">Pastikan nilai telah divalidasi oleh seluruh panitia seleksi. Skala 0 - 100.</p>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nilai Ujian Tertulis <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="nilai_tertulis" class="form-control fw-bold fs-5" 
                                                           value="{{ $applicant->score_written }}" step="0.01" min="0" max="100" required>
                                                    <span class="input-group-text bg-light text-muted">/ 100</span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nilai Ujian Wawancara <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="nilai_wawancara" class="form-control fw-bold fs-5" 
                                                           value="{{ $applicant->score_interview }}" step="0.01" min="0" max="100" required>
                                                    <span class="input-group-text bg-light text-muted">/ 100</span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Catatan Penilaian (Opsional)</label>
                                                <textarea name="catatan" rows="2" class="form-control bg-light">{{ $applicant->score->catatan_penilai ?? '' }}</textarea>
                                            </div>

                                            <div class="mb-3 p-3 bg-white border border-info rounded">
                                                <label class="form-label fw-bold">
                                                    Upload Bukti Nilai / Berita Acara (PDF)
                                                    @if(!$applicant->score?->bukti_ujian_path)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="file" name="bukti_ujian_file" class="form-control" accept=".pdf" {{ $applicant->score?->bukti_ujian_path ? '' : 'required' }}>
                                                @if($applicant->score?->bukti_ujian_path)
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($applicant->score->bukti_ujian_path) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-file-pdf me-1"></i>Lihat Bukti Tersimpan
                                                        </a>
                                                        <small class="text-muted ms-2">Abaikan jika tidak ingin mengubah file.</small>
                                                    </div>
                                                @else
                                                    <div class="form-text text-muted small mt-1">
                                                        <i class="bi bi-info-circle me-1"></i> Wajib lampirkan scan resmi format PDF (Max 5MB).
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary fw-bold text-uppercase px-4">Simpan Nilai</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada kandidat yang lolos verifikasi administrasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
