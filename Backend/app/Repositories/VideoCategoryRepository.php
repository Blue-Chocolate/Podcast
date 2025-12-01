<?php

namespace App\Repositories;

use App\Models\VideoCategory;
use App\Models\Video;

class VideoCategoryRepository
{
    /**
     * Get all categories only (no videos) with pagination
     */
    public function getAllCategories($perPage = 10, $page = 1)
    {
        return VideoCategory::select('id', 'name', 'description', 'image_path')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get a single category with paginated videos
     */
    public function getCategoryWithVideos($categoryId, $limit = 10, $page = 1)
    {
        $category = VideoCategory::find($categoryId);

        if (!$category) {
            return null;
        }

        $videos = Video::where('video_category_id', $categoryId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $category->setRelation('videos', $videos);

        return $category;
    }

    /**
     * Get all categories with videos (if needed elsewhere)
     */
    public function getAllCategoriesWithVideos($perPage = 10)
    {
        return VideoCategory::with(['videos' => function ($query) {
            $query->select('id', 'title', 'description', 'video_category_id', 'views_count');
        }])
        ->paginate($perPage, ['id', 'name', 'description', 'image_path', 'slug', 'views_count']);
    }
}
