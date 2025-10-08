<?php

namespace App\Actions\EpisodeFile;

use App\Repositories\EpisodeFileRepository;

class DeleteEpisodeFileAction
{
    protected $repository;

    public function __construct(EpisodeFileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        $episodeFile = $this->repository->find($id);
        return $this->repository->delete($episodeFile);
    }
}
