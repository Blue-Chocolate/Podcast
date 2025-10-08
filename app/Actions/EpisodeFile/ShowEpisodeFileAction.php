<?php

namespace App\Actions\EpisodeFile;

use App\Repositories\EpisodeFileRepository;

class ShowEpisodeFileAction
{
    protected $repository;

    public function __construct(EpisodeFileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->findById($id);
    }
}
