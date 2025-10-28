<?php

namespace App\Actions\DocVideo;

use App\Repositories\DocVideoRepository;
use App\Models\DocVideo;

class UpdateDocVideoAction
{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(DocVideo $doc_video, array $data)
    {
        return $this->repo->update($doc_video, $data);
    }
}
