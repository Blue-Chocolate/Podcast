<?php

namespace App\Actions\Categories;

use App\Repositories\CategoryRepository;

class DeleteCategoryAction
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->delete($id);
    }
}
