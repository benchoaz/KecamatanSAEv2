@extends('layouts.kecamatan')

@php
    $isEdit = isset($layanan);
    $title = $isEdit ? 'Edit Layanan' : 'Tambah Layanan Baru';
@endphp

@section('title', $title)

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="{{ route('kecamatan.pelayanan.layanan.index') }}"
                            class="btn btn-link text-slate-500 p-0 text-decoration-none small mb-2 d-inline-block">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                        <h4 class="fw-bold text-slate-800 mb-1">{{ $title }}</h4>
                        <p class="text-slate-500 small mb-0">Isi detail informasi layanan yang ingin ditampilkan.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <form
                            action="{{ $isEdit ? route('kecamatan.pelayanan.layanan.update', $layanan->id) : route('kecamatan.pelayanan.layanan.store') }}"
                            method="POST">
                            @csrf
                            @if($isEdit) @method('PUT') @endif

                            <div class="row g-4">
                                <!-- Nama Layanan -->
                                <div class="col-md-12">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Nama
                                        Layanan <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_layanan"
                                        value="{{ old('nama_layanan', $layanan->nama_layanan ?? '') }}"
                                        class="form-control border-slate-200 bg-slate-50 rounded-3"
                                        placeholder="Contoh: Surat Keterangan" required>
                                </div>

                                <!-- Deskripsi / Syarat -->
                                <div class="col-12">
                                    <label
                                        class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Persyaratan
                                        (Teks) <span class="text-rose-500">*</span></label>
                                    <textarea name="deskripsi_syarat"
                                        class="form-control border-slate-200 bg-slate-50 rounded-3" rows="3"
                                        placeholder="Contoh: Fotokopi KTP, KK, & Surat Pengantar RT/RW."
                                        required>{{ old('deskripsi_syarat', $layanan->deskripsi_syarat ?? '') }}</textarea>
                                </div>

                                <!-- Dynamic Attachment Requirements -->
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <label
                                            class="form-label text-slate-700 fw-bold small uppercase tracking-wider mb-0">
                                            <i class="fas fa-paperclip text-primary me-1"></i> Upload Berkas Mandiri
                                        </label>
                                        <button type="button" id="addAttachmentBtn"
                                            class="btn btn-xs btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-plus me-1"></i> Tambah Field Upload
                                        </button>
                                    </div>
                                    <div id="attachmentRequirementsContainer" class="d-flex flex-column gap-3">
                                        @php
                                            $requirements = old('attachment_requirements', $layanan->attachment_requirements ?? []);
                                        @endphp
                                        @forelse($requirements as $req)
                                            <div class="attachment-row d-flex gap-2">
                                                <input type="text" name="attachment_requirements[]" value="{{ $req }}"
                                                    class="form-control border-slate-200 bg-slate-50 rounded-3"
                                                    placeholder="Contoh: Foto Ijazah Asli" required>
                                                <button type="button"
                                                    class="btn btn-outline-rose rounded-3 remove-attachment-btn">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @empty
                                            <div class="text-center py-3 border border-dashed border-slate-200 rounded-4 bg-slate-50/50"
                                                id="noAttachmentsMsg">
                                                <p class="text-slate-400 small mb-0">Belum ada persyaratan upload berkas
                                                    digital.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div class="form-text x-small text-slate-400 mt-2">
                                        Tentukan berkas apa saja yang harus di-upload oleh masyarakat saat mengajukan
                                        layanan ini.
                                    </div>
                                </div>

                                <!-- Estimasi Waktu -->
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Estimasi
                                        Waktu</label>
                                    <input type="text" name="estimasi_waktu"
                                        value="{{ old('estimasi_waktu', $layanan->estimasi_waktu ?? '') }}"
                                        class="form-control border-slate-200 bg-slate-50 rounded-3"
                                        placeholder="Contoh: 15 Menit">
                                </div>

                                <!-- Ikon FontAwesome -->
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Ikon
                                        (FontAwesome Class) <span class="text-rose-500">*</span></label>
                                    <div class="d-flex align-items-start gap-3">
                                        <div id="iconPreview"
                                            class="w-12 h-12 bg-slate-100 rounded-3 d-flex align-items-center justify-content-center text-slate-400 border border-slate-200 fs-4">
                                            <i class="fas {{ old('ikon', $layanan->ikon ?? 'fa-file-signature') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" name="ikon" id="ikonInput"
                                                value="{{ old('ikon', $layanan->ikon ?? 'fa-file-signature') }}"
                                                class="form-control border-slate-200 bg-slate-50 rounded-3"
                                                placeholder="fa-file-signature" required>
                                            <div class="form-text x-small text-slate-400">Preview ikon akan muncul di
                                                sebelah kiri.</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label text-slate-400 small fw-bold mb-2">Ikon Populer:</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @php
                                                $popularIcons = ['fa-file-signature', 'fa-id-card', 'fa-hands-helping', 'fa-heart', 'fa-user-md', 'fa-file-contract', 'fa-briefcase', 'fa-school', 'fa-building', 'fa-users', 'fa-map-marked-alt', 'fa-shield-halved'];
                                            @endphp
                                            @foreach($popularIcons as $popIcon)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-slate border-slate-200 bg-white rounded-3 icon-select-btn"
                                                    data-icon="{{ $popIcon }}">
                                                    <i class="fas {{ $popIcon }}"></i>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Warna Tampilan -->
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Pilih
                                        Tema Warna</label>
                                    <div class="row g-2">
                                        @php
                                            $colorPresets = [
                                                ['name' => 'Emerald', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'hex' => '#10b981'],
                                                ['name' => 'Blue', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-200', 'hex' => '#3b82f6'],
                                                ['name' => 'Rose', 'bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'hex' => '#f43f5e'],
                                                ['name' => 'Teal', 'bg' => 'bg-teal-50', 'text' => 'text-teal-600', 'border' => 'border-teal-200', 'hex' => '#14b8a6'],
                                                ['name' => 'Indigo', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'hex' => '#6366f1'],
                                                ['name' => 'Slate', 'bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'hex' => '#64748b'],
                                                ['name' => 'Amber', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200', 'hex' => '#f59e0b'],
                                                ['name' => 'Violet', 'bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'border' => 'border-violet-200', 'hex' => '#8b5cf6'],
                                            ];
                                        @endphp
                                        @foreach($colorPresets as $preset)
                                            <div class="col-3">
                                                <button type="button"
                                                    class="w-100 p-2 rounded-3 border transition-all color-select-btn d-flex flex-column align-items-center gap-1 {{ ($layanan->warna_bg ?? 'bg-emerald-50') == $preset['bg'] ? 'border-primary ring-2' : $preset['border'] }} bg-white"
                                                    data-bg="{{ $preset['bg'] }}" data-text="{{ $preset['text'] }}"
                                                    title="{{ $preset['name'] }}">
                                                    <div class="w-6 h-6 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                                        style="background-color: {{ $preset['hex'] }}">
                                                        <i class="fas fa-check text-white small check-icon {{ ($layanan->warna_bg ?? 'bg-emerald-50') == $preset['bg'] ? '' : 'd-none' }}"
                                                            style="font-size: 8px"></i>
                                                    </div>
                                                    <span class="x-small fw-bold {{ $preset['text'] }}"
                                                        style="font-size: 9px">{{ $preset['name'] }}</span>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="warna_bg" id="warnaBgInput"
                                        value="{{ old('warna_bg', $layanan->warna_bg ?? 'bg-emerald-50') }}">
                                    <input type="hidden" name="warna_text" id="warnaTextInput"
                                        value="{{ old('warna_text', $layanan->warna_text ?? 'text-emerald-600') }}">
                                    <div class="form-text x-small text-slate-400 mt-2">Pilih skema warna yang sesuai untuk
                                        layanan ini.</div>
                                </div>

                                <!-- Urutan & Status -->
                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Urutan
                                        Tampil</label>
                                    <input type="number" name="urutan" value="{{ old('urutan', $layanan->urutan ?? 0) }}"
                                        class="form-control border-slate-200 bg-slate-50 rounded-3" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Status
                                        Tampilan</label>
                                    <select name="is_active" class="form-select border-slate-200 bg-slate-50 rounded-3">
                                        <option value="1" {{ old('is_active', $layanan->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif (Tampilkan)</option>
                                        <option value="0" {{ old('is_active', $layanan->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif (Sembunyikan)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="isPopularSwitch" {{ old('is_popular', $layanan->is_popular ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label text-slate-700 fw-bold small uppercase tracking-wider ms-2" for="isPopularSwitch">
                                            Populer / Hero Overlay
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">Tipe Aksi Link</label>
                                    <select name="link_type" id="linkTypeSelect" class="form-select border-slate-200 bg-slate-50 rounded-3">
                                        <option value="form" {{ old('link_type', $layanan->link_type ?? 'form') == 'form' ? 'selected' : '' }}>Standard (Form Pendaftaran)</option>
                                        <option value="loker" {{ old('link_type', $layanan->link_type ?? 'form') == 'loker' ? 'selected' : '' }}>Loker (Buka Loker)</option>
                                        <option value="umkm" {{ old('link_type', $layanan->link_type ?? 'form') == 'umkm' ? 'selected' : '' }}>UMKM (Modal Bantuan)</option>
                                        <option value="external" {{ old('link_type', $layanan->link_type ?? 'form') == 'external' ? 'selected' : '' }}>Custom Link / URL</option>
                                    </select>
                                </div>

                                <div class="col-md-6" id="customLinkContainer" style="{{ old('link_type', $layanan->link_type ?? 'form') == 'external' ? '' : 'display: none;' }}">
                                    <label class="form-label text-slate-700 fw-bold small uppercase tracking-wider">URL Kustom</label>
                                    <input type="text" name="custom_link" value="{{ old('custom_link', $layanan->custom_link ?? '') }}" class="form-control border-slate-200 bg-slate-50 rounded-3" placeholder="https://...">
                                </div>

                                <div class="col-12 mt-4 pt-3 border-top border-slate-50">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                                        {{ $isEdit ? 'Simpan Perubahan' : 'Terbitkan Layanan' }}
                                    </button>
                                    <a href="{{ route('kecamatan.pelayanan.layanan.index') }}"
                                        class="btn btn-light rounded-pill px-4 ms-2">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ikonInput = document.getElementById('ikonInput');
            const iconPreview = document.querySelector('#iconPreview i');
            const warnaBgInput = document.getElementById('warnaBgInput');
            const warnaTextInput = document.getElementById('warnaTextInput');
            const colorButtons = document.querySelectorAll('.color-select-btn');
            const iconButtons = document.querySelectorAll('.icon-select-btn');

            // Update icon preview on input
            ikonInput.addEventListener('input', function () {
                const val = this.value.trim();
                iconPreview.className = val ? 'fas ' + val : 'fas fa-question';
            });

            // Handle Quick Icon Select
            iconButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const icon = this.dataset.icon;
                    ikonInput.value = icon;
                    iconPreview.className = 'fas ' + icon;

                    // Highlight selected icon button
                    iconButtons.forEach(b => b.classList.remove('border-primary', 'bg-slate-50'));
                    this.classList.add('border-primary', 'bg-slate-50');
                });
            });

            // Handle Color Preset Select
            colorButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const bg = this.dataset.bg;
                    const text = this.dataset.text;

                    warnaBgInput.value = bg;
                    warnaTextInput.value = text;

                    // Update preview styling
                    const previewBox = document.getElementById('iconPreview');
                    previewBox.className = `w-12 h-12 ${bg} ${text} rounded-3 d-flex align-items-center justify-content-center border fs-4`;

                    // Manage UI highlights
                    colorButtons.forEach(b => {
                        b.classList.remove('border-primary', 'ring-2');
                        const checkIcon = b.querySelector('.check-icon');
                        if (checkIcon) checkIcon.classList.add('d-none');
                    });

                    this.classList.add('border-primary', 'ring-2');
                    const myCheck = this.querySelector('.check-icon');
                    if (myCheck) myCheck.classList.remove('d-none');
                });
            });

            // Initialize styling for existing data
            const activeColorBtn = Array.from(colorButtons).find(btn => btn.dataset.bg === warnaBgInput.value);
            if (activeColorBtn) {
                activeColorBtn.click();
            }

            // Dynamic Attachment Requirements
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const attachmentContainer = document.getElementById('attachmentRequirementsContainer');
            const noAttachmentsMsg = document.getElementById('noAttachmentsMsg');

            addAttachmentBtn.addEventListener('click', function () {
                if (noAttachmentsMsg) noAttachmentsMsg.remove();

                const row = document.createElement('div');
                row.className = 'attachment-row d-flex gap-2';
                row.innerHTML = `
                        <input type="text" name="attachment_requirements[]" 
                            class="form-control border-slate-200 bg-slate-50 rounded-3" 
                            placeholder="Contoh: Foto KK Asli" required>
                        <button type="button" class="btn btn-outline-rose rounded-3 remove-attachment-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                attachmentContainer.appendChild(row);

                // Add remove listener to the new button
                row.querySelector('.remove-attachment-btn').addEventListener('click', function () {
                    row.remove();
                    checkEmptyRequirements();
                });
            });

            // Initial remove listeners for existing rows
            document.querySelectorAll('.remove-attachment-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    this.closest('.attachment-row').remove();
                    checkEmptyRequirements();
                });
            });

            // Toggle custom link input
            const linkTypeSelect = document.getElementById('linkTypeSelect');
            const customLinkContainer = document.getElementById('customLinkContainer');

            linkTypeSelect.addEventListener('change', function () {
                if (this.value === 'external') {
                    customLinkContainer.style.display = 'block';
                } else {
                    customLinkContainer.style.display = 'none';
                }
            });

            function checkEmptyRequirements() {
                if (attachmentContainer.children.length === 0) {
                    attachmentContainer.innerHTML = `
                            <div class="text-center py-3 border border-dashed border-slate-200 rounded-4 bg-slate-50/50" id="noAttachmentsMsg">
                                <p class="text-slate-400 small mb-0">Belum ada persyaratan upload berkas digital.</p>
                            </div>
                        `;
                }
            }
        });
    </script>
    <style>
        .ring-2 {
            box-shadow: 0 0 0 2px white, 0 0 0 4px var(--bs-primary);
        }

        .btn-outline-slate:hover {
            background-color: var(--bs-slate-50);
            border-color: var(--bs-primary);
        }
    </style>
@endpush