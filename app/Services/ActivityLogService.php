<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogService
{
    public function log(?User $user, string $action, ?string $description = null, ?array $metadata = null, ?string $ipAddress = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }
}
