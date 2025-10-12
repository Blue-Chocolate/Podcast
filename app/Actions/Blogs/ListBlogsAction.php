<?php

namespace App\Actions\Blogs;

use App\Repositories\BlogRepository;
use Illuminate\Support\Facades\Cache;

class ListBlogsAction
{
    protected $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        return Cache::remember('blogs_all', now()->addMinutes(30), function () {
            return $this->repository->all();
        });
    }
}
