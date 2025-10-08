<?php

namespace App\Actions\Posts;

use App\Repositories\PostRepository;
use App\Models\Post;

class UpdatePostAction
{
    protected $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(Post $post, array $data)
    {
        return $this->repository->update($post, $data);
    }
}
