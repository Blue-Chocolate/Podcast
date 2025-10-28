<?php

namespace App\Http\Controllers\Api\Doc_Videos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Doc_Videos\{
    CreateDocVideoAction,
    UpdateDocVideoAction,
    ShowDocVideoAction,
    DeleteDocVideoAction
};
use App\Models\doc_videos as DocVideoModel;

use App\Repositories\DocVideoRepository;

class Doc_Videos extends Controller

{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $doc_videos = $this->repo->all($limit, $offset);
        $total = $this->repo->count();

        return response()->json([
            'data' => $doc_videos,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ],
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
}
