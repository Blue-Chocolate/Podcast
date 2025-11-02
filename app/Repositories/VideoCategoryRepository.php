<?php

namespace App\Repositories;

use App\Models\VideoCategory;
use App\Models\Video;

class VideoCategoryRepository
{
    public function getAllCategoriesWithVideos($perPage = 10)
    {
        return VideoCategory::with(['videos' => function ($query) {
            $query->where('is_active', true)
                  ->select('id', 'title', 'description', 'file_path', 'video_category_id', 'views_count');
        }])
        ->where('is_active', true)
        ->paginate($perPage, ['id', 'name', 'description', 'image_path', 'slug', 'views_count']);
    }

    public function getCategoryWithVideos($categoryId, $perPage = 10)
    {
        return VideoCategory::where('is_active', true)
            ->where('id', $categoryId)
            ->with(['videos' => function ($query) use ($perPage) {
                $query->where('is_active', true)
                      ->select('id', 'title', 'description', 'file_path', 'video_category_id', 'views_count')
                      ->paginate($perPage);
            }])
            ->firstOrFail();
    }

    public function getVideoInCategory($categoryId, $videoId)
    {
        return Video::where('video_category_id', $categoryId)
            ->where('id', $videoId)
            ->first();
    }
}
