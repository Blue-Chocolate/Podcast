<?php

namespace App\Actions\Submissions;

use App\Repositories\SubmissionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ShowSubmissionAction
{
    public function __construct(protected SubmissionRepository $submissionRepo) {}

    public function execute(int $id): array
    {
        try {
            $submission = $this->submissionRepo->getByIdWithRelations($id);
            return ['success' => true, 'data' => $submission];
        } catch (ModelNotFoundException) {
            return ['success' => false, 'message' => 'Submission not found'];
        } catch (Exception $e) {
            throw new Exception('Failed to fetch submission: '.$e->getMessage());
        }
    }
}
