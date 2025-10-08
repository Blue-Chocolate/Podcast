<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    public function all()
    {
        return Blog::with('user')->latest()->get();
    }

    public function find($id)
    {
        return Blog::with('user')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Blog::create($data);
    }

    public function update(Blog $blog, array $data)
    {
        $blog->update($data);
        return $blog;
    }

    public function delete(Blog $blog)
    {
        return $blog->delete();
    }
}
