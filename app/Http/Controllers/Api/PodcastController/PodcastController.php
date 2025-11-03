<?php

namespace App\Http\Controllers\Api\PodcastController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Podcast\{
    CreatePodcastAction,
    UpdatePodcastAction,
    ShowPodcastAction,
    DeletePodcastAction
};
use App\Models\Podcast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PodcastController extends Controller
{
    // ✅ List all podcasts with pagination
   public function index(Request $request)
{
    try {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        // Validate pagination parameters
        if ($limit < 1 || $limit > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Limit must be between 1 and 100',
            ], 400);
        }

        if ($page < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Page must be greater than 0',
            ], 400);
        }

        $offset = ($page - 1) * $limit;

        // Fetch paginated podcasts
        $podcasts = Podcast::select('id', 'title', 'description', 'cover_image')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($podcast) {
                return [
                    'id' => $podcast->id,
                    'title' => $podcast->title,
                    'description' => $podcast->description,
                    'image' => $podcast->cover_image 
                        ? url('storage/' . $podcast->cover_image)
                        : null,
                ];
            });

        $total = Podcast::count();

        return response()->json([
            'success' => true,
            'data' => $podcasts,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    } catch (Exception $e) {
        Log::error('Error fetching podcasts list', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching podcasts',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}


    // ✅ Show a specific podcast with its episodes
  public function show($id, Request $request, ShowPodcastAction $showAction)
{
    try {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        // Validate pagination parameters
        if ($limit < 1 || $limit > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Limit must be between 1 and 100',
            ], 400);
        }

        if ($page < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Page must be greater than 0',
            ], 400);
        }

        $offset = ($page - 1) * $limit;

        // Fetch the podcast
        $podcast = $showAction->execute($id);

        if (!$podcast) {
            return response()->json([
                'success' => false,
                'message' => 'Podcast not found',
            ], 404);
        }

        // Format podcast image URL
        $podcastImage = $podcast->cover_image && !str_starts_with($podcast->cover_image, 'http')
            ? asset('storage/' . ltrim($podcast->cover_image, '/'))
            : $podcast->cover_image;

        // Fetch paginated episodes
        $episodesQuery = $podcast->episodes()
            ->select('id', 'podcast_id', 'title', 'description', 'views_count', 'cover_image', 'audio_url', 'video_url', 'created_at')
            ->orderBy('created_at', 'desc');

        $total = $episodesQuery->count();
        $episodes = $episodesQuery->offset($offset)->limit($limit)->get();

        // Transform episode data to desired structure
        $formattedEpisodes = $episodes->map(function ($episode) use ($podcast) {
            return [
                'id' => $episode->id,
                'title' => $episode->title,
                'description' => $episode->description,
                'views' => $episode->views_count ?? 0,
                'image' => $episode->cover_image && !str_starts_with($episode->cover_image, 'http')
                    ? asset('storage/' . ltrim($episode->cover_image, '/'))
                    : $episode->cover_image,
                'audio_url' => $episode->audio_url && !str_starts_with($episode->audio_url, 'http')
                    ? asset('storage/' . ltrim($episode->audio_url, '/'))
                    : $episode->audio_url,
                'published_at' => $episode->created_at ? $episode->created_at->timestamp : null,
                'podcast' => [
                    'id' => $podcast->id,
                    'name' => $podcast->title,
                ],
            ];
        });

        // Final response
        return response()->json([
            'success' => true,
            'podcast' => [
                'id' => $podcast->id,
                'title' => $podcast->title,
                'description' => $podcast->description,
                'image' => $podcastImage,
            ],
            'episodes' => $formattedEpisodes,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => $total,
                'last_page' => ceil($total / $limit),
            ],
        ]);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Podcast not found',
        ], 404);
    } catch (Exception $e) {
        Log::error('Error fetching podcast details', [
            'podcast_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching podcast details',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

    // ✅ List all episodes for a specific podcast
    public function episodes($id)
    {
        try {
            $podcast = Podcast::findOrFail($id);

            $episodes = $podcast->episodes()
                ->select('id', 'podcast_id', 'title', 'description', 'audio_url', 'video_url', 'created_at')
                ->latest()
                ->get()
                ->map(function ($episode) {
                    if ($episode->audio_url && !str_starts_with($episode->audio_url, 'http')) {
                        $episode->audio_url = asset('storage/' . ltrim($episode->audio_url, '/'));
                    }
                    if ($episode->video_url && !str_starts_with($episode->video_url, 'http')) {
                        $episode->video_url = asset('storage/' . ltrim($episode->video_url, '/'));
                    }
                    return $episode;
                });

            return response()->json([
                'success' => true,
                'podcast_id' => $podcast->id,
                'podcast_name' => $podcast->name,
                'podcast_title' => $podcast->title,
                'episodes' => $episodes
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Podcast not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Error fetching podcast episodes', [
                'podcast_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching episodes',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ✅ Create a new podcast
    public function store(Request $request, CreatePodcastAction $createAction)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'author' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('cover_image')) {
                try {
                    $data['cover_image'] = $request->file('cover_image')->store('podcasts', 'public');
                } catch (Exception $e) {
                    Log::error('Error uploading cover image', [
                        'error' => $e->getMessage()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload cover image',
                        'error' => config('app.debug') ? $e->getMessage() : null
                    ], 500);
                }
            }

            $podcast = $createAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Podcast created successfully',
                'data' => $podcast,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error creating podcast', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the podcast',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ✅ Update existing podcast
    public function update(Request $request, $id, UpdatePodcastAction $updateAction)
    {
        try {
            $podcast = Podcast::findOrFail($id);

            $data = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'author' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('cover_image')) {
                try {
                    // Delete old cover image if exists
                    if ($podcast->cover_image && Storage::disk('public')->exists($podcast->cover_image)) {
                        Storage::disk('public')->delete($podcast->cover_image);
                    }
                    
                    $data['cover_image'] = $request->file('cover_image')->store('podcasts', 'public');
                } catch (Exception $e) {
                    Log::error('Error uploading new cover image', [
                        'podcast_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload new cover image',
                        'error' => config('app.debug') ? $e->getMessage() : null
                    ], 500);
                }
            }

            $podcast = $updateAction->execute($podcast, $data);

            return response()->json([
                'success' => true,
                'message' => 'Podcast updated successfully',
                'data' => $podcast,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Podcast not found',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error updating podcast', [
                'podcast_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the podcast',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ✅ Delete a podcast
    public function destroy($id, DeletePodcastAction $deleteAction)
    {
        try {
            $podcast = Podcast::findOrFail($id);
            
            // Delete cover image if exists
            if ($podcast->cover_image && Storage::disk('public')->exists($podcast->cover_image)) {
                try {
                    Storage::disk('public')->delete($podcast->cover_image);
                } catch (Exception $e) {
                    Log::warning('Failed to delete cover image during podcast deletion', [
                        'podcast_id' => $id,
                        'cover_image' => $podcast->cover_image,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with podcast deletion even if image deletion fails
                }
            }
            
            $deleteAction->execute($podcast);

            return response()->json([
                'success' => true,
                'message' => 'Podcast deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Podcast not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Error deleting podcast', [
                'podcast_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the podcast',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}