@extends('layouts.desa')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <form action="{{ route('desa.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="user-profile mb-4">
                                    <div class="mx-auto position-relative" style="width: 100px; height: 100px;">
                                        @if(auth()->user()->foto)
                                            <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="User-Profile"
                                                class="theme-color-default-img img-fluid rounded-circle avatar-100 object-cover"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div
                                                class="rounded-circle bg-soft-primary d-flex align-items-center justify-content-center h-100 w-100 fs-2 fw-bold text-primary">
                                                {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 2)) }}
                                            </div>
                                        @endif
                                        <label for="foto"
                                            class="position-absolute bottom-0 end-0 bg-primary text-white p-1 rounded-circle d-flex align-items-center justify-content-center border border-2 border-white shadow-sm"
                                            style="width: 35px; height: 35px; cursor: pointer;">
                                            <i class="fas fa-camera" style="font-size: 14px;"></i>
                                        </label>
                                        <input type="file" id="foto" name="foto" class="d-none" accept="image/*"
                                            onchange="previewImage(this)">
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="mb-1">Upload Foto</h6>
                                        <small class="text-muted">Max. 2MB</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3 text-start">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_lengkap"
                                        value="{{ old('nama_lengkap', auth()->user()->nama_lengkap) }}" required>
                                </div>

                                <div class="form-group mb-4 text-start">
                                    <label class="form-label">No. HP / WhatsApp</label>
                                    <input type="text" class="form-control" name="no_hp"
                                        value="{{ old('no_hp', auth()->user()->no_hp) }}" placeholder="Contoh: 08123456789">
                                </div>

                                <button type="submit" class="btn btn-primary rounded-pill w-100">
                                    Simpan Profil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title" id="password-section">
                            <h4 class="card-title">Pengaturan Akun</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @php $errorBag = $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : new \Illuminate\Support\ViewErrorBag; @endphp
                        @if ($errorBag->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errorBag->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="new-user-info">
                            <form action="{{ route('desa.profile.password') }}" method="POST">
                                @csrf
                                <h6 class="mb-3 text-primary fw-bold">Ganti Password</h6>
                                <div class="row">
                                    <div class="form-group col-md-12 mb-3">
                                        <label class="form-label" for="current_password">Password Saat Ini</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="current_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label class="form-label" for="password">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password"
                                                required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Min. 8 karakter.</small>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label class="form-label" for="password_confirmation">Konfirmasi Password
                                            Baru</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="password_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" 
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-power-off me-1"></i> Keluar (Logout)
                                    </button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                                        <i class="fas fa-save me-1"></i> Simpan Password
                                    </button>
                                </div>
                            </form>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    // Update image source
                    var img = input.parentElement.querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                    } else {
                        // If no image exists (placeholder div), replace it
                        var container = input.parentElement;
                        var placeholder = container.querySelector('.rounded-circle');
                        if (placeholder) placeholder.remove();

                        var newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.className = 'theme-color-default-img img-fluid rounded-circle avatar-100 object-cover';
                        newImg.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                        container.insertBefore(newImg, container.firstChild);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toggles = document.querySelectorAll('.toggle-password');

            toggles.forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>
@endsection