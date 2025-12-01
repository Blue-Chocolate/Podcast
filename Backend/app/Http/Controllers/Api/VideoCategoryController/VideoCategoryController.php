<?php

namespace App\Http\Controllers\Api\VideoCategoryController;

use App\Http\Controllers\Controller;
use App\Repositories\VideoCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class VideoCategoryController extends Controller
{
    protected $repo;

    public function __construct(VideoCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get all video categories with pagination
     */
    public function index(Request $request)
{
    try {
        // Validate pagination inputs
        $perPage = (int) $request->get('limit', 10);
        $page = (int) $request->get('page', 1);

        if ($perPage < 1 || $perPage > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Limit must be a number between 1 and 100',
            ], 400);
        }

        if ($page < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Page must be a positive number.',
            ], 400);
        }

        // Fetch paginated categories only (no videos)
        $categories = $this->repo->getAllCategories($perPage, $page);

        if (!$categories || $categories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No categories found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total_items' => $categories->total(),
                'last_page' => $categories->lastPage(),
            ],
        ], 200);

    } catch (Exception $e) {
        Log::error('Error fetching categories', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => [
                'limit' => $request->get('limit'),
                'page' => $request->get('page'),
            ]
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving categories',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}


    /**
     * Get a specific category with its videos
     */
    public function show(Request $request, $category_id)
    {
        try {
            // Validate category ID
            if (!is_numeric($category_id) || $category_id < 1) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid category ID. Must be a positive number.',
                ], 400);
            }

            // Get and validate pagination
            $perPage = $request->get('limit', 10);

            if (!is_numeric($perPage) || $perPage < 1 || $perPage > 100) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Limit must be a number between 1 and 100',
                ], 400);
            }

            $perPage = (int) $perPage;

            // Fetch category with videos
            $category = $this->repo->getCategoryWithVideos($category_id, $perPage);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Category with videos retrieved successfully',
                'data' => $category
            ], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Category not found', [
                'category_id' => $category_id,
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);

        } catch (Exception $e) {
            Log::error('Error fetching category with videos', [
                'category_id' => $category_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => [
                    'limit' => $request->get('limit'),
                ]
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred while retrieving the category',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get a specific video within a category
     */
    public function showVideo($category_id, $video_id)
    {
        try {
            // Validate category ID
            if (!is_numeric($category_id) || $category_id < 1) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid category ID. Must be a positive number.',
                ], 400);
            }

            // Validate video ID
            if (!is_numeric($video_id) || $video_id < 1) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid video ID. Must be a positive number.',
                ], 400);
            }

            // Fetch video from category
            $video = $this->repo->getVideoInCategory($category_id, $video_id);

            if (!$video) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Video not found in this category',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Video retrieved successfully',
                'data' => $video
            ], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Video not found in category', [
                'category_id' => $category_id,
                'video_id' => $video_id,
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Video not found in this category',
            ], 404);

        } catch (Exception $e) {
            Log::error('Error fetching video from category', [
                'category_id' => $category_id,
                'video_id' => $video_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred while retrieving the video',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle repository connection errors
     */
    private function handleRepositoryError(Exception $e, string $context, array $data = [])
    {
        Log::error("Repository error in {$context}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'context_data' => $data
        ]);

        return response()->json([
            'success' => false,
            'status' => 'error',
            'message' => 'A database error occurred. Please try again later.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}