@extends('layouts.kecamatan')

@section('title', 'Perencanaan Desa (Musrenbang)')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ auth()->user()->desa_id ? route('desa.pemerintahan.index') : route('kecamatan.pemerintahan.index') }}"
                class="btn btn-xs btn-light rounded-pill px-3 text-secondary text-decoration-none border shadow-sm">
                <i class="fas fa-arrow-left-long me-2"></i> Kembali ke Menu Utama
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-bold text-primary-900 mb-1">Monev Perencanaan Desa</h2>
                <p class="text-tertiary mb-0">
                    @if($desa_id)
                        <i class="fas fa-calendar-check me-1"></i> Evaluasi Pelaksanaan Musrenbang Desa & Usulan Prioritas.
                    @else
                        <i class="fas fa-map-location-dot me-1"></i> Dashboard Monitoring Kepatuhan Perencanaan & Penganggaran
                        Desa.
                    @endif
                </p>
            </div>
            @if($desa_id && auth()->user()->isOperatorDesa())
                <button class="btn btn-brand-600 text-white rounded-pill px-4 shadow-premium" data-bs-toggle="modal"
                    data-bs-target="#addPerencanaanModal">
                    <i class="fas fa-calendar-plus me-2"></i> Input Musrenbang
                </button>
            @endif
        </div>
    </div>

    @if(!$desa_id)
        <div class="card bg-white border-gray-200 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Nama Desa</th>
                            <th class="text-center">Status Tahunan ({{ date('Y') }})</th>
                            <th class="text-center">Total Dokumen</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desas as $index => $desa)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-slate-700"> Desa {{ $desa->nama_desa }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <span
                                            class="badge rounded-pill {{ $desa->perencanaan_count > 0 ? 'bg-success' : 'bg-light text-muted' }}"
                                            title="Musdes/RKP">
                                            <i class="fas fa-check-circle me-1"></i> RKP
                                        </span>
                                        <span class="badge rounded-pill bg-light text-muted" title="APBDes">
                                            <i class="fas fa-circle me-1"></i> APBDes
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-soft text-primary px-3 py-2"
                                        style="font-size: 0.85rem;">{{ $desa->perencanaan_count }} Dokumen</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ url()->current() }}?desa_id={{ $desa->id }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Timeline Tahunan Perencanaan (Infographic Based) -->
        <div class="card bg-white border-gray-200 shadow-sm mb-5 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 text-primary">
                <h6 class="mb-0 fw-bold"><i class="fas fa-sync-alt me-2"></i> Siklus Perencanaan & Penganggaran Tahunan</h6>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="cycle-timeline">
                <div class="timeline-line"></div>

                <!-- Step 1: MUSDES -->
                <div
                    class="timeline-step {{ $currentPhase == 'musdes' ? 'active' : '' }} {{ (date('n') > 6) ? 'completed' : '' }}">
                    <div class="step-icon">1</div>
                    <div class="step-content">
                        <div class="step-title">MUSDES</div>
                        <div class="step-period">Januari - Juni</div>
                        <div class="step-desc">Musyawarah Desa (RKP-Desa)</div>
                        <div class="step-legal">Permendagri 114 Pasal 31</div>
                    </div>
                </div>

                <!-- Step 2: RKP DESA -->
                <div
                    class="timeline-step {{ $currentPhase == 'rkp' ? 'active' : '' }} {{ (date('n') > 9) ? 'completed' : '' }}">
                    <div class="step-icon">2</div>
                    <div class="step-content">
                        <div class="step-title">RKP DESA</div>
                        <div class="step-period">Juli - September</div>
                        <div class="step-desc">Penyusunan RKP Desa</div>
                        <div class="step-legal">Penetapan Max 30 Sept</div>
                    </div>
                </div>

                <!-- Step 3: APB DESA -->
                <div
                    class="timeline-step {{ $currentPhase == 'apbdes' ? 'active' : '' }} {{ (date('n') > 12) ? 'completed' : '' }}">
                    <div class="step-icon">3</div>
                    <div class="step-content">
                        <div class="step-title">APB DESA</div>
                        <div class="step-period">Oktober - Desember</div>
                        <div class="step-desc">Penyusunan APB Desa</div>
                        <div class="step-legal">Penetapan Max 31 Des</div>
                    </div>
                </div>

                <!-- Step 4: REALISASI -->
                <div class="timeline-step">
                    <div class="step-icon">4</div>
                    <div class="step-content">
                        <div class="step-title">REALISASI</div>
                        <div class="step-period">Januari - Desember</div>
                        <div class="step-desc">Pelaksanaan APB Desa</div>
                        <div class="step-legal">Tahun Berjalan</div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="row g-4">
            @forelse($perencanaan as $p)
                <div class="col-md-6 col-lg-4">
                    <div class="card planning-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="year-badge">{{ $p->tahun }}</span>
                            @php
                                $statusClass = [
                                    'draft' => 'bg-secondary',
                                    'lengkap' => 'bg-info',
                                    'verified' => 'bg-success',
                                    'revision' => 'bg-warning',
                                ][$p->status_administrasi] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ strtoupper($p->status_administrasi) }}</span>
                        </div>
                        <div class="card-body">
                            <h5 class="mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> {{ $p->lokasi }}</h5>
                            <p class="text-muted small mb-3">{{ $p->tanggal_kegiatan->format('d M Y') }}</p>

                            <div class="stats-grid mb-3">
                                <div class="stat-box">
                                    <span class="stat-val">{{ $p->usulan_count }}</span>
                                    <span class="stat-label">Usulan</span>
                                </div>
                                <div class="stat-box">
                                    <span class="stat-val"><i
                                            class="fas fa-file-pdf {{ $p->file_ba ? 'text-success' : 'text-muted' }}"></i></span>
                                    <span class="stat-label">BA</span>
                                </div>
                                <div class="stat-box">
                                    <span class="stat-val"><i
                                            class="fas fa-image {{ $p->file_foto ? 'text-primary' : 'text-muted' }}"></i></span>
                                    <span class="stat-label">Foto</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('kecamatan.pemerintahan.detail.perencanaan.show', $p->id) }}"
                                class="btn btn-sm btn-outline-light">Detail Usulan</a>
                            <button class="btn btn-sm btn-icon"><i class="fas fa-edit"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card text-center py-5 bg-white border-gray-200 shadow-sm">
                        <i class="fas fa-calendar-alt fa-3x mb-3 text-gray-300"></i>
                        <p class="text-muted">Belum ada riwayat Musrenbang yang diinput.</p>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Modal Input Musrenbang -->
    <div class="modal fade" id="addPerencanaanModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Hasil Musrenbang Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('kecamatan.pemerintahan.detail.perencanaan.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="desa_id" value="{{ $desa_id }}">
                    <div class="modal-body">
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tahun Perencanaan</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal Kegiatan</label>
                                <input type="date" name="tanggal_kegiatan" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Lokasi Musyawarah</label>
                                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Balai Desa"
                                    required>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Berita Acara (PDF)</label>
                                <input type="file" name="file_ba" class="form-control" accept="application/pdf" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Daftar Hadir (PDF/JPG)</label>
                                <input type="file" name="file_absensi" class="form-control" accept="application/pdf,image/*"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Foto Kegiatan</label>
                                <input type="file" name="file_foto" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Daftar Usulan Prioritas</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addUsulanBtn">
                                <i class="fas fa-plus me-1"></i> Tambah Usulan
                            </button>
                        </div>

                        <div id="usulanContainer">
                            <div class="row g-2 mb-3 usulan-item p-3 border rounded">
                                <div class="col-md-3">
                                    <label class="small text-muted">Bidang</label>
                                    <select name="usulan[0][bidang]" class="form-select form-select-sm" required>
                                        <option value="Infrastruktur">Infrastruktur</option>
                                        <option value="Ekonomi">Ekonomi</option>
                                        <option value="Sosial Budaya">Sosial Budaya</option>
                                        <option value="Pemerintahan">Pemerintahan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Uraian Usulan</label>
                                    <input type="text" name="usulan[0][uraian]" class="form-control form-control-sm"
                                        required>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Prioritas</label>
                                    <select name="usulan[0][prioritas]" class="form-select form-select-sm">
                                        <option value="tinggi">Tinggi</option>
                                        <option value="sedang" selected>Sedang</option>
                                        <option value="rendah">Rendah</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-usulan"
                                        disabled><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Hasil Musrenbang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let usulanIndex = 1;
        document.getElementById('addUsulanBtn').addEventListener('click', function () {
            const container = document.getElementById('usulanContainer');
            const newItem = container.querySelector('.usulan-item').cloneNode(true);

            // Update names
            newItem.querySelectorAll('[name]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace('[0]', '[' + usulanIndex + ']'));
                input.value = '';
            });

            newItem.querySelector('.remove-usulan').removeAttribute('disabled');
            container.appendChild(newItem);
            usulanIndex++;
        });

        document.getElementById('usulanContainer').addEventListener('click', function (e) {
            if (e.target.closest('.remove-usulan')) {
                e.target.closest('.usulan-item').remove();
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .planning-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .planning-card:hover {
            transform: scale(1.02);
            border-color: #14b8a6;
        }

        .year-badge {
            background: rgba(20, 184, 166, 0.1);
            color: #0d9488;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 12px;
        }

        .stat-box {
            text-align: center;
        }

        .stat-val {
            display: block;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .stat-label {
            font-size: 0.65rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .planning-card .card-footer {
            background: #f9fafb;
            border-top: 1px solid #e2e8f0;
        }

        .planning-card .btn-outline-light {
            border-color: #e2e8f0;
            color: #64748b;
        }

        .planning-card .btn-outline-light:hover {
            background: #f1f5f9;
            color: #1e293b;
            border-color: #cbd5e1;
        }

        /* Cycle Timeline Styles */
        .cycle-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 0 40px;
        }

        .timeline-line {
            position: absolute;
            top: 24px;
            left: 80px;
            right: 80px;
            height: 4px;
            background: #f1f5f9;
            z-index: 1;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            width: 25%;
            text-align: center;
            position: relative;
        }

        .step-icon {
            width: 48px;
            height: 48px;
            background: white;
            border: 4px solid #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .timeline-step.active .step-icon {
            background: #14b8a6;
            border-color: #ccfbf1;
            color: white;
            box-shadow: 0 0 20px rgba(20, 184, 166, 0.3);
            transform: scale(1.1);
        }

        .timeline-step.completed .step-icon {
            background: #10b981;
            border-color: #dcfce7;
            color: white;
        }

        .step-title {
            font-weight: 800;
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .step-period {
            font-size: 0.75rem;
            color: #14b8a6;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .step-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .step-legal {
            font-size: 0.65rem;
            background: #f8fafc;
            padding: 2px 8px;
            border-radius: 4px;
            color: #94a3b8;
            font-style: italic;
        }

        .timeline-step.active .step-title {
            color: var(--brand-color);
        }

        .timeline-step.completed .step-title {
            color: #10b981;
        }
    </style>
@endpush