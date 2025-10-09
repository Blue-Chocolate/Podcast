<?php

namespace App\Http\Controllers\Api\BlogController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BlogRepository;
use App\Actions\Blogs\{
    CreateBlogAction,
    UpdateBlogAction,
    DeleteBlogAction,
    ShowBlogAction,
    ListBlogsAction
};

class BlogController extends Controller
{
    public function index(ListBlogsAction $action)
    {
        $blogs = $action->execute();
        return response()->json($blogs);
    }

    public function show($id, ShowBlogAction $action)
    {
        $blog = $action->execute($id);
        return response()->json($blog);
    }

    public function store(Request $request, CreateBlogAction $action)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog = $action->execute($data);

        return response()->json([
            'message' => 'Blog created successfully',
            'data' => $blog
        ], 201);
    }

    public function update(Request $request, $id, UpdateBlogAction $action, BlogRepository $repository)
    {
        $blog = $repository->find($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog = $action->execute($blog, $data);

        return response()->json([
            'message' => 'Blog updated successfully',
            'data' => $blog
        ]);
    }

    public function destroy($id, DeleteBlogAction $action, BlogRepository $repository)
    {
        $blog = $repository->find($id);
        $action->execute($blog);

        return response()->json(['message' => 'Blog deleted successfully']);
    }
}
