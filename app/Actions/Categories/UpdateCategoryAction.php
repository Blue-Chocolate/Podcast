<?php

namespace App\Actions\Categories;

use App\Repositories\CategoryRepository;

class UpdateCategoryAction
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id, array $data)
    {
        return $this->repository->update($id, $data);
    }
}
