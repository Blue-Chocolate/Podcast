<?php

namespace App\Actions\Posts;

use App\Repositories\PostRepository;

class ListPostsAction
{
    protected $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        return $this->repository->all();
    }
}
