<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;

class CreateBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data)
    {
        return $this->repository->create($data);
    }
}
