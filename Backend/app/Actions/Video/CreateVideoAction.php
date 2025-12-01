<?php

namespace App\Actions\Video;

use App\Repositories\VideoRepository;

class CreateVideoAction
{
    protected $repo;

    public function __construct(VideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        return $this->repo->create($data);
    }
}
