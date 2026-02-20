@extends('layouts.kecamatan')

@section('title', 'FAQ Administrasi - Jawaban Otomatis')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
            <div>
                <h1 class="text-slate-900 fw-bold fs-3 mb-1">FAQ Administrasi</h1>
                <p class="text-slate-400 small mb-0">Kelola jawaban otomatis untuk pertanyaan administratif masyarakat.</p>
            </div>
            <button class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addFaqModal">
                <i class="fas fa-plus me-2"></i> Tambah FAQ
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-emerald border-0 shadow-sm rounded-4 p-3 mb-4 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box icon-box-emerald xs">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-emerald-700 small fw-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden border border-slate-100">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50/50 border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Kategori
                                </th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Pertanyaan &
                                    Kata Kunci</th>
                                <th class="py-3 text-slate-400 text-[11px] fw-bold uppercase tracking-wider">Ringkasan
                                    Jawaban</th>
                                <th class="py-3 text-center text-slate-400 text-[11px] fw-bold uppercase tracking-wider">
                                    Status</th>
                                <th class="pe-4 py-3 text-end text-slate-400 text-[11px] fw-bold uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span
                                            class="text-slate-500 small fw-medium px-2 py-1 bg-slate-100 rounded-pill">{{ $faq->category }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-slate-800 mb-1 fs-6">{{ $faq->question }}</div>
                                        <div class="d-flex gap-1 flex-wrap">
                                            @foreach(explode(',', $faq->keywords) as $keyword)
                                                <span
                                                    class="text-[10px] bg-slate-50 text-slate-400 px-2 py-0.5 rounded border border-slate-100 italic">{{ trim($keyword) }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-slate-500 mb-0 small leading-relaxed text-truncate"
                                            style="max-width: 280px;">
                                            {{ $faq->answer }}
                                        </p>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($faq->category == 'Darurat')
                                            <span
                                                class="badge bg-rose-100 text-rose-600 border border-rose-200 px-2 py-1 rounded-pill fw-bold"
                                                style="font-size: 10px;">
                                                <i class="fas fa-triangle-exclamation me-1"></i> DARURAT
                                            </span>
                                        @elseif($faq->is_active)
                                            <span class="text-emerald-500 text-[11px] fw-bold">
                                                <i class="fas fa-circle me-1 fs-[6px] align-middle"></i> AKTIF
                                            </span>
                                        @else
                                            <span class="text-slate-300 text-[11px] fw-bold">
                                                <i class="fas fa-circle me-1 fs-[6px] align-middle"></i> NONAKTIF
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <button
                                            class="btn btn-sm btn-white border border-slate-200 rounded-3 px-3 fw-bold text-slate-600"
                                            data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq->id }}">
                                            Edit
                                        </button>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-robot fa-2x text-slate-200 mb-3"></i>
                                            <p class="text-slate-400 small mb-0">Belum ada FAQ yang dibuat.</p>
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

@section('modal')
    <!-- Edit Modal Items -->
    @foreach($faqs as $faq)
        <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-white py-3 px-4 border-bottom border-slate-100">
                        <h5 class="modal-title fw-bold text-slate-900 fs-5">Ubah FAQ Administrasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('kecamatan.pelayanan.faq.update', $faq->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 bg-white">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-700">Kategori</label>
                                    <select name="category"
                                        class="form-select bg-white border-slate-200 text-slate-900 fw-medium" required>
                                        <option value="Darurat" {{ $faq->category == 'Darurat' ? 'selected' : '' }}
                                            class="fw-bold text-rose-600">
                                            ⚠️ DARURAT (PENTING)</option>
                                        <option value="Kependudukan" {{ $faq->category == 'Kependudukan' ? 'selected' : '' }}>
                                            Kependudukan (Info Umum)</option>
                                        <option value="Pemerintahan" {{ $faq->category == 'Pemerintahan' ? 'selected' : '' }}>
                                            Pemerintahan</option>
                                        <option value="Pembangunan" {{ $faq->category == 'Pembangunan' ? 'selected' : '' }}>
                                            Pembangunan</option>
                                        <option value="Umum" {{ $faq->category == 'Umum' ? 'selected' : '' }}>Umum</option>
                                        <option value="Adminduk" {{ $faq->category == 'Adminduk' ? 'selected' : '' }}>Adminduk
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-700">Status Aktif</label>
                                    <select name="is_active"
                                        class="form-select bg-white border-slate-200 text-slate-900 fw-medium" required>
                                        <option value="1" {{ $faq->is_active ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ !$faq->is_active ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-700">Kata Kunci (Keywords)</label>
                                    <input type="text" name="keywords" value="{{ old('keywords', $faq->keywords) }}"
                                        class="form-control bg-white border-slate-200 text-slate-900 fw-medium" required>
                                    <p class="text-[10px] text-slate-400 mt-1 italic">Gunakan koma (,) sebagai pemisah.</p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-700">Pertanyaan Standar</label>
                                    <input type="text" name="question" value="{{ old('question', $faq->question) }}"
                                        class="form-control bg-white border-slate-200 text-slate-900 fw-medium" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-700">Jawaban Resmi</label>
                                    <textarea name="answer"
                                        class="form-control bg-white border-slate-200 text-slate-900 fw-medium h-32"
                                        required>{{ old('answer', $faq->answer) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-slate-50 py-3 px-4 border-top border-slate-100">
                            <button type="button" class="btn btn-link text-slate-400 text-decoration-none small fw-semibold"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold rounded-3">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Modal -->
    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white py-3 px-4 border-bottom border-slate-100">
                    <h5 class="modal-title fw-bold text-slate-900 fs-5">Tambah FAQ Administrasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('kecamatan.pelayanan.faq.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-slate-700">Kategori</label>
                                <select name="category"
                                    class="form-select bg-white border-slate-200 text-slate-900 fw-medium" required>
                                    <option value="Darurat" class="fw-bold text-rose-600">⚠️ DARURAT (PENTING)</option>
                                    <option value="Adminduk">Adminduk</option>
                                    <option value="Kependudukan">Kependudukan (Info Umum)</option>
                                    <option value="Pemerintahan">Pemerintahan</option>
                                    <option value="Pembangunan">Pembangunan</option>
                                    <option value="Umum">Umum</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-700">Kata Kunci (Keywords)</label>
                                <input type="text" name="keywords"
                                    class="form-control bg-white border-slate-200 text-slate-900 fw-medium"
                                    placeholder="Contoh: syarat, domisili, ktp" required>
                                <p class="text-[10px] text-slate-400 mt-1 italic">Gunakan koma (,) sebagai pemisah.</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-700">Pertanyaan Standar</label>
                                <input type="text" name="question"
                                    class="form-control bg-white border-slate-200 text-slate-900 fw-medium"
                                    placeholder="Apa syarat..." required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-700">Jawaban Resmi</label>
                                <textarea name="answer"
                                    class="form-control bg-white border-slate-200 text-slate-900 fw-medium h-32"
                                    placeholder="Tuliskan jawaban formal..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-slate-50 py-3 px-4 border-top border-slate-100">
                        <button type="button" class="btn btn-link text-slate-400 text-decoration-none small fw-semibold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-3">Simpan FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection