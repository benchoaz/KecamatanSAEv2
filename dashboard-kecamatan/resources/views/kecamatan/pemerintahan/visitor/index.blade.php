@extends('layouts.kecamatan')

@section('title', 'Buku Tamu Kecamatan')

@section('content')
    <div class="content-header mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('kecamatan.pemerintahan.index') }}"
                class="btn btn-xs btn-light rounded-pill px-3 text-secondary text-decoration-none border shadow-sm">
                <i class="fas fa-arrow-left-long me-2"></i> Kembali ke Menu Utama
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-bold text-primary-900 mb-1">Registri Buku Tamu</h2>
                <p class="text-tertiary mb-0">
                    <i class="fas fa-clipboard-list me-1"></i> Pendataan administrasi pengunjung kantor kecamatan.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Registrasi -->
        <div class="col-md-4">
            <div class="card border-0 shadow-premium rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-primary-900 text-white py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold">Registrasi Pengunjung</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kecamatan.pemerintahan.visitor.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary-900">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control rounded-3 border-gray-200" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary-900">NIK (Opsional)</label>
                            <input type="text" name="nik" class="form-control rounded-3 border-gray-200" maxlength="16">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary-900">Asal Desa (Kecamatan)</label>
                            <select name="desa_asal_id" class="form-select rounded-3 border-gray-200">
                                <option value="">-- Luar Kecamatan --</option>
                                @foreach($desas as $desa)
                                    <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary-900">Alamat (Jika Luar Kecamatan)</label>
                            <input type="text" name="alamat_luar" class="form-control rounded-3 border-gray-200">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary-900">Tujuan Bidang</label>
                            <select name="tujuan_bidang" class="form-select rounded-3 border-gray-200" required>
                                <option value="Pemerintahan">Pemerintahan</option>
                                <option value="Ekbang">Ekbang</option>
                                <option value="Kesra">Kesra</option>
                                <option value="Trantib">Trantib</option>
                                <option value="Camat / Sekcam">Camat / Sekcam</option>
                                <option value="Pelayanan Umum">Pelayanan Umum</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary-900">Keperluan</label>
                            <textarea name="keperluan" class="form-control rounded-3 border-gray-200" rows="3"
                                required></textarea>
                        </div>
                        <button type="submit"
                            class="btn btn-brand-600 text-white w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="fas fa-user-plus me-2"></i> Daftar Pengunjung
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Monitoring -->
        <div class="col-md-8">
            <div class="card border-0 shadow-premium rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-primary-900 mb-0 fw-bold">Antrian & Riwayat Hari Ini</h5>
                    <span
                        class="badge rounded-pill bg-brand-50 text-brand-600 px-3 py-2 fw-bold">{{ $visitors->where('created_at', '>=', today())->count() }}
                        Pengunjung</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary-900 text-white small fw-bold">
                            <tr>
                                <th class="ps-4">NAMA & KEPERLUAN</th>
                                <th>ASAL</th>
                                <th>TUJUAN</th>
                                <th class="text-center">STATUS</th>
                                <th>JAM</th>
                                <th class="text-end pe-4">KENDALI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($visitors as $visitor)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary-900">{{ $visitor->nama }}</div>
                                        <small class="text-tertiary">{{ $visitor->keperluan }}</small>
                                    </td>
                                    <td class="text-secondary fw-medium">
                                        {{ $visitor->desaAsal->nama_desa ?? $visitor->alamat_luar }}</td>
                                    <td><span
                                            class="badge rounded-pill bg-brand-50 text-brand-600 px-3">{{ $visitor->tujuan_bidang }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($visitor->status == 'menunggu')
                                            <span class="badge rounded-pill bg-amber-50 text-amber-600 px-3">MENUNGGU</span>
                                        @elseif($visitor->status == 'dilayani')
                                            <span class="badge rounded-pill bg-sky-50 text-sky-600 px-3">DILAYANI</span>
                                        @else
                                            <span class="badge rounded-pill bg-emerald-50 text-emerald-600 px-3">SELESAI</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary fw-bold">{{ $visitor->jam_datang->format('H:i') }}</td>
                                    <td class="text-end pe-4">
                                        @if($visitor->status != 'selesai')
                                            <form action="{{ route('kecamatan.pemerintahan.visitor.update', $visitor->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                @if($visitor->status == 'menunggu')
                                                    <input type="hidden" name="status" value="dilayani">
                                                    <button class="btn btn-sm btn-sky-500 text-white rounded-pill px-3 shadow-sm">
                                                        Layani <i class="fas fa-chevron-right ms-1"></i>
                                                    </button>
                                                @else
                                                    <input type="hidden" name="status" value="selesai">
                                                    <button class="btn btn-sm btn-emerald-500 text-white rounded-pill px-3 shadow-sm">
                                                        Selesai <i class="fas fa-check ms-1"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="bg-primary-50 text-primary-200 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 80px; height: 80px;">
                                                <i class="fas fa-users fa-2x"></i>
                                            </div>
                                            <h5 class="text-primary-900 fw-bold">Belum ada pengunjung.</h5>
                                            <p class="text-tertiary">Hari ini belum ada tamu yang terdaftar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-slate-800 {
            background-color: #1e293b !important;
        }

        .bg-slate-900 {
            background-color: #0f172a !important;
        }

        .border-slate-700 {
            border-color: #334155 !important;
        }

        .text-slate-300 {
            color: #cbd5e1 !important;
        }

        .text-slate-400 {
            color: #94a3b8 !important;
        }

        .text-slate-500 {
            color: #64748b !important;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #0f172a;
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
    </style>
@endpush