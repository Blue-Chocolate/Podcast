<?php

namespace App\Http\Controllers\Api\PlaylistController;

use App\Http\Controllers\Controller;
use App\Repositories\PlaylistRepository;
use App\Actions\Playlists\{
    CreatePlaylistAction,
    UpdatePlaylistAction,
    DeletePlaylistAction,
    AttachEpisodesToPlaylistAction
};
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    protected $repo;

    public function __construct(PlaylistRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        return response()->json($this->repo->all());
    }

    public function show($id)
    {
        return response()->json($this->repo->find($id));
    }

    public function store(Request $request, CreatePlaylistAction $action)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:playlists,slug',
            'description' => 'nullable|string',
            'episode_ids' => 'array'
        ]);

        $playlist = $action->execute($validated, auth()->id());
        return response()->json($playlist, 201);
    }

    public function update(Request $request, $id, UpdatePlaylistAction $action)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:playlists,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        $playlist = $action->execute($id, $validated);
        return response()->json($playlist);
    }

    public function destroy($id, DeletePlaylistAction $action)
    {
        $action->execute($id);
        return response()->json(['message' => 'Playlist deleted']);
    }

    public function attachEpisodes(Request $request, $id, AttachEpisodesToPlaylistAction $action)
    {
        $validated = $request->validate([
            'episode_ids' => 'required|array',
            'episode_ids.*' => 'exists:episodes,id',
        ]);

        $playlist = $action->execute($id, $validated['episode_ids']);
        return response()->json($playlist);
    }
}
