<?php

namespace App\Actions\Doc_Videos;

use App\Repositories\DocVideoRepository;

class CreateDocVideoAction
{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        return $this->repo->create($data);
    }
}
