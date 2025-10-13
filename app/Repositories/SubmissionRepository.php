<?php
namespace App\Repositories;

use App\Models\Submission;

class SubmissionRepository
{
    public function createBase(int $organizationId, array $data): Submission
    {
        return Submission::create([
            'organization_id' => $organizationId,
            'status' => ($data['save_as_draft'] ?? false) ? 'draft' : 'submitted',
            'submitted_at' => ($data['save_as_draft'] ?? false) ? null : now(),
            'meta' => $data['meta'] ?? null,
        ]);
    }

    public function computeScore(Submission $submission): float
    {
        $answers = $submission->answers()->get();
        $totalScore = 0;
        foreach ($answers as $ans) {
            $totalScore += ($ans->axis_points / 20) * 25;
        }
        return round($totalScore, 2);
    }
}
