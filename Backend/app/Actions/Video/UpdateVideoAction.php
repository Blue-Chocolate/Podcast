<?php

namespace App\Actions\Video;

use App\Repositories\VideoRepository;
use App\Models\Video;

class UpdateVideoAction
{
    protected $repo;

    public function __construct(VideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Video $doc_video, array $data)
    {
        return $this->repo->update($doc_video, $data);
    }
}
