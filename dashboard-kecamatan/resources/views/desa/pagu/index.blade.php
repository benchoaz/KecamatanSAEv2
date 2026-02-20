@extends('layouts.desa')

@section('title', 'Set Anggaran Awal (Pagu)')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="fw-bold text-slate-800 mb-0">Anggaran Awal (Pagu)</h2>
                        <p class="text-slate-500 small">Input pagu dana desa per tahun anggaran untuk menghitung persentase penyerapan.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('desa.pembangunan.pagu.index') }}" method="GET" class="row g-2 mb-4 align-items-end">
                            <div class="col-md-4">
                                <label class="small text-slate-400 fw-bold">Tahun Anggaran</label>
                                <select name="tahun" class="form-select rounded-pill" onchange="this.form.submit()">
                                    @for($i = date('Y'); $i >= 2024; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="border-0 small fw-bold text-slate-500 py-3">Sumber Dana</th>
                                        <th class="border-0 small fw-bold text-slate-500 py-3">Jumlah Pagu (Rp)</th>
                                        <th class="border-0 small fw-bold text-slate-500 py-3">Keterangan</th>
                                        <th class="border-0 small fw-bold text-slate-500 py-3 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $sources = ['DDS' => 'Dana Desa', 'ADD' => 'Alokasi Dana Desa', 'PBP' => 'Bagi Hasil Pajak', 'DLL' => 'Lain-lain']; 
                                    @endphp
                                    @foreach($sources as $code => $label)
                                        @php $pagu = $pagus->where('sumber_dana', $code)->first(); @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-slate-800">{{ $label }}</div>
                                                <div class="small text-slate-400">{{ $code }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-slate-700">
                                                    Rp {{ number_format($pagu ? $pagu->jumlah_pagu : 0, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td><span class="small text-slate-500">{{ $pagu ? $pagu->keterangan : '-' }}</span></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-light rounded-pill px-3" 
                                                        onclick="editPagu('{{ $code }}', '{{ $label }}', '{{ $pagu ? $pagu->jumlah_pagu : 0 }}', '{{ $pagu ? $pagu->keterangan : '' }}')">
                                                    Update
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 2rem;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-slate-800 mb-3" id="formTitle">Set/Update Pagu</h5>
                        <form action="{{ route('desa.pembangunan.pagu.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-600">Sumber Dana</label>
                                <select name="sumber_dana" id="form_sumber" class="form-select rounded-3 py-2" required>
                                    <option value="DDS">Dana Desa (DDS)</option>
                                    <option value="ADD">Alokasi Dana Desa (ADD)</option>
                                    <option value="PBP">Bagi Hasil Pajak (PBP)</option>
                                    <option value="DLL">Lain-lain (DLL)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-slate-600">Total Anggaran Didapat (Rp)</label>
                                <input type="number" name="jumlah_pagu" id="form_jumlah" class="form-control rounded-3 py-2" placeholder="0" required>
                                <div class="small text-slate-400 mt-1 italic">Masukkan nilai pagu awal tahun ini.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-slate-600">Keterangan Tambahan</label>
                                <textarea name="keterangan" id="form_ket" class="form-control rounded-3" rows="2" placeholder="Catatan jika ada penambahan di perubahan APBDes..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-emerald text-white w-100 rounded-pill py-2 shadow-sm">
                                Simpan Pagu Anggaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editPagu(code, label, amount, note) {
            document.getElementById('formTitle').innerText = 'Update Pagu: ' + label;
            document.getElementById('form_sumber').value = code;
            document.getElementById('form_jumlah').value = amount;
            document.getElementById('form_ket').value = note;
            
            // Scroll smoothly to form if on mobile
            if (window.innerWidth < 992) {
                document.getElementById('formTitle').scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>

    <style>
        .btn-emerald { background-color: #10b981; }
        .btn-emerald:hover { background-color: #059669; color: white; }
    </style>
@endsection
