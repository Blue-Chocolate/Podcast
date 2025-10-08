<?php

namespace App\Actions\Person;

use App\Repositories\PersonRepository;

class UpdatePersonAction
{
    protected $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id, array $data)
    {
        return $this->repository->update($id, $data);
    }
}
