<?php

namespace App\Actions\Podcast;

use App\Repositories\PodcastRepository;

class UpdatePodcastAction
{
    protected PodcastRepository $repo;

    public function __construct(PodcastRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }
}
