<?php

namespace App\Actions\Person;

use App\Repositories\PersonRepository;

class ShowPersonAction
{
    protected $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->find($id);
    }
}
