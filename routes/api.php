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


Route::prefix('v1')->group(function () {

    // 🔓 Public routes
    Route::get('releases', [ReleaseController::class, 'index']);
    Route::get('/rss/podcasts/{slug}', [PodcastRssController::class, 'show']);
    Route::get('/podcasts/{slug}/feed', [FeedController::class, 'showRssFeed']);

    // 🔐 Authentication
    Route::post('/login', function (Request $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('user-token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'role' => $user->role
        ]);
    });

    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    });


    // 🧑 User routes (for users with role 'user')
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::apiResource('playlists', PlaylistController::class)->only(['index', 'show']);
        Route::get('releases/{id}/download', [ReleaseController::class, 'download']);
    });


    // 🧑‍💼 Admin routes (for users with role 'admin')
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::apiResource('podcasts', PodcastController::class);
        Route::apiResource('seasons', SeasonController::class);
        Route::apiResource('episodes', EpisodeController::class);
        Route::apiResource('episode-files', EpisodeFileController::class);
        Route::apiResource('transcripts', TranscriptController::class);
        Route::apiResource('people', PersonController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('blogs', BlogController::class);
        Route::apiResource('posts', PostController::class);
        Route::apiResource('playlists', PlaylistController::class);
        Route::post('playlists/{id}/attach-episodes', [PlaylistController::class, 'attachEpisodes']);
    });

});

Route::get('/', [ReleaseController::class, 'index']); // عرض كل الريليزز
    Route::post('/', [ReleaseController::class, 'store'])->middleware('auth:sanctum'); // رفع الملفات (Admins)
    Route::get('/{id}/download/{type?}', [ReleaseController::class, 'download'])
        ->where('type', 'pdf|excel|powerbi');

        