<?php

namespace App\Actions\Transcripts;

use App\Repositories\TranscriptRepository;

class ShowTranscriptAction
{
    protected $repo;

    public function __construct(TranscriptRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($id)
    {
        return $this->repo->find($id);
    }
}
