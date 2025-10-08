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
        return Episode::with(['podcast', 'season', 'files', 'hosts', 'guests', 'categories', 'tags', 'sponsors'])
            ->latest()
            ->paginate(15);
    }

    public function find($id)
    {
        $episode = Episode::with(['podcast', 'season', 'files', 'hosts', 'guests', 'categories', 'tags', 'sponsors'])
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
                $categories = $data['categories'] ?? [];
                unset($data['categories']);

                $episode = Episode::create($data);

                if (!empty($categories)) {
                    $episode->categories()->sync($categories);
                }

                return $episode->load('categories');
            });
        } catch (Exception $e) {
            throw new Exception('Failed to create episode: ' . $e->getMessage());
        }
    }

    public function update(Episode $episode, array $data)
    {
        try {
            return DB::transaction(function () use ($episode, $data) {
                $categories = $data['categories'] ?? null;
                unset($data['categories']);

                $episode->update($data);

                if (is_array($categories)) {
                    $episode->categories()->sync($categories);
                }

                return $episode->load('categories');
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
