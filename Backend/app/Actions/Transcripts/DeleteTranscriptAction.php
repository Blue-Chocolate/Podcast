<?php

namespace App\Actions\Transcripts;

use App\Repositories\TranscriptRepository;
use App\Models\Transcript;

class DeleteTranscriptAction
{
    protected $repo;

    public function __construct(TranscriptRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Transcript $transcript)
    {
        return $this->repo->delete($transcript);
    }
}
