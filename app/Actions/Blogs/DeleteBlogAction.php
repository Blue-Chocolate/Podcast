<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;
use App\Models\Blog;

class DeleteBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(Blog $blog)
    {
        return $this->repository->delete($blog);
    }
}
