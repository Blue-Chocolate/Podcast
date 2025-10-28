<?php

namespace App\Actions\Doc_Videos;

use App\Repositories\DocVideoRepository;

class ShowDocVideoAction
{
    protected $repo;

    public function __construct(DocVideoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute($id)
    {
        return $this->repo->find($id);
    }
}
