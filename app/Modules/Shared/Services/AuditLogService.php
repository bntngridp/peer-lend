<?php

namespace App\Modules\Shared\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Write an audit log entry to database.
     */
    public function log(string $action, string $modelType, string $modelId, ?User $user = null, array $payload = []): AuditLog
    {
        $userId = $user ? $user->id : (Auth::check() ? Auth::id() : null);

        return AuditLog::create([
            'user_id'    => $userId,
            'action'     => $action,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'payload'    => $payload,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'CLI/Test',
        ]);
    }
}
