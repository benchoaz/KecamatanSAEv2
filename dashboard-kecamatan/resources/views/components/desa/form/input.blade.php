@props([
    'label', 
    'name', 
    'type' => 'text', 
    'value' => '', 
    'helper' => null, 
    'required' => false, 
    'readonly' => false,
    'placeholder' => ''
])

<div class="mb-4">
    <label class="form-label fw-bold text-slate-700">
        {{ $label }} @if($required && !$readonly) <span class="text-danger">*</span> @endif
    </label>
    
    @if($readonly)
        <div class="p-3 bg-light border rounded-3 text-slate-800 fw-medium">
            {{ $value ?: '-' }}
        </div>
        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @else
        <input type="{{ $type }}" 
               name="{{ $name }}" 
               class="form-control rounded-3 border-slate-200 shadow-sm transition-all focus-premium-ring @error($name) is-invalid @enderror" 
               style="padding: 0.65rem 1rem;"
               value="{{ old($name, $value) }}" 
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}>
        
        @if($helper)
            <div class="form-text text-slate-500 mt-1" style="font-size: 0.8rem;"><i class="fas fa-info-circle me-1 small opacity-75"></i> {{ $helper }}</div>
        @endif
        
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif
</div>

<style>
    .focus-premium-ring:focus {
        border-color: var(--desa-primary) !important;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1) !important;
        background-color: #fff !important;
    }
    .transition-all {
        transition: all 0.2s ease-in-out !important;
    }
</style>
