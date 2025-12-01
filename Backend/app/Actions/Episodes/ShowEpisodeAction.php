<?php

namespace App\Actions\Episodes;

use App\Repositories\EpisodeRepository;

class ShowEpisodeAction
{
    protected $repo;

    public function __construct(EpisodeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($id)
    {
        return $this->repo->find($id);
    }
}
