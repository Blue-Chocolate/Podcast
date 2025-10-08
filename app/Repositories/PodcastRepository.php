<?php

namespace App\Repositories;

use App\Models\Podcast;

class PodcastRepository
{
    public function all()
    {
        return Podcast::all();
    }

    public function find($id)
    {
        return Podcast::findOrFail($id);
    }

    public function create(array $data)
    {
        return Podcast::create($data);
    }

    public function update($id, array $data)
    {
        $podcast = $this->find($id);
        $podcast->update($data);
        return $podcast;
    }

    public function delete($id)
    {
        $podcast = $this->find($id);
        return $podcast->delete();
    }
}
