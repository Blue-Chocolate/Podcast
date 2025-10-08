<?php

namespace App\Actions\Person;

use App\Repositories\PersonRepository;

class IndexPersonAction
{
    protected $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        return $this->repository->all();
    }
}
