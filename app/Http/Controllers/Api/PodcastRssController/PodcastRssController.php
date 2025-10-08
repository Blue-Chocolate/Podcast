<?php

namespace App\Http\Controllers\Api\PodcastController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PodcastRssController extends Controller
{
    public function show($slug)
    {
        try {
            // Fetch podcast by slug with published episodes
            $podcast = Podcast::where('slug', $slug)
                ->with(['episodes' => function ($query) {
                    $query->where('status', 'published')
                          ->orderBy('published_at', 'desc');
                }])
                ->firstOrFail();

            // Render XML view
            $rssContent = view('rss.podcast', compact('podcast'))->render();

            // Return as XML response
            return response($rssContent, 200)
                ->header('Content-Type', 'application/xml');

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Podcast not found.'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate RSS: ' . $e->getMessage()
            ], 500);
        }
    }
}
