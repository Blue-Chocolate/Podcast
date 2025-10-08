<?php

namespace App\Actions\Person;

use App\Repositories\PersonRepository;

class ListPeopleAction
{
    protected $repo;

    public function __construct(PersonRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute()
    {
        return $this->repo->all();
    }
}
