<?php

namespace App\Actions\EpisodeFile;

use App\Repositories\EpisodeFileRepository;

class UpdateEpisodeFileAction
{
    protected $repository;

    public function __construct(EpisodeFileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id, array $data)
    {
        $episodeFile = $this->repository->find($id);
        return $this->repository->update($episodeFile, $data);
    }
}
