<?php

namespace App\Actions\Categories;

use App\Repositories\CategoryRepository;

class GetCategoryByIdAction
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->find($id);
    }
}
