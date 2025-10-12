<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;


class UpdateBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

     public function execute($blog, array $data)
    {
        $blog->update($data);

        Cache::forget('blogs_all');
        Cache::forget("blog_{$blog->id}");

        return $blog;
    }
}
