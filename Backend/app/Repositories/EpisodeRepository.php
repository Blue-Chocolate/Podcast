<?php

namespace App\Repositories;

use App\Models\Episode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Exception;

class EpisodeRepository
{
    public function getAll()
    {
        // ✅ Remove all undefined relationships
        return Episode::with(['podcast', 'season'])
            ->latest()
            ->paginate(15);
    }

    public function find($id)
    {
        // ✅ Only load existing relations
        $episode = Episode::with(['podcast', 'season'])
            ->find($id);

        if (!$episode) {
            throw new ModelNotFoundException("Episode not found");
        }

        return $episode;
    }

    public function store(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                // Remove categories handling since relationship not defined
                unset($data['categories']);

                $episode = Episode::create($data);

                return $episode->load(['podcast', 'season']);
            });
        } catch (Exception $e) {
            throw new Exception('Failed to create episode: ' . $e->getMessage());
        }
    }

    public function update(Episode $episode, array $data)
    {
        try {
            return DB::transaction(function () use ($episode, $data) {
                unset($data['categories']);

                $episode->update($data);

                return $episode->load(['podcast', 'season']);
            });
        } catch (Exception $e) {
            throw new Exception('Failed to update episode: ' . $e->getMessage());
        }
    }

    public function delete(Episode $episode)
    {
        try {
            return $episode->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete episode: ' . $e->getMessage());
        }
    }
}
