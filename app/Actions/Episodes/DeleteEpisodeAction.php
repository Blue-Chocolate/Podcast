<?php

namespace App\Actions\Episodes;

use App\Repositories\EpisodeRepository;
use App\Models\Episode;

class DeleteEpisodeAction
{
    protected $repo;

    public function __construct(EpisodeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Episode $episode)
    {
        return $this->repo->delete($episode);
    }
}
