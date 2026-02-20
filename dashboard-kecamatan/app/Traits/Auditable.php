<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction('create', $model);
        });

        static::updated(function ($model) {
            self::logAction('update', $model);
        });

        static::deleted(function ($model) {
            self::logAction('delete', $model);
        });
    }

    protected static function logAction($action, $model)
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        $user = Auth::user();

        // Skip if no user is authenticated (unless it's a login action, handled elsewhere)
        if (!$user) {
            return;
        }

        $oldValues = null;
        $newValues = null;

        if ($action === 'update') {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newValues = $model->getDirty();

            // Don't log if no actual values changed (protection against dummy saves)
            if (empty($newValues)) {
                return;
            }
        } elseif ($action === 'create') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'delete') {
            $oldValues = $model->getAttributes();
        }

        // Sensitive fields filter
        $sensitiveFields = ['password', 'remember_token'];
        if ($oldValues) {
            foreach ($sensitiveFields as $field) {
                unset($oldValues[$field]);
            }
        }
        if ($newValues) {
            foreach ($sensitiveFields as $field) {
                unset($newValues[$field]);
            }
        }

        AuditLog::create([
            'user_id' => $user->id,
            'domain' => $user->role ? (str_contains(strtolower($user->role->nama_role), 'desa') ? 'desa' : 'kecamatan') : 'kecamatan',
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
