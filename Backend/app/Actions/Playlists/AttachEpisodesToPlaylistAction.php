<?php

namespace App\Actions\Playlists;

use App\Repositories\PlaylistRepository;

class AttachEpisodesToPlaylistAction
{
    protected $repo;

    public function __construct(PlaylistRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($playlistId, array $episodeIds)
    {
        return $this->repo->attachEpisodes($playlistId, $episodeIds);
    }
}
