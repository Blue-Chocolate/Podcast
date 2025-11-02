<?php

namespace App\Repositories;

use App\Models\VideoCategory;
use App\Models\Video;

class VideoCategoryRepository
{
    public function getAllCategoriesWithVideos($perPage = 10)
    {
        return VideoCategory::with(['videos' => function ($query) {
            $query->select('id', 'title', 'description', 'video_category_id', 'views_count');
        }])
        ->paginate($perPage, ['id', 'name', 'description', 'image_path', 'slug', 'views_count']);
    }

    public function getCategoryWithVideos($categoryId, $perPage = 10)
    {
        return VideoCategory::where('id', $categoryId)
            ->with(['videos' => function ($query) use ($perPage) {
                $query->select('id', 'title', 'description', 'video_category_id', 'views_count')
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