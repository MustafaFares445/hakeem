<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminActionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

final readonly class AdminActionLogService
{
    public function __construct(private Request $request) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function log(
        string $action,
        ?User $adminUser,
        ?string $tenantId = null,
        ?Model $target = null,
        ?string $description = null,
        array $metadata = [],
    ): AdminActionLog {
        return AdminActionLog::query()->create([
            'admin_user_id' => $adminUser?->id,
            'tenant_id' => $tenantId,
            'action' => $action,
            'target_type' => $target?->getMorphClass(),
            'target_id' => $target?->getKey(),
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'created_at' => now(),
        ]);
    }
}
