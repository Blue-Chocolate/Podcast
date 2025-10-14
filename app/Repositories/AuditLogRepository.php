<?php 


namespace App\Repositories;

use App\Models\Audit_Log;

class AuditLogRepository
{
    public function log($entity, string $action, ?int $userId = null, ?string $notes = null): void
    {
       Audit_Log::create([
    'entity_type' => get_class($entity),
    'entity_id' => $entity->id,
    'action' => $action,
    'user_id' => $userId,
    'notes' => $notes,
]);

    }
}
