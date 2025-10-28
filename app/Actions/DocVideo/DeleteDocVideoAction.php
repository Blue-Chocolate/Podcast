<?php

namespace App\Actions\DocVideo;

use App\Repositories\DocVideoRepository;
use App\Models\DocVideo;

class DeleteDocVideoAction
{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(DocVideo $doc_video)
    {
        return $this->repo->delete($doc_video);
    }
}
