<?php

namespace App\Http\Controllers\Api\PodcastRssController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Http;

class PodcastRssController extends Controller
{
    public function show($slug)
    {
        try {
            // Fetch podcast with published episodes
            $podcast = Podcast::where('slug', $slug)
                ->with(['episodes' => function ($query) {
                    $query->where('status', 'published')
                          ->orderBy('published_at', 'desc');
                }])
                ->firstOrFail();

            // Optional: Merge external RSS if rss_url exists
            if ($podcast->rss_url) {
                try {
                    $response = Http::get($podcast->rss_url);
                    $externalXml = simplexml_load_string($response->body());

                    foreach ($externalXml->channel->item as $item) {
                        $podcast->episodes->push((object)[
                            'title' => (string)$item->title,
                            'description' => (string)$item->description,
                            'audio_url' => (string)$item->enclosure['url'],
                            'video_url' => null,
                            'file_size' => (int)($item->enclosure['length'] ?? 0),
                            'mime_type' => (string)($item->enclosure['type'] ?? 'audio/mpeg'),
                            'published_at' => \Carbon\Carbon::parse((string)$item->pubDate),
                            'slug' => uniqid('external-'),
                        ]);
                    }
                } catch (\Exception $e) {
                    // Fail silently if external RSS is unreachable
                }
            }

            // Render RSS XML view
            $rssContent = view('rss.podcast', compact('podcast'))->render();

            // Return as XML
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
