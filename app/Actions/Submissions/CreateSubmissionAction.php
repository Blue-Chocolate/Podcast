<?php

namespace App\Actions\Submissions;

use App\Repositories\{
    SubmissionRepository,
    OrganizationRepository,
    SubmissionAnswerRepository,
    AttachmentRepository,
    AuditLogRepository
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CreateSubmissionAction
{
    public function __construct(
        protected SubmissionRepository $subRepo,
        protected OrganizationRepository $orgRepo,
        protected SubmissionAnswerRepository $ansRepo,
        protected AttachmentRepository $attRepo,
        protected AuditLogRepository $logRepo,
    ) {}

    public function execute($req): array
    {
        $data = $req->validated();
        DB::beginTransaction();

        try {
            $organization = $this->orgRepo->createOrFindByEmail($data['organization']);
            $submission = $this->subRepo->createBase($organization->id, $data);

            // Handle answers
            $totalCombined = 0;
            foreach ($data['answers'] as $ans) {
                $axisPoints = $this->ansRepo->storeAxisAnswer($submission->id, $ans);
                $totalCombined += $axisPoints;
            }

            // Compute total score
            if (!($data['save_as_draft'] ?? false)) {
                $totalScore = $this->subRepo->computeScore($submission);
                $submission->update(['total_score' => $totalScore]);
            }

            // Attachments
            if ($req->hasFile('attachments')) {
                $this->attRepo->storeAttachments($req, $submission->id);
            }

            // Audit
            $this->logRepo->log(
                entity: $submission,
                action: ($data['save_as_draft'] ?? false) ? 'created_draft' : 'submitted',
                userId: $req->user()?->id,
                notes: 'Submission saved via API'
            );

            DB::commit();

            return [
                'success' => true,
                'submission_id' => $submission->id,
                'status' => $submission->status,
                'total_score' => $submission->total_score,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CreateSubmissionAction failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create submission',
                'error' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }
}
