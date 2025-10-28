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
use App\Models\Category;

class BlogController extends Controller
{
    public function index(ListBlogsAction $action, Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
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
            'category_id' => 'nullable|exists:categories,id', // تعديل هنا
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
            'category_id' => 'nullable|exists:categories,id', // تعديل هنا
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

    // ✅ List all categories
    public function categories()
    {
        $categories = Category::select('id', 'name', 'slug')->get(); // تعديل هنا

        return response()->json([
            'categories' => $categories
        ]);
    }

    // ✅ List blogs by category_id
    public function categoryBlogs(Request $request, $category_id)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $category = Category::findOrFail($category_id);

        $query = Blog::where('category_id', $category_id);

        $total = $query->count();
        $blogs = $query->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name
            ],
            'data' => $blogs,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    }
}
