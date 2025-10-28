<?php

namespace App\Actions\Doc_Videos;

use App\Repositories\DocVideoRepository;
use App\Models\doc_videos;

class UpdateDocVideoAction
{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(doc_videos $doc_video, array $data)
    {
        return $this->repo->update($doc_video, $data);
    }
}
