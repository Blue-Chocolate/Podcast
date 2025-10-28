<?php

namespace App\Http\Controllers\Api\PodcastController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Podcast\{
    CreatePodcastAction,
    UpdatePodcastAction,
    ShowPodcastAction,
    DeletePodcastAction
};
use App\Models\Podcast;

class PodcastController extends Controller
{
    // ✅ List all podcasts with pagination
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $limit;

        $podcasts = Podcast::offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = Podcast::count();

        return response()->json([
            'data' => $podcasts,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    }

    // ✅ Show a specific podcast with its episodes
   public function show($id, Request $request, ShowPodcastAction $showAction)
{
    $limit = (int) $request->query('limit', 10);
    $page = (int) $request->query('page', 1);
    $offset = ($page - 1) * $limit;

    $podcast = $showAction->execute($id);

    // Get paginated episodes
    $episodesQuery = $podcast->episodes()
        ->select('id', 'podcast_id', 'title', 'description', 'audio_url', 'video_url', 'created_at')
        ->orderBy('created_at', 'desc');

    $total = $episodesQuery->count();
    $episodes = $episodesQuery->offset($offset)->limit($limit)->get();

    // Fix URLs
    foreach ($episodes as $episode) {
        if ($episode->audio_url && !str_starts_with($episode->audio_url, 'http')) {
            $episode->audio_url = asset('storage/' . ltrim($episode->audio_url, '/'));
        }

        if ($episode->video_url && !str_starts_with($episode->video_url, 'http')) {
            $episode->video_url = asset('storage/' . ltrim($episode->video_url, '/'));
        }
    }

    return response()->json([
        'podcast' => $podcast,
        'episodes' => $episodes,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => ceil($total / $limit),
        ],
    ]);
}

    // ✅ List all episodes for a specific podcast
    public function episodes($id)
    {
        $podcast = Podcast::findOrFail($id);

        $episodes = $podcast->episodes()
            ->select('id', 'podcast_id', 'title', 'description', 'audio_url', 'video_url', 'created_at')
            ->latest()
            ->get();

        foreach ($episodes as $episode) {
            if ($episode->audio_url && !str_starts_with($episode->audio_url, 'http')) {
                $episode->audio_url = asset('storage/' . ltrim($episode->audio_url, '/'));
            }

            if ($episode->video_url && !str_starts_with($episode->video_url, 'http')) {
                $episode->video_url = asset('storage/' . ltrim($episode->video_url, '/'));
            }
        }

        return response()->json([
            'podcast_id' => $podcast->id,
            'podcast_title' => $podcast->title,
            'episodes' => $episodes,
        ]);
    }

    // ✅ Create a new podcast
    public function store(Request $request, CreatePodcastAction $createAction)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'author' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('podcasts', 'public');
        }

        $podcast = $createAction->execute($data);

        return response()->json([
            'message' => 'Podcast created successfully',
            'data' => $podcast,
        ], 201);
    }

    // ✅ Update existing podcast
    public function update(Request $request, $id, UpdatePodcastAction $updateAction)
    {
        $podcast = Podcast::findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'author' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('podcasts', 'public');
        }

        $podcast = $updateAction->execute($podcast, $data);

        return response()->json([
            'message' => 'Podcast updated successfully',
            'data' => $podcast,
        ]);
    }

    // ✅ Delete a podcast
    public function destroy($id, DeletePodcastAction $deleteAction)
    {
        $podcast = Podcast::findOrFail($id);
        $deleteAction->execute($podcast);

        return response()->json(['message' => 'Podcast deleted successfully']);
    }
}
