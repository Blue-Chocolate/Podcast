<?php

namespace App\Actions\Episodes;

use App\Repositories\EpisodeRepository;
use App\Models\Episode;

class UpdateEpisodeAction
{
    protected $repo;

    public function __construct(EpisodeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Episode $episode, array $data)
    {
        return $this->repo->update($episode, $data);
    }
}
