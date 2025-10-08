<?php

namespace App\Actions\Transcripts;

use App\Repositories\TranscriptRepository;

class CreateTranscriptAction
{
    protected $repo;

    public function __construct(TranscriptRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        return $this->repo->create($data);
    }
}
