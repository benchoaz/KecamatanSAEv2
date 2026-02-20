@extends('layouts.desa')

@section('title', 'Buat Laporan Baru')
@section('breadcrumb', 'Buat Laporan')

    @section('content')
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card bg-white border-gray-200 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title text-gray-800 fw-bold mb-0">
                                <i class="fas fa-file-signature me-2 text-teal-600"></i>
                                Form Input Laporan
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if(isset($submission) && $submission->status === 'returned' && $submission->catatan_review)
                                <div class="alert alert-warning border-start border-warning border-4">
                                    <h6 class="text-warning fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Catatan
                                        Perbaikan:</h6>
                                    <p class="mb-0">{{ $submission->catatan_review }}</p>
                                </div>
                            @endif

                            @if(isset($submission) && $submission->isLocked())
                                <div class="alert alert-info border-start border-info border-4">
                                    <h6 class="text-info fw-bold"><i class="fas fa-info-circle me-2"></i>Status:
                                        {{ ucfirst($submission->status) }}
                                    </h6>
                                    <p class="mb-0">Laporan ini sedang diproses atau sudah disetujui. Anda tidak dapat mengubah
                                        data.</p>
                                </div>
                            @endif

                            <form
                                action="{{ isset($submission) ? route('desa.submissions.update', $submission->id) : route('desa.submissions.store') }}"
                                method="POST" enctype="multipart/form-data" id="submissionForm">
                                @csrf
                                @if(isset($submission))
                                    @method('PUT')
                                @endif

                                <!-- Section 1: Informasi Dasar -->
                                <h6 class="text-uppercase text-teal-600 small fw-bold mb-3 border-bottom border-gray-100 pb-2">
                                    Informasi Dasar</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-gray-700 fw-bold">Menu Bidang <span
                                                class="text-danger">*</span></label>
                                        <select name="menu_id" id="menuSelect"
                                            class="form-select bg-white text-gray-900 border-gray-200" required>
                                            <option value="">Pilih Bidang...</option>
                                            @foreach($menus as $menu)
                                                <option value="{{ $menu->id }}" {{ (isset($selectedMenuId) && $selectedMenuId == $menu->id) ? 'selected' : '' }}>
                                                    {{ $menu->nama_menu }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-gray-700 fw-bold">Aspek Penilaian <span
                                                class="text-danger">*</span></label>
                                        <select name="aspek_id" id="aspekSelect"
                                            class="form-select bg-white text-gray-900 border-gray-200" required disabled>
                                            <option value="">Pilih Bidang Terlebih Dahulu...</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label text-gray-700 fw-bold">Tahun <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="tahun"
                                            class="form-control bg-white text-gray-900 border-gray-200" value="{{ date('Y') }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-gray-700 fw-bold">Periode <span
                                                class="text-danger">*</span></label>
                                        <select name="periode" class="form-select bg-white text-gray-900 border-gray-200"
                                            required>
                                            <option value="bulanan">Bulanan</option>
                                            <option value="triwulan">Triwulan</option>
                                            <option value="semester">Semester</option>
                                            <option value="tahunan">Tahunan</option>
                                        </select>
                                    </div>
                                    <!-- Conditional inputs for Month/Quarter could go here -->
                                </div>

                                <!-- Section 2: Indikator & Bukti -->
                                <div id="indikatorSection" style="display: none;">
                                    <h6
                                        class="text-uppercase text-teal-600 small fw-bold mb-3 border-bottom border-gray-100 pb-2">
                                        Indikator & Bukti Dukung</h6>
                                    <div id="indikatorContainer">
                                        <!-- Indikator items will be injected here via JS -->
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2">Memuat Indikator...</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Submit -->
                                <div class="d-flex justify-content-end mt-4 pt-3 border-top border-gray-100">
                                    <a href="{{ route('desa.dashboard') }}" class="btn btn-outline-secondary me-2 px-4">
                                        {{ isset($submission) && $submission->isLocked() ? 'Kembali' : 'Batal' }}
                                    </a>

                                    @if((!isset($submission) || (isset($submission) && $submission->isEditable())) && auth()->user()->can('submission.submit'))
                                        <button type="submit" name="action" value="draft"
                                            class="btn btn-light border text-gray-700 me-2 px-4 shadow-sm">
                                            <i class="fas fa-save me-2"></i> Simpan Draft
                                        </button>
                                        <button type="submit" name="action" value="submit" class="btn btn-teal px-4 shadow-sm">
                                            <i class="fas fa-paper-plane me-2"></i> Ajukan Laporan
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/menu-pages.css') }}">
    <style>
        .btn-teal {
            background-color: #14b8a6;
            color: white;
        }

        .btn-teal:hover {
            background-color: #0d9488;
            color: white;
        }

        /* Override form input background for white theme */
        .form-select,
        .form-control {
            background-color: #fff !important;
            color: #1e293b !important;
        }

        .form-select:disabled,
        .form-control:disabled {
            background-color: #f8fafc !important;
            color: #64748b !important;
        }
    </style>
    <script>
        // Override the dynamic generation to use white theme classes
        window.loadIndikatorUiConfig = {
            inputClass: "form-control bg-white text-gray-900 border-gray-200",
            cardClass: "card mb-3 bg-white border-gray-200 shadow-sm",
            badgeClass: "badge bg-teal-500",
            labelClass: "form-label text-gray-800 fw-bold mb-0"
        };
    </script>
@endpush

@push('scripts')
    <script>
        const submissionId = @json($submission->id ?? null);
        const existingAnswers = @json($submission->jawabanIndikator ?? []);
        const existingEvidence = @json($submission->buktiDukung ?? []);
        const isEditable = @json(isset($submission) ? $submission->isEditable() : true);
        const status = @json($submission->status ?? 'draft');

        document.addEventListener('DOMContentLoaded', function () {
            const menuSelect = document.getElementById('menuSelect');
            const aspekSelect = document.getElementById('aspekSelect');
            const indikatorSection = document.getElementById('indikatorSection');
            const indikatorContainer = document.getElementById('indikatorContainer');

            // Initial Load if menu selected
            if (menuSelect.value) {
                loadAspek(menuSelect.value);
            }

            menuSelect.addEventListener('change', function () {
                loadAspek(this.value);
            });

            aspekSelect.addEventListener('change', function () {
                loadIndikator(this.value);
            });

            function loadAspek(menuId) {
                if (!menuId) {
                    aspekSelect.innerHTML = '<option value="">Pilih Bidang Terlebih Dahulu...</option>';
                    aspekSelect.disabled = true;
                    return;
                }

                // If editing, don't clear right away if value matches
                const currentAspekId = aspekSelect.value;
                const targetAspekId = @json($submission->aspek_id ?? null);

                // Reset
                aspekSelect.innerHTML = '<option value="">Memuat...</option>';
                aspekSelect.disabled = true;

                if (!submissionId) indikatorSection.style.display = 'none';

                fetch(`/submissions/helper/aspek/${menuId}`)
                    .then(response => response.json())
                    .then(data => {
                        aspekSelect.innerHTML = '<option value="">Pilih Aspek Penilaian...</option>';
                        data.forEach(aspek => {
                            const isSelected = (targetAspekId && targetAspekId == aspek.id) || (currentAspekId == aspek.id);
                            aspekSelect.innerHTML += `<option value="${aspek.id}" ${isSelected ? 'selected' : ''}>${aspek.nama_aspek}</option>`;
                        });

                        if (isEditable) {
                            aspekSelect.disabled = false;
                        } else {
                            aspekSelect.disabled = true;
                        }

                        if (aspekSelect.value) {
                            loadIndikator(aspekSelect.value);
                        }
                    });
            }

            function loadIndikator(aspekId) {
                if (!aspekId) {
                    indikatorSection.style.display = 'none';
                    return;
                }

                indikatorSection.style.display = 'block';
                indikatorContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-teal-500"></div></div>';

                // Use the UI config if defined, or defaults
                const cfg = window.loadIndikatorUiConfig || {
                    inputClass: "form-control bg-dark text-white border-secondary",
                    cardClass: "card mb-3 bg-dark border border-secondary",
                    badgeClass: "badge bg-primary",
                    labelClass: "form-label text-white fw-bold mb-0"
                };

                fetch(`/submissions/helper/indikator/${aspekId}`)
                    .then(response => response.json())
                    .then(data => {
                        indikatorContainer.innerHTML = '';

                        if (data.length === 0) {
                            indikatorContainer.innerHTML = '<div class="alert alert-info">Belum ada indikator untuk aspek ini.</div>';
                            return;
                        }

                        data.forEach((ind, index) => {
                            let inputHtml = '';

                            const answer = existingAnswers.find(a => a.indikator_id === ind.id);
                            const evidence = existingEvidence.find(e => e.indikator_id === ind.id);
                            const val = answer ? answer.nilai : '';
                            const readOnlyAttr = isEditable ? '' : 'disabled';

                            if (ind.tipe_input === 'file') {
                                inputHtml = `
                                        <input type="file" name="indikator[${ind.id}][file]" class="${cfg.inputClass}" ${ind.wajib_bukti && !evidence ? 'required' : ''} ${readOnlyAttr}>
                                        ${evidence ? `<div class="mt-1"><small class="text-success"><i class="fas fa-check me-1"></i> File terupload: <a href="/files/${submissionId}/${evidence.nama_file}" target="_blank" class="text-teal-600 fw-bold">${evidence.nama_file}</a></small></div>` : ''}
                                        <small class="text-muted d-block mt-1">Format: PDF/JPG. Maks 5MB.</small>
                                    `;
                            } else if (ind.tipe_input === 'number' || ind.tipe_input === 'persen') {
                                inputHtml = `
                                        <div class="input-group">
                                            <input type="number" name="indikator[${ind.id}][nilai]" step="any" class="${cfg.inputClass}" placeholder="0" value="${val}" required ${readOnlyAttr}>
                                            ${ind.tipe_input === 'persen' ? '<span class="input-group-text bg-light border-gray-200 text-gray-600">%</span>' : ''}
                                        </div>
                                    `;
                                if (ind.wajib_bukti) {
                                    inputHtml += `
                                            <div class="mt-2">
                                                <label class="small text-muted mb-1 fw-bold">Upload Bukti Dukung (Wajib):</label>
                                                <input type="file" name="indikator[${ind.id}][file]" class="${cfg.inputClass} form-control-sm" ${!evidence ? 'required' : ''} ${readOnlyAttr}>
                                                ${evidence ? `<small class="text-success d-block mt-1"><i class="fas fa-check me-1"></i> Terupload: <a href="/files/${submissionId}/${evidence.nama_file}" target="_blank" class="text-teal-600 fw-bold">${evidence.nama_file}</a></small>` : ''}
                                            </div>
                                        `;
                                }
                            } else if (ind.tipe_input === 'text') {
                                inputHtml = `
                                        <input type="text" name="indikator[${ind.id}][nilai]" class="${cfg.inputClass}" placeholder="Isi jawaban..." value="${val}" required ${readOnlyAttr}>
                                    `;
                                if (ind.wajib_bukti) {
                                    inputHtml += `
                                            <div class="mt-2">
                                                <label class="small text-muted mb-1 fw-bold">Upload Bukti Dukung (Wajib):</label>
                                                <input type="file" name="indikator[${ind.id}][file]" class="${cfg.inputClass} form-control-sm" ${!evidence ? 'required' : ''} ${readOnlyAttr}>
                                                ${evidence ? `<small class="text-success d-block mt-1"><i class="fas fa-check me-1"></i> Terupload: <a href="/files/${submissionId}/${evidence.nama_file}" target="_blank" class="text-teal-600 fw-bold">${evidence.nama_file}</a></small>` : ''}
                                            </div>
                                        `;
                                }
                            } else {
                                inputHtml = `
                                        <textarea name="indikator[${ind.id}][nilai]" rows="2" class="${cfg.inputClass}" placeholder="Isi keterangan..." ${readOnlyAttr}>${val}</textarea>
                                    `;
                            }

                            const cardHtml = `
                                    <div class="${cfg.cardClass}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <label class="${cfg.labelClass}">
                                                    <span class="${cfg.badgeClass} me-2">${index + 1}</span>
                                                    ${ind.nama_indikator}
                                                </label>
                                                ${ind.wajib_bukti ? '<span class="badge bg-amber-100 text-amber-700">Wajib Bukti</span>' : ''}
                                            </div>
                                            <div class="ps-1">
                                                ${inputHtml}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            indikatorContainer.innerHTML += cardHtml;
                        });
                    });
            }
        });
    </script>
@endpush