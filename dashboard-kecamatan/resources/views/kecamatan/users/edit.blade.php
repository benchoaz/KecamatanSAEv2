@extends('layouts.kecamatan')

@section('title', 'Ubah Pengguna')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('kecamatan.users.index') }}">Manajemen
                                    Pengguna</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ubah Data User</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold">Perbarui Akun Pengguna</h4>
                    <p class="text-muted">Modifikasi hak akses atau profil user. Username tidak dapat diubah demi integritas
                        log audit.</p>
                </div>

                <form action="{{ route('kecamatan.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-id-card me-2 text-primary"></i> Identitas & Kredensial
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Username (Permanen)</label>
                                    <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly
                                        disabled title="Username bersifat permanen dan tidak dapat diubah">
                                    <div class="form-text x-small text-danger">Identitas username terkunci secara sistem.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap"
                                        class="form-control @error('nama_lengkap') is-invalid @enderror"
                                        value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Ganti Password <small
                                            class="text-muted">(Opsional)</small></label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    <div class="form-text x-small">Kosongkan jika tidak ingin mengubah password akun ini.
                                    </div>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-key me-2 text-warning"></i> Pengaturan Akses</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Role / Peran</label>
                                    <select name="role_id" id="role_select"
                                        class="form-select @error('role_id') is-invalid @enderror" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" data-role="{{ $role->nama_role }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                            <option value="{{ $desa->id }}" {{ old('desa_id', $user->desa_id) == $desa->id ? 'selected' : '' }}>
                                                {{ $desa->nama_desa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('desa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Akun</label>
                                    <select name="status" class="form-select" required>
                                        <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>
                                            Aktif (Memberi Akses)</option>
                                        <option value="nonaktif" {{ old('status', $user->status) == 'nonaktif' ? 'selected' : '' }}>Non-Aktif (Blokir Akses)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('kecamatan.users.index') }}" class="btn btn-light px-4">Kembali</a>
                        <button type="submit" class="btn btn-primary px-5 d-flex align-items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Perubahan</span>
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
                toggleDesaSelect(); // Initial check
            });
        </script>
    @endpush

    <style>
        .x-small {
            font-size: 0.75rem;
        }

        .bg-light {
            background-color: #f8fafc !important;
        }
    </style>
@endsection