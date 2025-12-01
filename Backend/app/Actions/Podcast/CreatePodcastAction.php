<?php

namespace App\Actions\Podcast;

use App\Repositories\PodcastRepository;

class CreatePodcastAction
{
    protected PodcastRepository $repo;

    public function __construct(PodcastRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        // business logic can be added here
        return $this->repo->create($data);
    }
}
