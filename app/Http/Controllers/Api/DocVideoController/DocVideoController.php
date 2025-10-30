<?php

namespace App\Http\Controllers\Api\DocVideoController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\DocVideo\{
    CreateDocVideoAction,
    UpdateDocVideoAction,
    ShowDocVideoAction,
    DeleteDocVideoAction
};
use App\Models\DocVideo as DocVideoModel;
use App\Models\Category;
use App\Repositories\DocVideoRepository;

class DocVideoController extends Controller
{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
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
    $categories = Category::whereHas('docVideos')
        ->with(['docVideos' => function ($query) {
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

    public function show($id, ShowDocVideoAction $showAction)
    {
        $doc_video = $showAction->execute($id);
        $doc_video->increment('views_count');

        return response()->json($doc_video);
    }

    public function store(Request $request, CreateDocVideoAction $createAction)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'video_path' => 'required|string',
        ]);

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('doc_videos', 'public');
        }

        $doc_video = $createAction->execute($data);

        return response()->json([
            'message' => 'Doc video created successfully',
            'data' => $doc_video
        ], 201);
    }

    public function update(Request $request, $id, UpdateDocVideoAction $updateAction)
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

    public function destroy($id, DeleteDocVideoAction $deleteAction)
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

        $videos = DocVideoModel::where('category_id', $id)
            ->with('category:id,name')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = DocVideoModel::where('category_id', $id)->count();

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
        $doc_video = DocVideoModel::with('category:id,name')
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
    $limit = $request->query('limit', 10);

    $videos = \App\Models\DocVideo::leftJoin('categories', 'doc_videos.category_id', '=', 'categories.id')
        ->select(
            'doc_videos.id',
            'doc_videos.title',
            'doc_videos.description',
            'doc_videos.video_path',
            'doc_videos.image_path',
            'doc_videos.created_at',
            'doc_videos.category_id',
            'categories.name as category_name'
        )
        ->orderBy('doc_videos.created_at', 'desc')
        ->paginate($limit);

    if ($videos->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No videos found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $videos,
        'message' => 'Videos retrieved successfully'
    ]);
}

}
