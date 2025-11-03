<?php

namespace App\Http\Controllers\Api\VideoController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Video\{
    CreateVideoAction,
    UpdateVideoAction,
    ShowVideoAction,
    DeleteVideoAction
};
use App\Models\Video as VideoModel;
use App\Models\Category;
use App\Repositories\VideoRepository;

class VideoController extends Controller
{
    protected $repo;

    public function __construct(VideoRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Fetch all categories that have at least one doc video,
     * with their videos included.
     */
   public function index(Request $request)
{
    $limit = $request->query('limit', 10);

    // ✅ Fetch only categories that exist in doc_videos table
    $categories = Category::whereHas('Videos')
        ->with(['Videos' => function ($query) {
            $query->select('id', 'title', 'description', 'category_id',  'image_path', 'created_at');
        }])
        ->select('id', 'name')
        ->paginate($limit);

    if ($categories->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No categories with videos found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $categories,
        'message' => 'Categories with videos retrieved successfully'
    ]);
}

 public function show($id, ShowVideoAction $showAction)
{
    try {
        $video = $showAction->execute($id);

        // Increment views count
        $video->increment('views_count');

        // Return structured JSON response
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $video->id,
                'title' => $video->title,
                'short_description' => $video->short_description,
                'description' => $video->description,
                'views' => $video->views_count,
                'image' => $video->image_path ? asset('storage/' . $video->image_path) : null,
                'video_url' => $video->video_path,
                'published_at' => $video->created_at?->timestamp,
                'category' => $video->category ? [
                    'id' => $video->category->id,
                    'name' => $video->category->name,
                ] : null,
            ],
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Video not found',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
        ], 500);
    }
}

    public function update(Request $request, $id, UpdateVideoAction $updateAction)
    {
        $doc_video = $this->repo->find($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'video_path' => 'nullable|string',
        ]);

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('doc_videos', 'public');
        }

        $doc_video = $updateAction->execute($doc_video, $data);

        return response()->json([
            'message' => 'Doc video updated successfully',
            'data' => $doc_video
        ]);
    }

    public function destroy($id, DeleteVideoAction $deleteAction)
    {
        $doc_video = $this->repo->find($id);
        $deleteAction->execute($doc_video);

        return response()->json(['message' => 'Doc video deleted successfully']);
    }

    public function getByCategory($id, Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $videos = VideoModel::where('category_id', $id)
            ->with('category:id,name')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = VideoModel::where('category_id', $id)->count();

        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No videos found for this category'], 404);
        }

        return response()->json([
            'data' => $videos,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    }

    public function showInCategory($category_id, $video_id)
    {
        $doc_video = VideoModel::with('category:id,name')
            ->where('category_id', $category_id)
            ->where('id', $video_id)
            ->first();

        if (!$doc_video) {
            return response()->json(['error' => 'Video not found in this category'], 404);
        }

        $doc_video->increment('views_count');

        return response()->json([
            'message' => 'Video retrieved successfully',
            'data' => $doc_video
        ]);
    }
public function videosList(Request $request)
{
    try {
        $limit = (int) $request->query('limit', 10);

        // Fetch videos with their category info
        $videos = \App\Models\Video::with('category:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        if ($videos->total() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No videos found',
            ], 404);
        }

        // Transform each video to match the desired structure
        $videoData = $videos->getCollection()->transform(function ($video) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'views' => $video->views_count ?? 0,
                'image' => $video->image_path ? asset('storage/' . ltrim($video->image_path, '/')) : null,
                'published_at' => $video->created_at ? $video->created_at->timestamp : null,
                'category' => [
                    'id' => $video->category?->id,
                    'name' => $video->category?->name,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $videoData,
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'per_page' => $videos->perPage(),
                'total_items' => $videos->total(),
                'last_page' => $videos->lastPage(),
            ],
        ]);
    } catch (Exception $e) {
        Log::error('Error fetching videos list', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching videos',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

}
