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
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionResultMail;
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
            // 🏢 1. Create or find organization
            $organization = $this->orgRepo->createOrFindByEmail($data['organization']);

            // 📝 2. Create submission base
            $submission = $this->subRepo->createBase($organization->id, $data);

            // 🧮 3. Handle answers
            $totalCombined = 0;
            foreach ($data['answers'] as $ans) {
                $axisPoints = $this->ansRepo->storeAxisAnswer($submission->id, $ans);
                $totalCombined += $axisPoints;
            }

            // 🎯 4. Compute total score (only if submitted, not draft)
            if (!($data['save_as_draft'] ?? false)) {
                $totalScore = $this->subRepo->computeScore($submission);
                $submission->update(['total_score' => $totalScore]);
            }

            // 📎 5. Attachments (optional)
            if ($req->hasFile('attachments')) {
                $this->attRepo->storeAttachments($req, $submission->id);
            }

            // 🧾 6. Audit log
            $this->logRepo->log(
                entity: $submission,
                action: ($data['save_as_draft'] ?? false) ? 'created_draft' : 'submitted',
                userId: $req->user()?->id,
                notes: 'Submission saved via API'
            );

            DB::commit();

            // ✉️ 7. Send result email (only if submitted)
            if (!($data['save_as_draft'] ?? false)) {
                Mail::to($organization->email)
                    ->send(new SubmissionResultMail($submission->fresh()));
            }

            // ✅ 8. Return response
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
