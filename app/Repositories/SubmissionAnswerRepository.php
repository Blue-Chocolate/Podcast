<?php


namespace App\Repositories;

use App\Models\Submission_Answer;

class SubmissionAnswerRepository
{
    public function storeAxisAnswer(int $submissionId, array $axisAnswer): int
    {
        $q1 = isset($axisAnswer['q1']) ? (bool)$axisAnswer['q1'] : false;
        $q2 = isset($axisAnswer['q2']) ? (bool)$axisAnswer['q2'] : false;
        $q3 = isset($axisAnswer['q3']) ? (bool)$axisAnswer['q3'] : false;
        $q4 = isset($axisAnswer['q4']) ? (bool)$axisAnswer['q4'] : false;

        $axisPoints = ($q1?5:0) + ($q2?5:0) + ($q3?5:0) + ($q4?5:0);

        Submission_Answer::create([
            'submission_id' => $submissionId,
            'axis' => $axisAnswer['axis'],
            'q1' => $q1,
            'q2' => $q2,
            'q3' => $q3,
            'q4' => $q4,
            'axis_points' => $axisPoints,
            'notes' => $axisAnswer['notes'] ?? null,
        ]);

        return $axisPoints;
    }
}
