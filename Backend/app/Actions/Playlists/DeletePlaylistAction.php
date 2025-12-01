<?php

namespace App\Actions\Playlists;

use App\Repositories\PlaylistRepository;

class DeletePlaylistAction
{
    protected $repo;

    public function __construct(PlaylistRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($id)
    {
        return $this->repo->delete($id);
    }
}
