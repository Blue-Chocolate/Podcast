<?php

namespace App\Actions\Podcast;

use App\Repositories\PodcastRepository;

class DeletePodcastAction
{
    protected PodcastRepository $repo;

    public function __construct(PodcastRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(int $id)
    {
        return $this->repo->delete($id);
    }
}
