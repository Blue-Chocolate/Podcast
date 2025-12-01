<?php

namespace App\Actions\Episodes;

use App\Repositories\EpisodeRepository;

class CreateEpisodeAction
{
    protected $repo;

    public function __construct(EpisodeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        return $this->repo->store($data);
    }
}
