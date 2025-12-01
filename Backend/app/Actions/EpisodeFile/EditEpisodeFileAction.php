<?php

namespace App\Actions\EpisodeFile;

use App\Repositories\EpisodeFileRepository;

class EditEpisodeFileAction
{
    protected $repository;

    public function __construct(EpisodeFileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->find($id);
    }
}
