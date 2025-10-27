<?php

namespace App\Http\Controllers\Api\PodcastRssController;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PodcastRssController extends Controller
{
    public function show($slug)
    {
        try {
            // Cache RSS for 1 hour (or until manually cleared)
            $rssContent = Cache::remember("podcast_rss_{$slug}", 3600, function () use ($slug) {
                return $this->generateRss($slug);
            });

            return response($rssContent, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8')
                ->header('Cache-Control', 'public, max-age=3600');

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Podcast not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate RSS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateRss($slug)
{
    $podcast = Podcast::where('slug', $slug)
        ->with(['episodes' => function ($query) {
            $query->where('status', 'published')
                ->orderBy('published_at', 'desc');
        }])
        ->firstOrFail();

    try {
        if ($podcast->rss_url) {
            $response = Http::timeout(10)->get($podcast->rss_url);
            if ($response->successful()) {
                $feed = simplexml_load_string($response->body());
                $channel = $feed->channel;
                $podcast->title = (string)$channel->title;
                $podcast->description = (string)$channel->description;
                $podcast->base_url = url('/');

                $episodes = [];
                foreach ($channel->item as $item) {
                    $audioUrl = (string)$item->enclosure['url'] ?? '';
                    if (!Str::startsWith($audioUrl, 'http')) {
                        $audioUrl = $podcast->base_url . '/' . ltrim($audioUrl, '/');
                    }

                    $episodes[] = [
                        'title' => (string)$item->title,
                        'description' => (string)$item->description,
                        'audio_url' => $audioUrl,
                        'file_size' => (int)($item->enclosure['length'] ?? 0),
                        'mime_type' => (string)($item->enclosure['type'] ?? 'audio/mpeg'),
                        'published_at' => Carbon::parse((string)$item->pubDate),
                        'duration' => $this->parseDuration((string)($item->children('itunes', true)->duration ?? '0')),
                    ];
                }
                $podcast->episodes = $episodes;
            }
        }

return response(view('rss.podcast', compact('podcast'))->render(), 200)
    ->header('Content-Type', 'application/rss+xml');        
    } catch (Exception $e) {
        Log::warning('Failed to fetch external RSS for ' . $slug . ': ' . $e->getMessage());
    }
}
    private function parseDuration($duration)
    {
        if (is_numeric($duration)) {
            return (int)$duration;
        }
        
        $parts = array_reverse(explode(':', $duration));
        $seconds = 0;
        $seconds += (int)($parts[0] ?? 0); // seconds
        $seconds += ((int)($parts[1] ?? 0)) * 60; // minutes
        $seconds += ((int)($parts[2] ?? 0)) * 3600; // hours
        
        return $seconds;
    }
}