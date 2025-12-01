<?php

namespace App\Http\Controllers\Api\PostController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Posts\{
    CreatePostAction,
    UpdatePostAction,
    DeletePostAction,
    ShowPostAction,
    ListPostsAction
};
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    public function index(ListPostsAction $listPosts)
    {
        $posts = $listPosts->execute();
        return response()->json(['data' => $posts]);
    }

    public function show($id, ShowPostAction $showPost)
    {
        try {
            $post = $showPost->execute($id);
            return response()->json(['data' => $post]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    public function store(Request $request, CreatePostAction $createPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'category' => 'nullable|string|max:100',
            'status' => 'in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'image' => 'nullable|string|max:255',
        ]);

        $post = $createPost->execute($validated);
        return response()->json(['data' => $post, 'message' => 'Post created successfully'], 201);
    }

    public function update(Request $request, $id, UpdatePostAction $updatePost)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'status' => 'in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'image' => 'nullable|string|max:255',
        ]);

        try {
            $post = $updatePost->execute($id, $validated);
            return response()->json(['data' => $post, 'message' => 'Post updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    public function destroy($id, DeletePostAction $deletePost)
    {
        try {
            $deletePost->execute($id);
            return response()->json(['message' => 'Post deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }
}
