<?php

namespace App\Http\Controllers\Api\BlogCategoryController;

use App\Http\Controllers\Controller;
use App\Repositories\BlogCategoryRepository;
use Illuminate\Http\Request;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    protected $repo;

    public function __construct(BlogCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * GET /api/categories/blogs?page=&limit=
     * Get all categories (with blogs limited per category)
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $categories = $this->repo->getAll($limit);

        return response()->json([
            'success' => true,
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
            ],
            'categories' => $categories->items(),
        ]);
    }

    /**
     * GET /api/categories/blogs/{category_id}?page=&limit=
     * Get one category with paginated blogs
     */
 public function show($category_id)
    {
        // Get the category with all its blogs
        $category = BlogCategory::with('blogs')->find($category_id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // Map blogs to include all details + category info
        $blogs = $category->blogs->map(function ($blog) use ($category) {
            return [
                'id' => $blog->id,
                'user_id' => $blog->user_id,
                'header_image' => $blog->header_image ? asset('storage/' . $blog->header_image) : null,
                'title' => $blog->title,
                'description' => $blog->description,
                'content' => $blog->content,
                'status' => $blog->status,
                'publish_date' => $blog->publish_date,
                'views' => $blog->views,
                'image' => $blog->image ? asset('storage/' . $blog->image) : null,
                'announcement' => $blog->announcement,
                'footer' => $blog->footer,

                // include category info in each blog
                'blog_category_id' => $category->id,
                'blog_category_name' => $category->name,
            ];
        });

        // Response
        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'image_path' => $category->image_path ? asset('storage/' . $category->image_path) : null,
                'slug' => $category->slug,
                'is_active' => $category->is_active,
                'views_count' => $category->views_count,

            ],
            'blogs' => $blogs,
        ]);
    }
    /**
     * GET /api/categories/blogs/{category_id}/blog/{blog_id}
     * Get a specific blog inside a category
     */
    public function showBlog($category_id, $blog_id)
    {
        $result = $this->repo->getBlogByCategory($category_id, $blog_id);

        if (!$result || !$result['blog']) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found in this category',
            ], 404);
        }

        $blog = $result['blog'];

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $result['category']['id'],
                'name' => $result['category']['name'],
                'description' => $result['category']['description'],
                'header_image' => $result['category']['header_image']
                    ? asset('storage/' . $result['category']['header_image'])
                    : null,
            ],
            'blog' => [
                'id' => $blog->id,
                'user_id' => $blog->user_id,
                'title' => $blog->title,
                'description' => $blog->description,
                'content' => $blog->content,
                'header_image' => $blog->header_image ? asset('storage/' . $blog->header_image) : null,
                'image' => $blog->image ? asset('storage/' . $blog->image) : null,
                'announcement' => $blog->announcement,
                'footer' => $blog->footer,
                'status' => $blog->status,
                'publish_date' => $blog->publish_date,
                'views' => $blog->views,
                'created_at' => $blog->created_at,
                'updated_at' => $blog->updated_at,
            ],
        ]);
    }
}
