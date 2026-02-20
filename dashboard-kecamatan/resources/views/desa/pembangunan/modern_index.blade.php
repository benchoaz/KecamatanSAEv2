@extends('layouts.modern')

@section('title', 'Daftar Kegiatan Desa')

@section('breadcrumb')
    <li><span class="opacity-60">Kegiatan</span></li>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-base-content tracking-tight">Manajemen Kegiatan</h1>
                <p class="text-base-content/60 mt-1 italic">Membangun desa dengan semangat Saling Asah, Asih, Asuh.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('desa.pembangunan.create_new') }}" class="btn btn-primary shadow-lg shadow-primary/20">
                    <i class="fas fa-plus"></i> Tambah Kegiatan
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stats shadow bg-base-100 border border-base-200">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-hammer fa-2x"></i>
                    </div>
                    <div class="stat-title">Kegiatan Fisik</div>
                    <div class="stat-value">12</div>
                    <div class="stat-desc">Tahun Anggaran 2025</div>
                </div>
            </div>
            <div class="stats shadow bg-base-100 border border-base-200">
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="stat-title">Pemberdayaan</div>
                    <div class="stat-value">8</div>
                    <div class="stat-desc">Non-Fisik</div>
                </div>
            </div>
            <div class="stats shadow bg-base-100 border border-base-200">
                <div class="stat">
                    <div class="stat-figure text-accent">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <div class="stat-title">Total SPJ Draft</div>
                    <div class="stat-value">45</div>
                    <div class="stat-desc text-accent font-bold">Siap Unduh</div>
                </div>
            </div>
        </div>

        <!-- Filters & Table -->
        <div class="card bg-base-100 shadow-xl border border-base-200 overflow-hidden">
            <div
                class="p-6 border-b border-base-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-base-50/50">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-list text-primary"></i> Daftar Kegiatan Terkini
                </h3>
                <div class="join">
                    <input class="input input-bordered join-item w-full md:w-64" placeholder="Cari kegiatan..." />
                    <button class="btn join-item btn-primary">Cari</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200/50">
                        <tr>
                            <th>No</th>
                            <th>Kegiatan</th>
                            <th>Bidang</th>
                            <th>Pagu Anggaran</th>
                            <th>Bantuan ADM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembangunans as $index => $item)
                            <tr class="hover">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-base-content">{{ $item->nama_kegiatan }}</span>
                                        <span class="text-xs text-base-content/50 italic">{{ $item->lokasi }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge badge-outline gap-2">
                                        @if($item->masterKegiatan)
                                            {{ $item->masterKegiatan->subBidang->bidang->nama_bidang }}
                                        @else
                                            Pembangunan
                                        @endif
                                    </div>
                                </td>
                                <td class="font-mono font-semibold text-primary">
                                    Rp {{ number_format($item->pagu_anggaran, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="badge badge-accent badge-sm font-bold">{{ count($item->dokumenSpjs) }}
                                            Dokumen</span>
                                        <i class="fas fa-magic text-accent animate-pulse" title="Auto-checklist aktif"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="#" class="btn btn-square btn-ghost btn-sm text-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-square btn-ghost btn-sm text-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-base-50 flex justify-center">
                <div class="join shadow-sm bg-base-100">
                    <button class="join-item btn btn-sm">1</button>
                    <button class="join-item btn btn-sm btn-active">2</button>
                    <button class="join-item btn btn-sm">3</button>
                    <button class="join-item btn btn-sm">4</button>
                </div>
            </div>
        </div>

        <!-- Info Card (SAE Philosophy) -->
        <div class="alert shadow-lg bg-info/10 border-info/20 text-info rounded-2xl">
            <i class="fas fa-info-circle fa-lg"></i>
            <div>
                <h3 class="font-bold">Tips Pendampingan Administrasi (SAE)</h3>
                <div class="text-xs opacity-90">Gunakan tombol <span class="badge badge-accent badge-xs">Bantuan ADM</span>
                    untuk melihat dokumen apa saja yang harus disiapkan. Draft yang dihasilkan bersifat membantu persiapan
                    Anda, silakan sesuaikan dengan kondisi riil di lapangan.</div>
            </div>
        </div>
    </div>
@endsection