<?php

namespace App\Repositories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogRepository
{
    public function all()
    {
        return Blog::with('user', 'category')->latest()->get();
    }

    public function find($id)
    {
        $blog = Blog::with('user', 'category')->find($id);
        if (!$blog) {
            throw new ModelNotFoundException('Blog not found');
        }
        return $blog;
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
