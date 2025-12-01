<?php

namespace App\Actions\Video;

use App\Repositories\VideoRepository;
use App\Models\Video;

class DeleteVideoAction
{
    protected $repo;

    public function __construct(VideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Video $doc_video)
    {
        return $this->repo->delete($doc_video);
    }
}
