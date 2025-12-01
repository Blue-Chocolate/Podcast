<?php

namespace App\Actions\Categories;

use App\Repositories\CategoryRepository;

class CreateCategoryAction
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data)
    {
        return $this->repository->create($data);
    }
}
