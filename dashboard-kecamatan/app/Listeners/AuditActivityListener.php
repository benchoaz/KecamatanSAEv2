<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class AuditActivityListener
{
    public function handleLogin(Login $event)
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        AuditLog::create([
            'user_id' => $user->id,
            'domain' => $user->role ? (str_contains(strtolower($user->role->nama_role), 'desa') ? 'desa' : 'kecamatan') : 'kecamatan',
            'action' => 'login',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function handleLogout(Logout $event)
    {
        if (!$event->user) {
            return;
        }

        /** @var \App\Models\User $user */
        $user = $event->user;

        AuditLog::create([
            'user_id' => $user->id,
            'domain' => $user->role ? (str_contains(strtolower($user->role->nama_role), 'desa') ? 'desa' : 'kecamatan') : 'kecamatan',
            'action' => 'logout',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function subscribe($events)
    {
        $events->listen(
            Login::class,
            [AuditActivityListener::class, 'handleLogin']
        );

        $events->listen(
            Logout::class,
            [AuditActivityListener::class, 'handleLogout']
        );
    }
}
