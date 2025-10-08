<?php

namespace App\Http\Controllers\Api\FeedController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;

class FeedController extends Controller
{
    public function showRssFeed($slug)
    {
        $podcast = Podcast::with('episodes')->where('slug', $slug)->firstOrFail();

        // Return the RSS view as XML
        return response()
            ->view('rss', compact('podcast'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
