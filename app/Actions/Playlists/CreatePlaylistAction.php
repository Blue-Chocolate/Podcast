<?php

namespace App\Actions\Playlists;

use App\Repositories\PlaylistRepository;

class CreatePlaylistAction
{
    protected $repo;

    public function __construct(PlaylistRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data, $userId)
    {
        $data['created_by'] = $userId;
        return $this->repo->create($data);
    }
}
