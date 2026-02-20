<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit logs with filtering
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by domain (desa/kecamatan)
        if ($request->filled('domain')) {
            $query->where('domain', $request->domain);
        }

        // Search by IP address
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ip_address', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('nama_lengkap', 'like', '%' . $request->search . '%')
                            ->orWhere('username', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $logs = $query->latest()->paginate(20)->appends($request->query());

        // Get unique actions for filter dropdown
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();

        // Get users for filter dropdown
        $users = \App\Models\User::orderBy('nama_lengkap')->get(['id', 'nama_lengkap', 'username']);

        return view('kecamatan.audit-logs.index', compact('logs', 'actions', 'users'));
    }

    /**
     * Display specific audit log detail
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('kecamatan.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs to CSV
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        // Apply same filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->limit(1000)->get();

        $filename = 'audit_logs_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Waktu', 'User', 'Username', 'Aksi', 'Tabel', 'Record ID', 'IP Address', 'User Agent', 'Domain']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->nama_lengkap ?? 'System',
                    $log->user?->username ?? '-',
                    $log->action,
                    $log->table_name,
                    $log->record_id,
                    $log->ip_address,
                    $log->user_agent,
                    $log->domain ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics for dashboard
     */
    public function stats()
    {
        $stats = [
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AuditLog::whereMonth('created_at', now()->month)->count(),
            'unique_visitors_today' => AuditLog::whereDate('created_at', today())
                ->distinct('ip_address')
                ->count('ip_address'),
            'top_actions' => AuditLog::selectRaw('action, COUNT(*) as count')
                ->whereDate('created_at', today())
                ->groupBy('action')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}
