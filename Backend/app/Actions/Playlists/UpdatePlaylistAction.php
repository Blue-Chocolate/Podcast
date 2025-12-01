<?php

namespace App\Actions\Playlists;

use App\Repositories\PlaylistRepository;

class UpdatePlaylistAction
{
    protected $repo;

    public function __construct(PlaylistRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($id, array $data)
    {
        return $this->repo->update($id, $data);
    }
}
