<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;
use Illuminate\Support\Facades\Cache;

class ShowBlogAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id)
    {
        $cacheKey = "blog_{$id}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($id) {
            return $this->repository->find($id);
        });
    }
}
