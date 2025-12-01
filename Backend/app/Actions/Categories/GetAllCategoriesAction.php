<?php

namespace App\Actions\Categories;

use App\Repositories\CategoryRepository;

class GetAllCategoriesAction
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        return $this->repository->getAll();
    }
}
