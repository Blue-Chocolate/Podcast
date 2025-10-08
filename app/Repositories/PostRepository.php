<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function all()
    {
        return Post::with('user')->latest()->get();
    }

    public function find($id)
    {
        return Post::with('user')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data)
    {
        $post->update($data);
        return $post;
    }

    public function delete(Post $post)
    {
        return $post->delete();
    }
}
