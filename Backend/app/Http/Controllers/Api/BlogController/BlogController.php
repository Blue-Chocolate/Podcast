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
use App\Models\BlogCategory;
use Exception;

class BlogController extends Controller
{
    /**
     * Display a paginated list of blogs.
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $blogs = Blog::with('category:id,name')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'title',
                'description',
                'views',
                'image',
                'publish_date',
                'blog_category_id'
            ]);

        $total = Blog::count();

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total_items' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    }

    /**
     * Show a single blog and increment views.
     */
    public function show($id, ShowBlogAction $action)
    {
        $blog = $action->execute($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        $blog->increment('views');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'description' => $blog->description,
                'content' => $blog->content,
                'views' => $blog->views,
                'image' => $blog->image,
                'published_at' => $blog->publish_date,
                'header_image' => $blog->header_image ?? null,
                'announcement' => $blog->announcement,
                'user_name' => $blog->user->name ?? null,
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Update an existing blog.
     */
    public function update(Request $request, $id, UpdateBlogAction $action, BlogRepository $repository)
    {
        $blog = $repository->find($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'nullable|in:draft,published,archived',
            'publish_date' => 'nullable|date',
            'announcement' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'footer' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $updatedBlog = $action->execute($blog, $data);

        return response()->json([
            'message' => 'Blog updated successfully',
            'data' => $updatedBlog
        ]);
    }

    /**
     * Delete a blog.
     */
    public function destroy($id, DeleteBlogAction $action, BlogRepository $repository)
    {
        $blog = $repository->find($id);
        $action->execute($blog);

        return response()->json(['message' => 'Blog deleted successfully']);
    }

    /**
     * List all available blog categories.
     */
    public function categories()
    {
        $categories = BlogCategory::select('id', 'name', 'slug')->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * List blogs by blog category ID.
     */
    public function categoryBlogs(Request $request, $blog_category_id)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $category = BlogCategory::findOrFail($blog_category_id);

        $query = Blog::where('blog_category_id', $blog_category_id);
        $total = $query->count();

        $blogs = $query->with('category:id,name')
            ->offset($offset)
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

    /**
     * List blog categories that have at least one blog.
     */
    public function categoriesWithBlogs(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $query = BlogCategory::whereHas('blogs')->withCount('blogs');
        $total = $query->count();

        $categories = $query->offset($offset)
            ->limit($limit)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'data' => $categories,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ]
        ]);
    }

    /**
     * List blog categories (with specific IDs) that have blogs.
     */
    public function categoriesWithBlogsWithIds(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $categoryIds = $request->query('ids', []);

        $query = BlogCategory::whereIn('id', $categoryIds)
            ->whereHas('blogs')
            ->withCount('blogs');

        $total = $query->count();

        $categories = $query->offset($offset)
            ->limit($limit)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'data' => $categories,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ]
        ]);
    }

    /**
     * Get a specific blog category with its blogs.
     */
    public function categoryWithBlogsById($id, Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $category = BlogCategory::where('id', $id)
            ->whereHas('blogs')
            ->with(['blogs' => function ($query) use ($limit, $offset) {
                $query->offset($offset)
                    ->limit($limit)
                    ->orderBy('created_at', 'desc');
            }])
            ->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found or has no blogs'], 404);
        }

        $totalBlogs = $category->blogs()->count();

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'blogs' => $category->blogs,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $totalBlogs,
                'last_page' => ceil($totalBlogs / $limit),
            ]
        ]);
    }
}
