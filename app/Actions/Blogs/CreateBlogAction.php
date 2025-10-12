<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;
use Illuminate\Support\Facades\Cache;


class CreateBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data)
    {
        $blog = \App\Models\Blog::create($data);
        Cache::forget('blogs_all'); // invalidate list cache
        return $blog;
    }
}
