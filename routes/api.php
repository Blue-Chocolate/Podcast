<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// 🎧 Controllers
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
use App\Http\Controllers\Api\SubmissionController\SubmissionController;
use App\Http\Controllers\Api\OrganizationController\OrganizationController;
use App\Http\Controllers\Api\OrganizationController\OrganizationSubmissionController;
use App\Http\Controllers\Api\OrganizationController\RegisterController;

// ======================================================
// 📦 API v1 Routes
// ======================================================
Route::prefix('api/v1')->group(function () {

    // ==================================================
    // 🔓 PUBLIC ROUTES
    // ==================================================
    Route::get('/releases', [ReleaseController::class, 'index']);
    Route::get('/rss/podcast/{slug}', [PodcastRssController::class, 'show']); // internal API feed
    Route::get('/podcasts/{slug}/feed', [FeedController::class, 'showRssFeed']);

    // ==================================================
    // 🔐 AUTHENTICATION ROUTES
    // ==================================================
    Route::post('/login', function (Request $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => $user->role,
        ]);
    });

    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    });

    // ==================================================
    // 🧑 USER ROUTES
    // ==================================================
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('playlists', [PlaylistController::class, 'index']);
        Route::get('playlists/{id}', [PlaylistController::class, 'show']);
        Route::get('releases/{id}/download', [ReleaseController::class, 'download']);
    });

    // ==================================================
    // 🧑‍💼 ADMIN ROUTES
    // ==================================================
    Route::middleware(['auth:sanctum', 'role:admin'])
        ->prefix('admin')
        ->group(function () {

        // 🎧 Podcasts
        Route::get('podcasts', [PodcastController::class, 'index']);
        Route::get('podcasts/{id}', [PodcastController::class, 'show']);
        Route::post('podcasts', [PodcastController::class, 'store']);
        Route::put('podcasts/{id}', [PodcastController::class, 'update']);
        Route::delete('podcasts/{id}', [PodcastController::class, 'destroy']);

        // 🎬 Seasons
        Route::get('seasons', [SeasonController::class, 'index']);
        Route::get('seasons/{id}', [SeasonController::class, 'show']);
        Route::post('seasons', [SeasonController::class, 'store']);
        Route::put('seasons/{id}', [SeasonController::class, 'update']);
        Route::delete('seasons/{id}', [SeasonController::class, 'destroy']);

        // 🎙 Episodes
        Route::get('episodes', [EpisodeController::class, 'index']);
        Route::get('episodes/{id}', [EpisodeController::class, 'show']);
        Route::post('episodes', [EpisodeController::class, 'store']);
        Route::put('episodes/{id}', [EpisodeController::class, 'update']);
        Route::delete('episodes/{id}', [EpisodeController::class, 'destroy']);

        // 🎧 Episode Files
        Route::get('episode-files', [EpisodeFileController::class, 'index']);
        Route::get('episode-files/{id}', [EpisodeFileController::class, 'show']);
        Route::post('episode-files', [EpisodeFileController::class, 'store']);
        Route::put('episode-files/{id}', [EpisodeFileController::class, 'update']);
        Route::delete('episode-files/{id}', [EpisodeFileController::class, 'destroy']);

        // 🗒 Transcripts
        Route::get('transcripts', [TranscriptController::class, 'index']);
        Route::get('transcripts/{id}', [TranscriptController::class, 'show']);
        Route::post('transcripts', [TranscriptController::class, 'store']);
        Route::put('transcripts/{id}', [TranscriptController::class, 'update']);
        Route::delete('transcripts/{id}', [TranscriptController::class, 'destroy']);

        // 👥 People
        Route::get('people', [PersonController::class, 'index']);
        Route::get('people/{id}', [PersonController::class, 'show']);
        Route::post('people', [PersonController::class, 'store']);
        Route::put('people/{id}', [PersonController::class, 'update']);
        Route::delete('people/{id}', [PersonController::class, 'destroy']);

        // 🏷 Categories
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

        // 📝 Blogs
        Route::get('blogs', [BlogController::class, 'index']);
        Route::get('blogs/{id}', [BlogController::class, 'show']);
        Route::post('blogs', [BlogController::class, 'store']);
        Route::put('blogs/{id}', [BlogController::class, 'update']);
        Route::delete('blogs/{id}', [BlogController::class, 'destroy']);

        // 📰 Posts
        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{id}', [PostController::class, 'show']);
        Route::post('posts', [PostController::class, 'store']);
        Route::put('posts/{id}', [PostController::class, 'update']);
        Route::delete('posts/{id}', [PostController::class, 'destroy']);

        // 🎵 Playlists
        Route::get('playlists', [PlaylistController::class, 'index']);
        Route::get('playlists/{id}', [PlaylistController::class, 'show']);
        Route::post('playlists/{id}/attach-episodes', [PlaylistController::class, 'attachEpisodes']);
        Route::post('playlists', [PlaylistController::class, 'store']);
        Route::put('playlists/{id}', [PlaylistController::class, 'update']);
        Route::delete('playlists/{id}', [PlaylistController::class, 'destroy']);
    });
});

// ======================================================
// 🌍 PUBLIC WEBSITE ROUTES
// ======================================================

// 📰 Releases
Route::get('/', [ReleaseController::class, 'index']);
Route::post('/', [ReleaseController::class, 'store'])->middleware('auth:sanctum');
Route::get('/{id}/download/{type?}', [ReleaseController::class, 'download'])
    ->where('type', 'pdf|excel|powerbi');

// 📨 Submissions
Route::post('/submissions', [SubmissionController::class, 'store']);

// 🏢 Organizations
Route::post('/organizations', [OrganizationController::class, 'store']);
Route::post('/organization/submit', [OrganizationSubmissionController::class, 'store']);
Route::post('/organization/register', [RegisterController::class, 'register']);

// ======================================================
// 🌟 APPLE PODCASTS / STATIC RSS FEED ROUTES
// ======================================================

// Top-level RSS feed (public, cacheable) for Apple Podcasts
Route::get('/rss/podcast/{slug}', [PodcastRssController::class, 'show']);
