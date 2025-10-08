<?php

namespace App\Actions\Episodes;

use App\Repositories\EpisodeRepository;

class ListEpisodesAction
{
    protected $repo;

    public function __construct(EpisodeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute()
    {
        return $this->repo->getAll();
    }
}
