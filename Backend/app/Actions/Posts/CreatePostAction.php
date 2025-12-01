<?php

namespace App\Actions\Posts;

use App\Repositories\PostRepository;

class CreatePostAction
{
    protected $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data)
    {
        return $this->repository->create($data);
    }
}
