<?php

namespace App\Repositories;

use App\Models\Season;

class SeasonRepository
{
    public function all()
    {
        return Season::all();
    }

    public function find(int $id): Season
    {
        return Season::findOrFail($id);
    }

    public function create(array $data): Season
    {
        return Season::create($data);
    }

    public function update(int $id, array $data): Season
    {
        $season = $this->find($id);
        $season->update($data);
        return $season;
    }

    public function delete(int $id): bool
    {
        $season = $this->find($id);
        return $season->delete();
    }
}
