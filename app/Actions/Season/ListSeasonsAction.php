<?php

namespace App\Actions\Season;

use App\Repositories\SeasonRepository;

class ListSeasonsAction
{
    protected SeasonRepository $repo;

    public function __construct(SeasonRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute()
    {
        return $this->repo->all();
    }
}
