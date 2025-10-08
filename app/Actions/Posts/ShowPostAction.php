<?php

namespace App\Actions\Posts;

use App\Repositories\PostRepository;

class ShowPostAction
{
    protected $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        return $this->repository->find($id);
    }
}
