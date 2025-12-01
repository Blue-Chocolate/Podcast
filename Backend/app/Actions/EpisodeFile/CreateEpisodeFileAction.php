<?php

namespace App\Actions\EpisodeFile;

use App\Repositories\EpisodeFileRepository;

class CreateEpisodeFileAction
{
    protected $repository;

    public function __construct(EpisodeFileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data)
    {
        return $this->repository->create($data);
    }
}
