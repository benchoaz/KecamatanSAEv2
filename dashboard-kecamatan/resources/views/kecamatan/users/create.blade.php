@extends('layouts.kecamatan')

@section('title', 'Tambah Pengguna Baru')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header & Access Rules Alert -->
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('kecamatan.users.index') }}">Manajemen
                                    Pengguna</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold">Pendaftaran Pengguna Baru</h4>
                    <p class="text-muted">Tentukan hak akses dan penempatan unit kerja pengguna secara tepat.</p>
                </div>

                <div class="alert bg-soft-primary border-0 rounded-4 mb-4">
                    <div class="d-flex gap-3">
                        <i class="fas fa-shield-alt text-primary fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold text-primary mb-1">Aturan Keamanan Sistem</h6>
                            <ul class="mb-0 small text-dark opacity-75">
                                <li><strong>Super Admin & Kec:</strong> Tidak terikat pada desa tertentu.</li>
                                <li><strong>Operator Desa:</strong> Wajib dipilihkan desa penempatan untuk membatasi ruang
                                    lingkup data.</li>
                                <li><strong>Username:</strong> Bersifat permanen dan tidak dapat diubah di masa depan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form action="{{ route('kecamatan.users.store') }}" method="POST">
                    @csrf

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i> Identitas Akun
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap"
                                        class="form-control @error('nama_lengkap') is-invalid @enderror"
                                        value="{{ old('nama_lengkap') }}" placeholder="Masukan nama lengkap sesuai SK"
                                        required>
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0 bg-transparent text-muted">@</span>
                                        <input type="text" name="username"
                                            class="form-control border-start-0 @error('username') is-invalid @enderror"
                                            value="{{ old('username') }}" placeholder="username_unik" required>
                                    </div>
                                    @error('username') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Password Awal</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" required>
                                    <div class="form-text x-small">Minimal 6 karakter. Harap berikan kepada user secara
                                        personal.</div>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-user-shield me-2 text-warning"></i> Hak Akses &
                                Penempatan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pilih Role / Peran</label>
                                    <select name="role_id" id="role_select"
                                        class="form-select @error('role_id') is-invalid @enderror" required>
                                        <option value="">Pilih Role...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" data-role="{{ $role->nama_role }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->nama_role }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6" id="desa_select_container" style="display: none;">
                                    <label class="form-label fw-bold">Desa Penempatan</label>
                                    <select name="desa_id" class="form-select @error('desa_id') is-invalid @enderror">
                                        <option value="">Pilih Desa...</option>
                                        @foreach($villages as $desa)
                                            <option value="{{ $desa->id }}" {{ old('desa_id') == $desa->id ? 'selected' : '' }}>
                                                {{ $desa->nama_desa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('desa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Akun</label>
                                    <select name="status" class="form-select" required>
                                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('kecamatan.users.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 d-flex align-items-center gap-2"
                            style="background-color: #4f46e5; border-color: #4f46e5; color: white;">
                            <i class="fas fa-save"></i>
                            <span>Simpan Akun</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const roleSelect = document.getElementById('role_select');
                const desaContainer = document.getElementById('desa_select_container');

                function toggleDesaSelect() {
                    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                    const roleName = selectedOption.getAttribute('data-role');

                    if (roleName === 'Operator Desa') {
                        desaContainer.style.display = 'block';
                    } else {
                        desaContainer.style.display = 'none';
                    }
                }

                roleSelect.addEventListener('change', toggleDesaSelect);

                // Initial check on page load (for old input validation)
                toggleDesaSelect();
            });
        </script>
    @endpush

    <style>
        .bg-soft-primary {
            background: rgba(30, 66, 159, 0.08);
        }

        .x-small {
            font-size: 0.75rem;
        }
    </style>
@endsection