@props([
    'label', 
    'name', 
    'currentFile' => null,
    'downloadUrl' => null,
    'helper' => null,
    'required' => false,
    'readonly' => false
])

<div class="mb-4">
    <label class="form-label fw-bold text-slate-700">
        {{ $label }} @if($required && !$readonly) <span class="text-danger">*</span> @endif
    </label>

    @if($readonly)
        <div class="d-flex align-items-center p-3 border rounded-3 bg-light">
            <i class="fas fa-file-pdf text-danger fa-2x me-3"></i>
            <div>
                <div class="fw-bold text-slate-800">Dokumen Tersimpan</div>
                @if($currentFile)
                    @if($downloadUrl)
                        <a href="{{ $downloadUrl }}" target="_blank" class="text-primary text-decoration-none small">
                            Lihat File <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    @else
                        {{-- Fallback for legacy public storage files --}}
                        <a href="{{ asset('storage/' . $currentFile) }}" target="_blank" class="text-primary text-decoration-none small">
                            Lihat File <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    @endif
                @else
                    <span class="text-muted small">Tidak ada file</span>
                @endif
            </div>
        </div>
    @else
        <div class="upload-area p-4 border-2 border-dashed border-slate-200 rounded-4 bg-slate-50 text-center hover-premium-upload transition-all position-relative">
            <input type="file" name="{{ $name }}" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                   accept=".pdf" {{ $required && !$currentFile ? 'required' : '' }}
                   onchange="updateFileName(this, '{{ $name }}-label')">
            
            <div class="mb-2">
                <div class="bg-white rounded-circle shadow-layered d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="fas fa-cloud-arrow-up fa-lg text-primary-500"></i>
                </div>
            </div>
            <h6 class="fw-bold text-slate-800 mb-1" style="font-size: 0.9rem;">Pilih atau Seret File ke Sini</h6>
            <p class="text-slate-400 small mb-0 px-3" id="{{ $name }}-label" style="font-size: 0.8rem;">
                @if($currentFile)
                    <span class="text-success-600 fw-medium"><i class="fas fa-check-circle me-1"></i> File terunggah. Klik untuk ganti baru.</span>
                @else
                    Unggah dokumen dalam format PDF dengan ukuran maksimal 2 Megabyte.
                @endif
            </p>
        </div>
        @if($helper)
            <div class="form-text text-slate-500 mt-2" style="font-size: 0.75rem;"><i class="fas fa-lightbulb me-1 text-warning opacity-75"></i> {{ $helper }}</div>
        @endif
        @error($name)
            <div class="text-danger small mt-1"><i class="fas fa-times-circle me-1"></i> {{ $message }}</div>
        @enderror
    @endif
</div>

<style>
    .shadow-layered {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important;
    }
    .hover-premium-upload:hover {
        border-color: var(--desa-primary) !important;
        background-color: #f0fdf4 !important;
        transform: translateY(-2px);
    }
    .hover-premium-upload:hover .shadow-layered {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<script>
    function updateFileName(input, labelId) {
        if (input.files && input.files[0]) {
            document.getElementById(labelId).innerHTML = 
                '<span class="text-primary fw-bold"><i class="fas fa-file-pdf me-1"></i> ' + input.files[0].name + '</span>';
        }
    }
</script>

<style>
    .hover-border-primary:hover {
        border-color: var(--bs-primary) !important;
        background-color: #f0f9ff !important;
    }
</style>
