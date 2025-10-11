<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Controllers
use App\Http\Controllers\Api\PodcastController\PodcastController;
use App\Http\Controllers\Api\EpisodeController\EpisodeController;
use App\Http\Controllers\Api\CategoryController\CategoryController;
use App\Http\Controllers\Api\PodcastRSSController\PodcastRssController;
use App\Http\Controllers\Api\FeedController\FeedController;
use App\Http\Controllers\Api\SeasonController\SeasonController;
use App\Http\Controllers\Api\EpisodeFileController\EpisodeFileController;
use App\Http\Controllers\Api\TranscriptController\TranscriptController;
use App\Http\Controllers\Api\PersonController\PersonController;
use App\Http\Controllers\Api\BlogController\BlogController;
use App\Http\Controllers\Api\PostController\PostController;
use App\Http\Controllers\Api\PlaylistController\PlaylistController;
use App\Http\Controllers\Api\ReleaseController\ReleaseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 🎙️ Podcasts
    Route::apiResource('podcasts', PodcastController::class);

    // 📅 Seasons
    Route::apiResource('seasons', SeasonController::class);

    // 🎧 Episodes
    Route::apiResource('episodes', EpisodeController::class);

    // 📂 Episode Files
    Route::prefix('episode-files')->group(function () {
        Route::post('/', [EpisodeFileController::class, 'store']);
        Route::get('/{id}/edit', [EpisodeFileController::class, 'edit']);
        Route::put('/{id}', [EpisodeFileController::class, 'update']);
        Route::delete('/{id}', [EpisodeFileController::class, 'destroy']);
    });

    // 📜 Transcripts
    Route::apiResource('transcripts', TranscriptController::class);

    // 👥 People
    Route::apiResource('people', PersonController::class);

    // 🏷️ Categories
    Route::apiResource('categories', CategoryController::class);

    // 📰 Blogs
    Route::apiResource('blogs', BlogController::class);

    // ✍️ Posts
    Route::apiResource('posts', PostController::class);

    // 🔊 RSS Feed
    Route::get('/rss/podcasts/{slug}', [PodcastRssController::class, 'show']);
    Route::get('/podcasts/{slug}/feed', [FeedController::class, 'showRssFeed']);

    // 📀 Releases
Route::get('releases', [ReleaseController::class, 'index']);
Route::middleware('auth:sanctum')->get('v1/releases/{id}/download', [ReleaseController::class, 'download']);

    // 🎵 Admin Playlists (Protected)
    Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
        Route::apiResource('playlists', PlaylistController::class);
        Route::post('playlists/{id}/attach-episodes', [PlaylistController::class, 'attachEpisodes']);
    });

    // 🔐 Auth routes
    Route::post('/login', function (Request $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('user-token')->plainTextToken;
        return response()->json(['token' => $token]);
    });

    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    });
});
