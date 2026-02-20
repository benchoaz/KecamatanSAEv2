@extends('layouts.desa')

@section('title', 'Dashboard Desa')

@section('content')
    <div class="dashboard">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title text-success">Ruang Kerja Operator Desa</h1>
                <h2 class="welcome-name h3 text-white mb-2">{{ auth()->user()->name }}</h2>
                <p class="welcome-subtitle">Pastikan seluruh data pelaporan desa Anda terkirim tepat waktu.</p>
            </div>
            <div class="welcome-illustration">
                <div class="illustration-circle bg-success"></div>
                <i class="fas fa-file-invoice text-success"></i>
            </div>
        </div>

        <!-- Stats Grid (Operator Desa Context) -->
        <div class="stats-grid">
            <div class="stat-card stat-secondary">
                <div class="stat-icon"><i class="fas fa-file-pen"></i></div>
                <div class="stat-content">
                    <span class="stat-value" data-count="{{ $stats['summary']['draft'] }}">0</span>
                    <span class="stat-label">Laporan Draft</span>
                </div>
            </div>

            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="fas fa-file-circle-exclamation"></i></div>
                <div class="stat-content">
                    <span class="stat-value" data-count="{{ $stats['summary']['returned'] }}">0</span>
                    <span class="stat-label">Perlu Perbaikan</span>
                </div>
            </div>

            <div class="stat-card stat-info">
                <div class="stat-icon"><i class="fas fa-file-circle-check"></i></div>
                <div class="stat-content">
                    <span class="stat-value" data-count="{{ $stats['summary']['submitted'] }}">0</span>
                    <span class="stat-label">Data Terkirim</span>
                </div>
            </div>

            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="fas fa-map-location-dot"></i></div>
                <div class="stat-content">
                    <span class="stat-value-text"
                        style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $stats['nama_desa'] }}</span>
                    <span class="stat-label">Lokasi Anda</span>
                </div>
            </div>
        </div>

        <!-- Working Area Grid -->
        <div class="dashboard-grid">
            <!-- Activities (Audit Log) -->
            <div class="dashboard-card activities-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock-rotate-left"></i> Aktivitas Anda</h3>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        @foreach($activities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon {{ $activity['type'] }}">
                                    <i class="fas {{ $activity['icon'] }}"></i>
                                </div>
                                <div class="activity-content">
                                    <span class="activity-text">{{ $activity['message'] }}</span>
                                    <span class="activity-time">{{ $activity['time'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Access -->
            <div class="dashboard-card domains-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt"></i> Menu Input Operasional</h3>
                </div>
                <div class="card-body">
                    <div class="domains-grid">
                        <a href="{{ route('desa.ekbang.index') }}" class="domain-item domain-ekbang">
                            <div class="domain-icon"><i class="fas fa-road"></i></div>
                            <span class="domain-name">Input Progres Fisik</span>
                        </a>
                        <a href="{{ route('desa.submissions.create') }}" class="domain-item domain-kesra">
                            <div class="domain-icon"><i class="fas fa-file-export"></i></div>
                            <span class="domain-name">Kirim Laporan Baru</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const counters = document.querySelectorAll('.stat-value');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 1500;
                const step = target / (duration / 16);
                let current = 0;
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString('id-ID');
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString('id-ID');
                    }
                }, 16);
            });
        });
    </script>
@endpush