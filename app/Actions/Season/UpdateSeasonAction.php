<?php

namespace App\Actions\Season;

use App\Repositories\SeasonRepository;

class UpdateSeasonAction
{
    protected SeasonRepository $repo;

    public function __construct(SeasonRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }
}
