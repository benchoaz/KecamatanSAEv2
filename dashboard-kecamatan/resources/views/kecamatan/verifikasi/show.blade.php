@extends('layouts.kecamatan')

@section('title', 'Detail Verifikasi')

    @section('content')
        <div class="content-header mb-4">
            <div class="header-breadcrumb">
                <a href="{{ route('verifikasi.index') }}" class="text-teal-600"><i class="fas fa-arrow-left"></i> Kembali ke
                    Inbox</a>
            </div>
            <div class="header-title">
                <h1>Detail Laporan: {{ $submission->menu->nama_menu }}</h1>
                <p class="text-muted">{{ $submission->desa->nama_desa }} &bull; {{ $submission->tahun }} &bull;
                    {{ ucfirst($submission->periode) }}
                </p>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Report Content -->
            <div class="col-lg-8">
                <div class="card bg-white border-gray-200 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-gray-800 fw-bold">Isi Laporan (Indikator)</h5>
                        <span
                            class="badge bg-teal-50 text-teal-600 border border-teal-100">{{ $submission->aspek->nama_aspek }}</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="indicator-list">
                            @foreach($submission->jawabanIndikator as $jawaban)
                                <li class="indicator-item">
                                    <div class="indicator-header">
                                        <span class="indicator-num">{{ $loop->iteration }}</span>
                                        <div class="indicator-text">{{ $jawaban->indikator->nama_indikator }}</div>
                                    </div>
                                    <div class="indicator-body">
                                        <div class="answer-box">
                                            <span class="answer-label">Jawaban:</span>
                                            <span class="answer-value">{{ $jawaban->nilai }}</span>
                                        </div>

                                        <!-- Proof Files -->
                                        @php
                                            $proofs = $submission->buktiDukung->where('indikator_id', $jawaban->indikator_id);
                                        @endphp
                                        @if($proofs->count() > 0)
                                            <div class="proof-section mt-3">
                                                <span class="proof-label">Bukti Dukung:</span>
                                                <div class="proof-list">
                                                    @foreach($proofs as $p)
                                                        <a href="{{ route('files.show', ['uuid' => $submission->uuid, 'filename' => $p->nama_file]) }}"
                                                            target="_blank" class="proof-link">
                                                            <i
                                                                class="fas {{ str_contains($p->tipe_file, 'pdf') ? 'fa-file-pdf' : 'fa-file-image' }}"></i>
                                                            {{ $p->nama_file }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar (Actions & History) -->
            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card bg-white border-gray-200 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 text-gray-800 fw-bold">Status Saat Ini</h5>
                    </div>
                    <div class="card-body text-center py-4">
                        @php
                            $statusClass = [
                                'submitted' => 'badge-warning',
                                'returned' => 'badge-info',
                                'reviewed' => 'badge-primary',
                                'approved' => 'badge-success',
                                'rejected' => 'badge-danger',
                            ][$submission->status] ?? 'badge-secondary';
                            $statusLabel = [
                                'submitted' => 'Menunggu Kasi',
                                'returned' => 'Dikembalikan ke Desa',
                                'reviewed' => 'Menunggu Camat',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ][$submission->status] ?? $submission->status;
                        @endphp
                        <div class="status-badge-lg badge {{ $statusClass }} mb-3 px-4 py-2">
                            {{ strtoupper($statusLabel) }}
                        </div>
                        <div class="text-muted small">Diajukan oleh: <span
                                class="text-gray-900 fw-bold">{{ $submission->submittedBy->name }}</span></div>
                        <div class="text-muted small">Pada: <span
                                class="text-gray-900">{{ $submission->submitted_at->format('d M Y H:i') }}</span></div>
                    </div>
                </div>

                <!-- Action Card -->
                @if(
                        $submission->status === 'submitted' && auth()->user()->can('submission.verify') ||
                        $submission->status === 'reviewed' && auth()->user()->can('submission.approve')
                    )
                    <div class="card bg-white border-teal-200 shadow-sm mb-4 border-top border-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 text-teal-800 fw-bold">Proses Laporan</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('verifikasi.process', $submission->id) }}" method="POST" id="actionForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label">Keputusan</label>
                                    <select name="status" class="form-control" id="statusSelect" required>
                                        <option value="">-- Pilih Aksi --</option>
                                        @if($submission->status === 'submitted')
                                            <option value="reviewed">Verifikasi (Teruskan ke Camat)</option>
                                            <option value="returned">Kembalikan ke Desa (Perbaikan)</option>
                                        @elseif($submission->status === 'reviewed')
                                            <option value="approved">Setujui (Selesai)</option>
                                            <option value="rejected">Tolak Laporan</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label">Catatan <span id="noteRequired"
                                            class="text-danger d-none">*</span></label>
                                    <textarea name="catatan" rows="4" class="form-control"
                                        placeholder="Masukkan alasan pengembalian/penolakan..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Simpan Keputusan</button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- History Timeline -->
                <div class="card bg-white border-gray-200 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 text-gray-800 fw-bold">Riwayat Laporan</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="timeline">
                            @foreach($submission->verifikasi as $v)
                                <li class="timeline-item">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="timeline-status">{{ ucfirst($v->to_status) }}</span>
                                            <span class="timeline-time">{{ $v->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="timeline-actor">{{ $v->verifikator->name }}
                                            ({{ strtoupper($v->role ?? $v->tipe_verifikasi) }})</div>
                                        @if($v->catatan)
                                            <div class="timeline-note">"{{ $v->catatan }}"</div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                            <!-- Initial Submission -->
                            <li class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-status text-muted">Submitted</span>
                                        <span class="timeline-time">{{ $submission->submitted_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="timeline-actor">{{ $submission->submittedBy->name }} (Operator Desa)</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endsection

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .indicator-list {
            list-style: none;
            padding: 0;
        }

        .indicator-item {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .indicator-item:last-child {
            border-bottom: none;
        }

        .indicator-header {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .indicator-num {
            background: #f1f5f9;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            flex-shrink: 0;
        }

        .indicator-text {
            font-weight: 600;
            color: #1e293b;
            line-height: 1.5;
        }

        .answer-box {
            background: #f8fafc;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border-left: 4px solid #14b8a6;
        }

        .answer-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 2px;
        }

        .answer-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.125rem;
        }

        .proof-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.875rem;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }

        .proof-link:hover {
            border-color: #14b8a6;
            color: #14b8a6;
            background: #f0fdfa;
        }

        /* Status Badges */
        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .badge-primary {
            background-color: #ccfbf1;
            color: #0f766e;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #047857;
        }

        .badge-danger {
            background-color: #ffe4e6;
            color: #be123c;
        }

        /* Timeline Styles */
        .timeline {
            list-style: none;
            padding: 1.5rem;
            position: relative;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 2.25rem;
            top: 1.5rem;
            bottom: 1.5rem;
            width: 2px;
            background: #f1f5f9;
        }

        .timeline-item {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 2rem;
        }

        .timeline-point {
            position: absolute;
            left: 0.75rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            background: #cbd5e1;
            border-radius: 50%;
            z-index: 1;
            border: 3px solid #fff;
            box-shadow: 0 0 0 1px #f1f5f9;
        }

        .timeline-status {
            font-weight: 700;
            font-size: 0.875rem;
            color: #334155;
        }

        .timeline-note {
            margin-top: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: #f8fafc;
            border-radius: 6px;
            font-style: italic;
            font-size: 0.875rem;
            color: #475569;
            border: 1px dashed #e2e8f0;
        }

        .btn-primary {
            background-color: #14b8a6;
            border-color: #14b8a6;
        }

        .btn-primary:hover {
            background-color: #0d9488;
            border-color: #0d9488;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('statusSelect')?.addEventListener('change', function () {
            const val = this.value;
            const requiredLabel = document.getElementById('noteRequired');
            if (val === 'returned' || val === 'rejected') {
                requiredLabel.classList.remove('d-none');
                document.querySelector('textarea[name="catatan"]').setAttribute('required', 'required');
            } else {
                requiredLabel.classList.add('d-none');
                document.querySelector('textarea[name="catatan"]').removeAttribute('required');
            }
        });

        document.getElementById('actionForm')?.addEventListener('submit', function (e) {
            const status = document.getElementById('statusSelect').value;
            const note = document.querySelector('textarea[name="catatan"]').value;

            if ((status === 'returned' || status === 'rejected') && !note.trim()) {
                e.preventDefault();
                alert('Catatan wajib diisi untuk tindakan ini.');
            }
        });
    </script>
@endpush