<?php

namespace App\Actions\Transcripts;

use App\Repositories\TranscriptRepository;

class ListTranscriptsAction
{
    protected $repo;

    public function __construct(TranscriptRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute()
    {
        return $this->repo->getAll();
    }
}
