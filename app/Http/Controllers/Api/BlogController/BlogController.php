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
use App\Models\Blog;

class BlogController extends Controller
{
    public function index(ListBlogsAction $action, Request $request)
    {
        $limit = $request->query('limit', 10); // Default = 10
        $page = $request->query('page', 1);    // Default = 1
        $offset = ($page - 1) * $limit;

        $blogs = Blog::offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = Blog::count();

        return response()->json([
            'data' => $blogs,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    }

    public function show($id, ShowBlogAction $action)
    {
        $blog = $action->execute($id);
        $blog->increment('views');

        return response()->json($blog);
    }

    public function store(Request $request, CreateBlogAction $action)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'announcement' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'footer' => 'nullable|string|max:255',
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
            'header_image' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'announcement' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'footer' => 'nullable|string|max:255',
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
