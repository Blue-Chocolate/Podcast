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
    public function index(ShowPodcastAction $showAction)
    {
        // if you want all podcasts, you can add a separate ListPodcastsAction
        return response()->json($showAction->execute(0)); // 0 or separate list action
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
