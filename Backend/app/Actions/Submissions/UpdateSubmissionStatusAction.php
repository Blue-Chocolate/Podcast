<?php

namespace App\Actions\Submissions;

use App\Repositories\SubmissionRepository;
use App\Repositories\AuditLogRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class UpdateSubmissionStatusAction
{
    public function __construct(
        protected SubmissionRepository $submissionRepo,
        protected AuditLogRepository $auditRepo
    ) {}

    public function execute(int $id, array $data, $user = null): array
    {
        try {
            if (!in_array($data['status'], ['draft', 'submitted', 'under_review', 'shortlisted', 'winner', 'rejected'])) {
                throw ValidationException::withMessages(['status' => 'Invalid status']);
            }

            $submission = $this->submissionRepo->updateStatus($id, $data['status'], $data['announced_at'] ?? null);

            $this->auditRepo->logAction(
                entityType: get_class($submission),
                entityId: $submission->id,
                action: 'status_changed',
                userId: $user?->id,
                notes: "Status changed to {$submission->status}"
            );

            return [
                'success' => true,
                'submission_id' => $submission->id,
                'status' => $submission->status
            ];
        } catch (Exception $e) {
            Log::error('UpdateSubmissionStatusAction failed: '.$e->getMessage());
            throw new Exception('Failed to update status: '.$e->getMessage());
        }
    }
}
