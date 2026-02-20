@extends('layouts.kecamatan')

@section('title', 'Audit Aktivitas')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Audit Aktivitas Sistem</h4>
                <p class="text-muted small">Catatan aktivitas login, logout, dan aksi pengguna</p>
            </div>
            <a href="{{ route('kecamatan.audit-logs.export', request()->query()) }}" class="btn btn-success d-flex align-items-center gap-2">
                <i class="fas fa-file-export"></i>
                <span>Export CSV</span>
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-soft-primary text-primary rounded-3 p-3">
                                <i class="fas fa-calendar-day fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Hari Ini</div>
                                <div class="fw-bold fs-5">{{ \App\Models\AuditLog::whereDate('created_at', today())->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-soft-success text-success rounded-3 p-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Pengunjung Unik</div>
                                <div class="fw-bold fs-5">{{ \App\Models\AuditLog::whereDate('created_at', today())->distinct('ip_address')->count('ip_address') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-soft-info text-info rounded-3 p-3">
                                <i class="fas fa-right-to-bracket fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Login Hari Ini</div>
                                <div class="fw-bold fs-5">{{ \App\Models\AuditLog::whereDate('created_at', today())->where('action', 'login')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-soft-warning text-warning rounded-3 p-3">
                                <i class="fas fa-right-from-bracket fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Logout Hari Ini</div>
                                <div class="fw-bold fs-5">{{ \App\Models\AuditLog::whereDate('created_at', today())->where('action', 'logout')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3">
                <form action="{{ route('kecamatan.audit-logs.index') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-light bg-light"
                                placeholder="Cari IP atau User..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="action" class="form-select border-light bg-light">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="user_id" class="form-select border-light bg-light">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control border-light bg-light"
                            placeholder="Dari Tanggal" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control border-light bg-light"
                            placeholder="Sampai Tanggal" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-auto ms-auto">
                        <button type="submit" class="btn btn-teal px-4">Filter</button>
                        @if(request()->anyFilled(['search', 'action', 'user_id', 'date_from', 'date_to']))
                            <a href="{{ route('kecamatan.audit-logs.index') }}" class="btn btn-light"><i class="fas fa-undo"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Waktu</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Pengguna</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Aksi</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Target</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">IP Address</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Domain</th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase">User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-medium text-dark">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-soft-primary text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($log->user->nama_lengkap, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $log->user->nama_lengkap }}</div>
                                                <small class="text-muted">@{{ $log->user->username }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $actionColors = [
                                            'login' => 'success',
                                            'logout' => 'warning',
                                            'create' => 'info',
                                            'update' => 'primary',
                                            'delete' => 'danger',
                                            'verify' => 'success',
                                        ];
                                        $color = $actionColors[$log->action] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-soft-{{ $color }} text-{{ $color }} rounded-pill px-3">
                                        @if($log->action === 'login')
                                            <i class="fas fa-right-to-bracket me-1"></i>
                                        @elseif($log->action === 'logout')
                                            <i class="fas fa-right-from-bracket me-1"></i>
                                        @elseif($log->action === 'create')
                                            <i class="fas fa-plus me-1"></i>
                                        @elseif($log->action === 'update')
                                            <i class="fas fa-pen me-1"></i>
                                        @elseif($log->action === 'delete')
                                            <i class="fas fa-trash me-1"></i>
                                        @elseif($log->action === 'verify')
                                            <i class="fas fa-check me-1"></i>
                                        @endif
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->table_name)
                                        <div class="small">
                                            <span class="fw-medium">{{ $log->table_name }}</span>
                                            @if($log->record_id)
                                                <span class="text-muted">#{{ $log->record_id }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <code class="small bg-light px-2 py-1 rounded">{{ $log->ip_address ?? '-' }}</code>
                                </td>
                                <td>
                                    @if($log->domain)
                                        <span class="badge bg-light text-dark rounded-pill px-2">
                                            {{ $log->domain === 'desa' ? 'Desa' : 'Kecamatan' }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent ? Str::limit($log->user_agent, 30) : '-' }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">Tidak ada data audit log.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .bg-soft-primary {
            background: rgba(30, 66, 159, 0.1);
        }

        .bg-soft-success {
            background: rgba(16, 185, 129, 0.1);
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.1);
        }

        .bg-soft-info {
            background: rgba(14, 165, 233, 0.1);
        }

        .bg-soft-warning {
            background: rgba(245, 158, 11, 0.1);
        }

        .bg-soft-secondary {
            background: rgba(107, 114, 128, 0.1);
        }

        .text-info {
            color: #0ea5e9;
        }

        .btn-teal {
            background: #14b8a6;
            color: white;
            border: none;
        }

        .btn-teal:hover {
            background: #0d9488;
            color: white;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection
