<?php

namespace App\Actions\Person;

use App\Repositories\PersonRepository;

class DeletePersonAction
{
    protected $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->delete($id);
    }
}
