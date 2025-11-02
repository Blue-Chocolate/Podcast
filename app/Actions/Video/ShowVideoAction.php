<?php

namespace App\Actions\Video;

use App\Repositories\VideoRepository;

class ShowVideoAction
{
    protected $repo;

    public function __construct(VideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($id)
    {
        return $this->repo->find($id);
    }
}
