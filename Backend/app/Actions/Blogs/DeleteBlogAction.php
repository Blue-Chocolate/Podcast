<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class DeleteBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

     public function execute($blog)
    {
        $blog->delete();

        Cache::forget('blogs_all');
        Cache::forget("blog_{$blog->id}");
    }
}
