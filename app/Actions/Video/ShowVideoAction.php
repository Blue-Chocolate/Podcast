<?php

namespace App\Actions\Video;

use App\Repositories\VideoRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShowVideoAction
{
    protected $repo;

    public function __construct(VideoRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Execute action to get a video by ID including category info
     *
     * @param int $id
     * @return \App\Models\Video
     *
     * @throws ModelNotFoundException
     */
    public function execute($id)
    {
        return $this->repo->find($id);
    }
}
