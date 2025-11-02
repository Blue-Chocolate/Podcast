<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class VideoRepository
{
    /**
     * Get all videos with optional pagination, including category info
     *
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($limit = 10, $offset = 0)
    {
        return Video::with('category:id,name')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Count total videos
     *
     * @return int
     */
    public function count()
    {
        return Video::count();
    }

    /**
     * Find a video by ID with category info
     *
     * @param int $id
     * @return Video
     *
     * @throws ModelNotFoundException
     */
    public function find($id)
    {
        $video = Video::with('category:id,name')->find($id);

        if (!$video) {
            throw new ModelNotFoundException('Video not found.');
        }

        return $video;
    }

    /**
     * Create a new video
     *
     * @param array $data
     * @return Video
     *
     * @throws Exception
     */
    public function create(array $data)
    {
        try {
            return Video::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create video: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing video
     *
     * @param Video $video
     * @param array $data
     * @return Video
     *
     * @throws Exception
     */
    public function update(Video $video, array $data)
    {
        try {
            $video->update($data);
            // Reload category info after update
            $video->load('category:id,name');
            return $video;
        } catch (Exception $e) {
            throw new Exception('Failed to update video: ' . $e->getMessage());
        }
    }

    /**
     * Delete a video
     *
     * @param Video $video
     * @return bool|null
     *
     * @throws Exception
     */
    public function delete(Video $video)
    {
        try {
            return $video->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete video: ' . $e->getMessage());
        }
    }
}
