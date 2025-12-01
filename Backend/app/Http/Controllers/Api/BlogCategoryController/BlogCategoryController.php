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
    $limit = $request->query('limit', 10);
    $page = $request->query('page', 1);

    $categories = $this->repo->getAll($limit, $page);

    // Map categories to your desired structure
    $data = $categories->getCollection()->map(function ($category) {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image_path ? asset('storage/' . $category->image_path) : null,
            'description' => $category->description,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $data,
        'pagination' => [
            'current_page' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
            'total_items' => $categories->total(),
            'last_page' => $categories->lastPage(),
        ],
    ]);
}
    /**
     * GET /api/categories/blogs/{category_id}?page=&limit=
     * Get one category with paginated blogs
     */
 public function show(Request $request, $category_id)
{
    // Pagination inputs
    $perPage = $request->query('limit', 10);
    $page = $request->query('page', 1);

    // Find category
    $category = BlogCategory::find($category_id);

    if (!$category) {
        return response()->json([
            'success' => false,
            'message' => 'Category not found',
        ], 404);
    }

    // Get paginated blogs for this category
    $blogsQuery = $category->blogs()->paginate($perPage, ['*'], 'page', $page);

    // Transform blogs to match your structure
    $blogs = $blogsQuery->getCollection()->map(function ($blog) use ($category) {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'description' => $blog->description,
            'views' => $blog->views,
            'image' => $blog->image ? asset('storage/' . $blog->image) : null,
            'published_at' => $blog->publish_date,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
        ];
    });

    // Build response
    return response()->json([
        'success' => true,
        'category' => [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image_path ? asset('storage/' . $category->image_path) : null,
            'description' => $category->description,
        ],
        'blogs' => $blogs,
        'pagination' => [
            'current_page' => $blogsQuery->currentPage(),
            'per_page' => $blogsQuery->perPage(),
            'total_items' => $blogsQuery->total(),
            'last_page' => $blogsQuery->lastPage(),
        ],
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
