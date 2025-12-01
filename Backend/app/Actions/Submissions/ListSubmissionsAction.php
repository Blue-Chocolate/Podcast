<?php

namespace App\Actions\Submissions;

use App\Repositories\SubmissionRepository;
use Illuminate\Http\Request;

class ListSubmissionsAction
{
    public function __construct(protected SubmissionRepository $submissionRepo) {}

    public function execute(Request $req)
    {
        $filters = [
            'status' => $req->query('status'),
            'min_score' => $req->query('min_score'),
            'max_score' => $req->query('max_score'),
            'sector' => $req->query('sector'),
        ];
        $perPage = max(10, min(100, (int)$req->query('per_page', 20)));

        return $this->submissionRepo->getFilteredPaginated($filters, $perPage);
    }
}
