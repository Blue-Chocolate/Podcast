<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;

class ShowBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->find($id);
    }
}
