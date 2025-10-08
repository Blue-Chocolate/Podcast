<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;

class ListBlogsAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        return $this->repository->all();
    }
}
