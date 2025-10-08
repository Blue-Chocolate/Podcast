<?php

namespace App\Actions\EpisodeFile;

use App\Repositories\EpisodeFileRepository;

class IndexEpisodeFilesAction
{
    protected $repository;

    public function __construct(EpisodeFileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($episodeId)
    {
        return $this->repository->getByEpisode($episodeId);
    }
}
