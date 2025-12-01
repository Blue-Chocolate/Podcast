<?php

namespace App\Repositories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SeasonRepository
{
    /**
     * Get all seasons with optional related podcast and episodes
     */
    public function all(): Collection
    {
        return Season::with(['podcast', 'episodes'])->orderBy('number')->get();
    }

    /**
     * Find a season by ID with its episodes
     */
    public function find(int $id): Season
    {
        $season = Season::with('episodes')->find($id);

        if (! $season) {
            throw new ModelNotFoundException("Season not found");
        }

        return $season;
    }

    /**
     * Create a new season
     */
    public function create(array $data): Season
    {
        return Season::create($data);
    }

    /**
     * Update a season
     */
    public function update(int $id, array $data): Season
    {
        $season = $this->find($id);
        $season->update($data);

        return $season;
    }

    /**
     * Delete a season and cascade delete its episodes
     */
    public function delete(int $id): bool
    {
        $season = $this->find($id);
        return $season->delete();
    }
}
