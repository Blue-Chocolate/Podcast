<?php

namespace App\Events;

use App\Models\Episode;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EpisodePublished
{
    use Dispatchable, SerializesModels;

    public $episode;

    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
    }
}