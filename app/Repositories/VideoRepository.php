<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class VideoRepository
{
    public function all($limit = 10, $offset = 0)
    {
        return Video::offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function count()
    {
        return Video::count();
    }

    public function find($id)
    {
        $video = Video::find($id);
        if (!$video) {
            throw new ModelNotFoundException('Video not found.');
        }
        return $video;
    }

    public function create(array $data)
    {
        try {
            return Video::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create doc video: ' . $e->getMessage());
        }
    }

    public function update(Video $video, array $data)
    {
        try {
            $video->update($data);
            return $video;
        } catch (Exception $e) {
            throw new Exception('Failed to update doc video: ' . $e->getMessage());
        }
    }

    public function delete(Video $video)
    {
        try {
            return $video->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete doc video: ' . $e->getMessage());
        }
    }
}
