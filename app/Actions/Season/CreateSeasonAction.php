<?php

namespace App\Actions\Season;

use App\Repositories\SeasonRepository;

class CreateSeasonAction
{
    protected SeasonRepository $repo;

    public function __construct(SeasonRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        return $this->repo->create($data);
    }
}
