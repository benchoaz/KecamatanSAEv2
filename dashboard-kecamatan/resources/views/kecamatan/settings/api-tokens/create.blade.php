@extends('layouts.kecamatan')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Buat API Token Baru
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kecamatan.settings.api-tokens.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">Nama Token <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Contoh: N8N WhatsApp Bot">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Beri nama deskriptif untuk mengidentifikasi penggunaan token</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Abilities (Izin Akses)</label>
                            <small class="text-muted d-block mb-2">Pilih izin yang diperlukan. Kosongkan untuk akses penuh.</small>
                            
                            <div class="row">
                                @foreach($abilities as $key => $description)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="abilities[]" value="{{ $key }}" 
                                               id="ability_{{ $key }}"
                                               @checked(in_array($key, old('abilities', [])))>
                                        <label class="form-check-label" for="ability_{{ $key }}">
                                            <strong>{{ $key }}</strong>
                                            <br><small class="text-muted">{{ $description }}</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('abilities')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="expires_at" class="form-label">Tanggal Kedaluwarsa (Opsional)</label>
                            <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                            @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan untuk token tanpa batas waktu</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Penting:</strong> Token hanya akan ditampilkan sekali setelah dibuat. 
                            Pastikan untuk menyalin dan menyimpan token dengan aman.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>Generate Token
                            </button>
                            <a href="{{ route('kecamatan.settings.api-tokens.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection