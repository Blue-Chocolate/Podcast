<?php

namespace App\Listeners;

use App\Events\EpisodePublished;
use Illuminate\Support\Facades\Cache;

class UpdatePodcastRss
{
    public function handle(EpisodePublished $event)
    {
        $episode = $event->episode;
        $podcast = $episode->podcast;

        // Clear RSS cache for this podcast
        Cache::forget("podcast_rss_{$podcast->slug}");

        // Optionally: Ping Apple Podcasts or other services
        // $this->notifyApplePodcasts($podcast);
    }

    private function notifyApplePodcasts($podcast)
    {
        // Apple Podcasts doesn't have a direct ping endpoint
        // But you can implement webhook notifications if needed
    }
}