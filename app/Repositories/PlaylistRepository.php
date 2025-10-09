<?php

namespace App\Repositories;

use App\Models\Playlist;

class PlaylistRepository
{
    public function all()
    {
        return Playlist::with(['creator', 'episodes'])->latest()->get();
    }

    public function find($id)
    {
        return Playlist::with(['creator', 'episodes'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Playlist::create($data);
    }

    public function update($id, array $data)
    {
        $playlist = Playlist::findOrFail($id);
        $playlist->update($data);
        return $playlist;
    }

    public function delete($id)
    {
        $playlist = Playlist::findOrFail($id);
        return $playlist->delete();
    }

    public function attachEpisodes($playlistId, array $episodeIds)
    {
        $playlist = Playlist::findOrFail($playlistId);
        $playlist->episodes()->sync($episodeIds);
        return $playlist->load('episodes');
    }
}
