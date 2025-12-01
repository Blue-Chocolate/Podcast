<?php

namespace App\Actions\Posts;

use App\Repositories\PostRepository;
use App\Models\Post;

class DeletePostAction
{
    protected $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(Post $post)
    {
        return $this->repository->delete($post);
    }
}
