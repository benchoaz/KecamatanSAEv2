@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="dashboard">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title">Selamat Datang, <span
                        class="welcome-name">{{ auth()->user()->name ?? 'Administrator' }}</span>!</h1>
                <p class="welcome-subtitle">Pantau dan kelola data kecamatan Anda dengan mudah.</p>
            </div>
            <div class="welcome-illustration">
                <div class="illustration-circle"></div>
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            @if(isset($stats['summary']))
                <!-- Operator Desa Summary -->
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
                        <span class="stat-label">Menunggu Verifikasi</span>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-map-location-dot"></i></div>
                    <div class="stat-content">
                        <span class="stat-value-text"
                            style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $stats['nama_desa'] }}</span>
                        <span class="stat-label">Desa Anda</span>
                    </div>
                </div>
            @else
                <!-- Admin / Kecamatan Stats -->
                <div class="stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <span class="stat-value" data-count="{{ $stats['total_penduduk'] }}">0</span>
                        <span class="stat-label">Total Penduduk</span>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-file-signature"></i></div>
                    <div class="stat-content">
                        <span class="stat-value" data-count="{{ $stats['surat_bulan_ini'] }}">0</span>
                        <span class="stat-label">Surat Bulan Ini</span>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                    <div class="stat-content">
                        <span class="stat-value" data-count="{{ $stats['jumlah_desa'] }}">0</span>
                        <span class="stat-label">Desa/Kelurahan</span>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-clipboard-user"></i></div>
                    <div class="stat-content">
                        <span class="stat-value" data-count="{{ $stats['pengunjung_hari_ini'] }}">0</span>
                        <span class="stat-label">Pengunjung Hari Ini</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Chart Section -->
            <div class="dashboard-card chart-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area"></i>
                        Statistik Pelayanan
                    </h3>
                    <div class="card-actions">
                        <select class="card-select">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>Tahun Ini</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Domain Quick Access -->
            <div class="dashboard-card domains-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-th-large"></i>
                        Akses Cepat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="domains-grid">
                        <a href="#" class="domain-item domain-pemerintahan">
                            <div class="domain-icon">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <span class="domain-name">Pemerintahan</span>
                            <span class="domain-count">24 data</span>
                        </a>

                        <a href="#" class="domain-item domain-ekbang">
                            <div class="domain-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <span class="domain-name">Ekbang</span>
                            <span class="domain-count">18 data</span>
                        </a>

                        <a href="#" class="domain-item domain-kesra">
                            <div class="domain-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <span class="domain-name">Kesra</span>
                            <span class="domain-count">32 data</span>
                        </a>

                        <a href="#" class="domain-item domain-trantibum">
                            <div class="domain-icon">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <span class="domain-name">Trantibum</span>
                            <span class="domain-count">15 data</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="dashboard-card activities-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock-rotate-left"></i>
                        Aktivitas Terbaru
                    </h3>
                    <a href="#" class="card-link">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon success">
                                <i class="fas fa-file-circle-check"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Surat Keterangan Domisili telah diterbitkan</span>
                                <span class="activity-time">5 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon info">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Penduduk baru terdaftar dari Desa Sukamaju</span>
                                <span class="activity-time">15 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon warning">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Laporan infrastruktur jalan rusak diterima</span>
                                <span class="activity-time">1 jam yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon primary">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Jadwal rapat koordinasi telah dibuat</span>
                                <span class="activity-time">2 jam yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon success">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Disposisi surat telah diselesaikan</span>
                                <span class="activity-time">3 jam yang lalu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="dashboard-card tasks-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-check"></i>
                        Tugas Pending
                    </h3>
                    <span class="card-badge">5</span>
                </div>
                <div class="card-body">
                    <div class="task-list">
                        <div class="task-item">
                            <label class="task-checkbox">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <div class="task-content">
                                <span class="task-text">Review surat masuk dari Dinas Kesehatan</span>
                                <span class="task-priority high">Tinggi</span>
                            </div>
                        </div>

                        <div class="task-item">
                            <label class="task-checkbox">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <div class="task-content">
                                <span class="task-text">Verifikasi data penduduk desa Mekar Jaya</span>
                                <span class="task-priority medium">Sedang</span>
                            </div>
                        </div>

                        <div class="task-item">
                            <label class="task-checkbox">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <div class="task-content">
                                <span class="task-text">Update laporan bulanan Ekbang</span>
                                <span class="task-priority medium">Sedang</span>
                            </div>
                        </div>

                        <div class="task-item">
                            <label class="task-checkbox">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <div class="task-content">
                                <span class="task-text">Koordinasi dengan Linmas untuk acara</span>
                                <span class="task-priority low">Rendah</span>
                            </div>
                        </div>

                        <div class="task-item">
                            <label class="task-checkbox">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <div class="task-content">
                                <span class="task-text">Siapkan dokumen rapat camat</span>
                                <span class="task-priority high">Tinggi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="dashboard-bottom">
            <!-- Population Distribution -->
            <div class="dashboard-card population-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-pie-chart"></i>
                        Distribusi Penduduk per Desa
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="populationChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Calendar/Schedule -->
            <div class="dashboard-card calendar-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Agenda Mendatang
                    </h3>
                </div>
                <div class="card-body">
                    <div class="schedule-list">
                        <div class="schedule-item">
                            <div class="schedule-date">
                                <span class="schedule-day">24</span>
                                <span class="schedule-month">Jan</span>
                            </div>
                            <div class="schedule-content">
                                <span class="schedule-title">Rapat Koordinasi Camat</span>
                                <span class="schedule-time"><i class="fas fa-clock"></i> 09:00 - 11:00</span>
                                <span class="schedule-location"><i class="fas fa-map-marker-alt"></i> Ruang Rapat
                                    Utama</span>
                            </div>
                        </div>

                        <div class="schedule-item">
                            <div class="schedule-date">
                                <span class="schedule-day">25</span>
                                <span class="schedule-month">Jan</span>
                            </div>
                            <div class="schedule-content">
                                <span class="schedule-title">Sosialisasi Program Bantuan</span>
                                <span class="schedule-time"><i class="fas fa-clock"></i> 13:00 - 15:00</span>
                                <span class="schedule-location"><i class="fas fa-map-marker-alt"></i> Balai Desa
                                    Sukamaju</span>
                            </div>
                        </div>

                        <div class="schedule-item">
                            <div class="schedule-date">
                                <span class="schedule-day">28</span>
                                <span class="schedule-month">Jan</span>
                            </div>
                            <div class="schedule-content">
                                <span class="schedule-title">Monitoring Pembangunan</span>
                                <span class="schedule-time"><i class="fas fa-clock"></i> 08:00 - 12:00</span>
                                <span class="schedule-location"><i class="fas fa-map-marker-alt"></i> Lokasi Proyek</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Animate stat counters
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

            // Service Chart
            const serviceCtx = document.getElementById('serviceChart');
            if (serviceCtx) {
                new Chart(serviceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Surat Masuk',
                            data: [12, 19, 15, 25, 22, 8, 5],
                            borderColor: '#14b8a6',
                            backgroundColor: 'rgba(20, 184, 166, 0.1)',
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Surat Keluar',
                            data: [8, 15, 12, 18, 20, 6, 3],
                            borderColor: '#64748b',
                            backgroundColor: 'rgba(100, 116, 139, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Population Chart
            const popCtx = document.getElementById('populationChart');
            if (popCtx) {
                new Chart(popCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sukamaju', 'Mekar Jaya', 'Harapan', 'Sejahtera', 'Makmur', 'Damai', 'Sentosa', 'Bahagia'],
                        datasets: [{
                            data: [2150, 1890, 1650, 1420, 1780, 1350, 1280, 1327],
                            backgroundColor: [
                                '#14b8a6',
                                '#0d9488',
                                '#0f766e',
                                '#64748b',
                                '#475569',
                                '#334155',
                                '#1e293b',
                                '#0f172a'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush