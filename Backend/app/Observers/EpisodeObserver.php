<?php

namespace App\Observers;

use App\Models\Episode;
use App\Events\EpisodePublished;

class EpisodeObserver
{
    public function created(Episode $episode)
    {
        if ($episode->status === 'published') {
            event(new EpisodePublished($episode));
        }
    }

    public function updated(Episode $episode)
    {
        // Check if status changed to published
        if ($episode->status === 'published' && $episode->wasChanged('status')) {
            event(new EpisodePublished($episode));
        }

        // Or if any media field was updated on a published episode
        if ($episode->status === 'published' && 
            ($episode->wasChanged('video_url') || $episode->wasChanged('audio_url'))) {
            event(new EpisodePublished($episode));
        }
    }
}