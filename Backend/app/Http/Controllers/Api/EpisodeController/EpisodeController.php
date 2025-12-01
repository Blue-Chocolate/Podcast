<?php

namespace App\Http\Controllers\Api\EpisodeController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Episode;
use Illuminate\Validation\ValidationException;
use App\Actions\Episodes\{
    CreateEpisodeAction,
    UpdateEpisodeAction,
    DeleteEpisodeAction,
    ShowEpisodeAction,
    ListEpisodesAction
};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EpisodeController extends Controller
{
    // ✅ GET all episodes with pagination
    public function index(ListEpisodesAction $action, Request $request)
{
    try {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);

        // Fetch episodes with related podcast (id + title only)
        $episodes = Episode::with('podcast:id,title')
            ->select('id', 'podcast_id', 'title', 'description', 'views_count', 'audio_url', 'cover_image', 'created_at')
            ->paginate($limit, ['*'], 'page', $page);

        // Transform data to match the expected structure
        $data = $episodes->getCollection()->map(function ($episode) {
            return [
                'id' => $episode->id,
                'title' => $episode->title,
                'description' => $episode->description,
                'views_count' => $episode->views_count ?? 0,
                'image' => $episode->cover_image ? asset('storage/' . $episode->cover_image) : null,
                'audio_url' => $episode->audio_url,
                'published_at' => $episode->created_at,
                'podcast' => [
                    'id' => $episode->podcast->id ?? null,
                    'name' => $episode->podcast->title ?? null,
                ],
            ];
        });

        // Build the response
        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $episodes->currentPage(),
                'per_page' => $episodes->perPage(),
                'total_items' => $episodes->total(),
                'last_page' => $episodes->lastPage(),
            ],
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

    // ✅ GET single episode by ID
   public function show($id)
{
    try {
        // Load episode with related podcast
        $episode = Episode::with('podcast:id,title')->findOrFail($id);

        // Increment the correct column
        $episode->increment('views_count');

        // Build structured response
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $episode->id,
                'title' => $episode->title,
                'short_description' => $episode->short_description ?? Str::limit($episode->description, 120),
                'description' => $episode->description,
                'views' => $episode->views_count ?? 0,
                'image' => $episode->cover_image ? asset('storage/' . $episode->cover_image) : null,
                'audio_url' => $episode->audio_filename
                    ? asset('storage/audios/' . $episode->audio_filename)
                    : ($episode->audio_url ?? null),
                'video_url' => $episode->video_filename
                    ? asset('storage/videos/' . $episode->video_filename)
                    : ($episode->video_url ?? null),
                'published_at' => $episode->created_at,
                'podcast' => [
                    'id' => $episode->podcast->id ?? null,
                    'name' => $episode->podcast->title ?? null,
                ],
            ],
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Episode not found',
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

    // ✅ POST create a new episode
    public function store(Request $request, CreateEpisodeAction $action)
    {
        try {
            $data = $request->validate([
                'podcast_id' => 'required|exists:podcasts,id',
                'season_id' => 'nullable|exists:seasons,id',
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:episodes,slug',
                'description' => 'nullable|string',
                'duration' => 'nullable|integer',
                'published_at' => 'nullable|date',
                'video' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:51200',
                'audio' => 'nullable|file|mimes:mp3,wav,aac|max:51200',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $data['slug'] = $data['slug'] ?? Str::slug($data['title']) . '_' . uniqid();

            // ✅ Handle file uploads
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/videos'), $fileName);
                $data['video_filename'] = $fileName;
                $data['video_url'] = url('storage/videos/' . $fileName);
            }

            if ($request->hasFile('audio')) {
                $file = $request->file('audio');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/audios'), $fileName);
                $data['audio_filename'] = $fileName;
                $data['audio_url'] = url('storage/audios/' . $fileName);
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/covers'), $fileName);
                $data['cover_image'] = url('storage/covers/' . $fileName);
            }

            $episode = $action->execute($data);

            return response()->json(['status' => 'success', 'data' => $episode], 201);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ✅ PUT/PATCH update episode
    public function update(Request $request, Episode $episode, UpdateEpisodeAction $action)
    {
        try {
            $data = $request->validate([
                'podcast_id' => 'sometimes|exists:podcasts,id',
                'season_id' => 'nullable|exists:seasons,id',
                'episode_number' => 'nullable|integer',
                'title' => 'sometimes|string|max:255',
                'slug' => 'nullable|string|max:200|unique:episodes,slug,' . $episode->id,
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'duration_seconds' => 'nullable|integer',
                'published_at' => 'nullable|date',
                'explicit' => 'boolean',
                'status' => 'in:draft,published,archived',
                'cover_image' => 'nullable|string|max:500',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id',
                'file' => 'nullable|file|mimes:mp3,mp4,m4a,wav|max:51200',
            ]);

            // ✅ Handle updated file upload
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('episodes', 'public');
                $data['video_url'] = asset('storage/' . $path);
                $data['mime_type'] = $request->file('file')->getMimeType();
                $data['file_size'] = $request->file('file')->getSize();
            }

            $episode = $action->execute($episode, $data);
            return response()->json(['status' => 'success', 'data' => $episode]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ✅ DELETE episode
    public function destroy(Episode $episode, DeleteEpisodeAction $action)
    {
        try {
            $action->execute($episode);
            return response()->json(['status' => 'success', 'message' => 'Episode deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete episode: ' . $e->getMessage()], 500);
        }
    }
}
