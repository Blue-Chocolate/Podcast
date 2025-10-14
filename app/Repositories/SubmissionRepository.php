<?php
namespace App\Repositories;

use App\Models\Submission;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionResultMail;

class SubmissionRepository
{
    public function createBase(int $organizationId, array $data): Submission
    {
        $submission = Submission::create([
            'organization_id' => $organizationId,
            'status' => ($data['save_as_draft'] ?? false) ? 'draft' : 'submitted',
            'submitted_at' => ($data['save_as_draft'] ?? false) ? null : now(),
            'meta' => $data['meta'] ?? null,
        ]);
        
        // حساب وحفظ النتيجة لو مش Draft
        if (($data['save_as_draft'] ?? false) === false) {
            $score = $this->computeScore($submission);
            $submission->update(['total_score' => $score]);
            
            // إرسال الإيميل
            Mail::to($submission->organization->email)
                ->send(new SubmissionResultMail($submission));
        }
        
        return $submission;
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