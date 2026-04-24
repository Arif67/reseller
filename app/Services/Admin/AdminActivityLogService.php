<?php

namespace App\Services\Admin;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class AdminActivityLogService
{
    public function log(
        string $entityType,
        string $action,
        ?int $entityId = null,
        ?string $entityName = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        if (! Schema::hasTable('admin_activity_logs')) {
            return;
        }

        AdminActivityLog::query()->create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_name' => $entityName,
            'action' => $action,
            'user_id' => Auth::id(),
            'old_values' => $this->normalizePayload($oldValues),
            'new_values' => $this->normalizePayload($newValues),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    private function normalizePayload(?array $payload): ?array
    {
        if (empty($payload)) {
            return null;
        }

        return collect($payload)
            ->map(function ($value) {
                if (is_bool($value) || is_numeric($value) || is_null($value)) {
                    return $value;
                }

                if (is_array($value)) {
                    return $value;
                }

                return (string) $value;
            })
            ->all();
    }
}
