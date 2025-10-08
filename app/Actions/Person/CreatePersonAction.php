<?php

namespace App\Actions\Person;

use App\Repositories\PersonRepository;

class CreatePersonAction
{
    protected $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data)
    {
        return $this->repository->create($data);
    }
}
