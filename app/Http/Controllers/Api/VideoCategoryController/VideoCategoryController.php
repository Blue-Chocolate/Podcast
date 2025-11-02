<?php

namespace App\Http\Controllers\Api\VideoCategoryController;

use App\Http\Controllers\Controller;
use App\Repositories\VideoCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class VideoCategoryController extends Controller
{
    protected $repo;

    public function __construct(VideoCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);
            $categories = $this->repo->getAllCategoriesWithVideos($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Video categories retrieved successfully',
                'data' => $categories
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve video categories'
            ], 500);
        }
    }

    public function show(Request $request, $category_id)
    {
        try {
            $perPage = $request->get('limit', 10);
            $category = $this->repo->getCategoryWithVideos($category_id, $perPage);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Category with videos retrieved successfully',
                'data' => $category
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching category: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve category'
            ], 500);
        }
    }

    public function showVideo($category_id, $video_id)
    {
        try {
            $video = $this->repo->getVideoInCategory($category_id, $video_id);

            if (!$video) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Video not found in this category'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Video retrieved successfully',
                'data' => $video
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching video: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve video'
            ], 500);
        }
    }
}