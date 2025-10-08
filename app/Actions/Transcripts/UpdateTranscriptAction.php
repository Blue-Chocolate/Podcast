<?php

namespace App\Actions\Transcripts;

use App\Repositories\TranscriptRepository;
use App\Models\Transcript;

class UpdateTranscriptAction
{
    protected $repo;

    public function __construct(TranscriptRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Transcript $transcript, array $data)
    {
        return $this->repo->update($transcript, $data);
    }
}
