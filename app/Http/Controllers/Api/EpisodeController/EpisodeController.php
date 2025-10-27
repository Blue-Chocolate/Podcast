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
    public function index(ListEpisodesAction $action)
    {
        try {
            $episodes = $action->execute();
            return response()->json(['status' => 'success', 'data' => $episodes]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id, ShowEpisodeAction $action)
{
    try {
        $episode = $action->execute($id);

        // ✅ Increment the view count safely
        $episode->increment('views_count');
        $episode->video_url = url('/videos/' . $episode->video_filename);


        return response()->json([
            'status' => 'success',
            'data' => $episode->fresh(), // refresh to show updated count
        ]);

    } catch (ModelNotFoundException $e) {
        return response()->json(['status' => 'error', 'message' => 'Episode not found'], 404);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
    public function store(Request $request, CreateEpisodeAction $action)
    {
        try {
            $data = $request->validate([
                'podcast_id' => 'required|exists:podcasts,id',
                'season_id' => 'nullable|exists:seasons,id',
                'episode_number' => 'nullable|integer',
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:200|unique:episodes,slug',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'duration_seconds' => 'nullable|integer',
                'published_at' => 'nullable|date',
                'explicit' => 'boolean',
                'status' => 'in:draft,published,archived',
                'cover_image' => 'nullable|string|max:500',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id',
                'file' => 'nullable|file|mimes:mp3,mp4,m4a,wav|max:51200', // up to 50 MB
            ]);

            $data['slug'] = $data['slug'] ?? Str::slug($data['title']) . '-' . uniqid();

            // ✅ Handle file upload (video/audio)
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('episodes', 'public');
                $data['video_url'] = asset('storage/' . $path);
                $data['mime_type'] = $request->file('file')->getMimeType();
                $data['file_size'] = $request->file('file')->getSize();
            }

            $episode = $action->execute($data);

            return response()->json(['status' => 'success', 'data' => $episode], 201);

        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

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

            // ✅ Handle file upload
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
