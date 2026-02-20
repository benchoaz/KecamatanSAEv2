@extends('layouts.kecamatan')

@section('title', 'Monitoring BLT Desa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold text-slate-800 mb-1">Monitoring Penyaluran BLT</h2>
                <p class="text-slate-500 small">Pengawasan realisasi bagi hasil dan bantuan langsung tunai desa.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="{{ route('kecamatan.pembangunan.blt.index') }}" method="GET" class="d-inline-flex gap-2">
                    <select name="desa_id" class="form-select form-select-sm rounded-pill px-3 shadow-sm border-0"
                        onchange="this.form.submit()">
                        <option value="">Semua Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ $desa_id == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-brand-50 border-bottom">
                            <tr>
                                <th class="ps-4 text-slate-500 small fw-bold text-uppercase py-3">Desa / TA</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">KPM (Realisasi)</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Dana Tersalurkan</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Status</th>
                                <th class="text-slate-500 small fw-bold text-uppercase py-3">Analisa Anomali</th>
                                <th class="pe-4 text-end text-slate-500 small fw-bold text-uppercase py-3">Monitoring</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blt as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-slate-800">{{ $item->desa->nama_desa }}</div>
                                        <div class="small text-slate-500">Tahun Anggaran {{ $item->tahun_anggaran }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-slate-800">{{ $item->kpm_terealisasi }} /
                                            {{ $item->jumlah_kpm }} KPM</div>
                                        <div class="progress mt-1" style="height: 5px;">
                                            @php $pct = ($item->jumlah_kpm > 0) ? ($item->kpm_terealisasi / $item->jumlah_kpm * 100) : 0; @endphp
                                            <div class="progress-bar bg-sky-500" role="progressbar" style="width: {{ $pct }}%">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-800">Rp
                                            {{ number_format($item->total_dana_tersalurkan, 0, ',', '.') }}</div>
                                        <div class="small text-slate-500 italic">{{ $item->status_penyaluran }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-1 fw-normal">{{ $item->status_laporan }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($item->alerts))
                                            @foreach($item->alerts as $alert)
                                                <div class="text-{{ $alert['type'] }} small d-flex align-items-center gap-1 mb-1">
                                                    <i class="fas fa-circle-exclamation"></i>
                                                    <span>{{ $alert['message'] }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-emerald-600 small"><i class="fas fa-check-circle me-1"></i>
                                                Wajar</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm {{ $item->indikator_internal == 'Perlu Klarifikasi' ? 'btn-red' : 'btn-slate' }} rounded-pill px-3 dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                                {{ $item->indikator_internal ?? 'Review' }}
                                            </button>
                                            <ul class="dropdown-menu shadow-premium border-0 rounded-4">
                                                <li>
                                                    <form
                                                        action="{{ route('kecamatan.pembangunan.update-monitoring', [$item->id, 'blt']) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="indikator_internal" value="Wajar">
                                                        <button type="submit" class="dropdown-item py-2"><i
                                                                class="fas fa-check text-emerald-500 me-2"></i> Tandai
                                                            Wajar</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('kecamatan.pembangunan.update-monitoring', [$item->id, 'blt']) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="indikator_internal"
                                                            value="Perlu Klarifikasi">
                                                        <button type="submit" class="dropdown-item py-2"><i
                                                                class="fas fa-flag text-red-500 me-2"></i> Perlu
                                                            Klarifikasi</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-slate-400">Belum ada data BLT desa yang
                                        dikirim.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-brand-50 {
            background-color: #f0f7ff;
        }

        .btn-red {
            background-color: #fee2e2;
            color: #b91c1c;
            border: none;
        }

        .btn-red:hover {
            background-color: #fecaca;
        }

        .btn-slate {
            background-color: #f1f5f9;
            color: #475569;
            border: none;
        }

        .btn-slate:hover {
            background-color: #e2e8f0;
        }

        .shadow-premium {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection