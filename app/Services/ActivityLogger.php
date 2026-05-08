<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(
        string $module,
        string $action,
        ?string $description = null
    ): void {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'module'     => $module,
            'action'     => $action,
            'description'=> $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
