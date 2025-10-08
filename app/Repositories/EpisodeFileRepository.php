<?php

namespace App\Repositories;

use App\Models\EpisodeFile;

class EpisodeFileRepository
{
    public function create(array $data)
    {
        return EpisodeFile::create($data);
    }

    public function update(EpisodeFile $episodeFile, array $data)
    {
        $episodeFile->update($data);
        return $episodeFile;
    }

    public function delete(EpisodeFile $episodeFile)
    {
        return $episodeFile->delete();
    }

    public function find($id)
    {
        return EpisodeFile::findOrFail($id);
    }
    public function getByEpisode($episodeId)
    {
        return EpisodeFile::where('episode_id', $episodeId)->get();
    }

   
    public function findById($id)
    {
        return EpisodeFile::findOrFail($id);
    }
}
