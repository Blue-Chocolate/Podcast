<?php

namespace App\Http\Controllers\Api\PodcastController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Podcast\CreatePodcastAction;
use App\Actions\Podcast\UpdatePodcastAction;
use App\Actions\Podcast\ShowPodcastAction;
use App\Actions\Podcast\DeletePodcastAction;

class PodcastController extends Controller
{
    public function index(ShowPodcastAction $showAction, Request $request)
{
    $limit = $request->query('limit', 10);

    $podcasts = \App\Models\Podcast::with(['episodes' => function ($query) {
        $query->select('id', 'podcast_id', 'title', 'description', 'audio_url', 'created_at');
    }])
        ->paginate($limit);

    // Map URLs for audio and images if needed
    $podcasts->getCollection()->transform(function ($podcast) {
        // Make cover_image full URL
        if ($podcast->cover_image && !str_starts_with($podcast->cover_image, 'http')) {
            $podcast->cover_image = asset('storage/' . ltrim($podcast->cover_image, '/'));
        }

        // Make each episode’s audio URL full
        foreach ($podcast->episodes as $episode) {
            if ($episode->audio_url && !str_starts_with($episode->audio_url, 'http')) {
                $episode->audio_url = asset('storage/' . ltrim($episode->audio_url, '/'));
            }
        }

        return $podcast;
    });

    return response()->json($podcasts);
}

    public function show(int $id, ShowPodcastAction $showAction)
    {
        return response()->json($showAction->execute($id));
    }

    public function store(Request $request, CreatePodcastAction $createAction)
    {
        $data = $request->validate([
            'slug' => 'required|string|unique:podcasts,slug',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'website_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|url|max:500',
            'rss_url' => 'nullable|url|max:500',
        ]);

        return response()->json($createAction->execute($data), 201);
    }

    public function update(Request $request, int $id, UpdatePodcastAction $updateAction)
    {
        $data = $request->validate([
            'slug' => 'sometimes|string|unique:podcasts,slug,' . $id,
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'website_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|url|max:500',
            'rss_url' => 'nullable|url|max:500',
        ]);

        return response()->json($updateAction->execute($id, $data));
    }

    public function destroy(int $id, DeletePodcastAction $deleteAction)
    {
        $deleteAction->execute($id);
        return response()->json(['message' => 'Podcast deleted successfully']);
    }
}
