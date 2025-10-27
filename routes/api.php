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
use App\Http\Controllers\Api\EpisodeFileController\EpisodeFileController;
use App\Http\Controllers\Api\TranscriptController\TranscriptController;
use App\Http\Controllers\Api\PersonController\PersonController;
use App\Http\Controllers\Api\BlogController\BlogController;
use App\Http\Controllers\Api\PostController\PostController;
use App\Http\Controllers\Api\PlaylistController\PlaylistController;
use App\Http\Controllers\Api\ReleaseController\ReleaseController;
use App\Http\Controllers\Api\SubmissionController\SubmissionController;
use App\Http\Controllers\Api\NewsController\NewsController;
use App\Http\Controllers\Api\SearchController\SearchController;
use App\Http\Controllers\Auth\RegisteredUserController;



use App\Http\Middleware\RoleMiddleware;

// ==================================================
// 🔓 PUBLIC ROUTES
// ==================================================
use App\Http\Controllers\Api\SubscriberController\SubscriberController;
Route::post('/register', [RegisteredUserController::class, 'store']);


Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe.store');

Route::post('/news', [NewsController::class, 'store']);
Route::get('releases', [ReleaseController::class, 'index']);

Route::get('podcasts/{slug}/feed', [FeedController::class, 'showRssFeed']);
Route::post('submissions', [SubmissionController::class, 'store']);
Route::get('episodes', [EpisodeController::class, 'index']);
Route::get('episodes/{id}', [EpisodeController::class, 'show']);
// 🎙️ Public podcast routes
Route::get('podcasts', [PodcastController::class, 'index']);
Route::get('podcasts/{id}', [PodcastController::class, 'show']);
Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs/{id}', [BlogController::class, 'show']);

Route::get('releases/{id}/download', [ReleaseController::class, 'download']);
Route::get('/releases/{id}', [\App\Http\Controllers\Api\ReleaseController\ReleaseController::class, 'show']);


// 🎵 Audio files route - لازم يبقى في الآخر
Route::get('episodes/audios/{filename}', function ($filename) {
    $path = public_path('storage/episodes/audios/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Content-Type' => 'audio/mpeg',
        'Accept-Ranges' => 'bytes',
    ]);
})->where('filename', '.*');
// ==================================================
// 🔐 AUTH ROUTES
// ==================================================
Route::post('login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('user-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'role' => $user->role,
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->avatar,
    ]);
});

Route::middleware('auth:sanctum')->post('logout', function (Request $request) {
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out']);
});

// ==================================================
// 🧑‍💼 ADMIN ROUTES
// ==================================================
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->group(function () {
        

        
        // Podcasts
        Route::post('podcasts', [PodcastController::class, 'store']);
        Route::put('podcasts/{id}', [PodcastController::class, 'update']);
        Route::delete('podcasts/{id}', [PodcastController::class, 'destroy']);

       

  
        Route::post('episodes', [EpisodeController::class, 'store']);
        Route::put('episodes/{id}', [EpisodeController::class, 'update']);
        Route::delete('episodes/{id}', [EpisodeController::class, 'destroy']);

        // Episode Files
        Route::get('episode-files', [EpisodeFileController::class, 'index']);
        Route::get('episode-files/{id}', [EpisodeFileController::class, 'show']);
        Route::post('episode-files', [EpisodeFileController::class, 'store']);
        Route::put('episode-files/{id}', [EpisodeFileController::class, 'update']);
        Route::delete('episode-files/{id}', [EpisodeFileController::class, 'destroy']);

        // Transcriptswhe
        Route::get('transcripts', [TranscriptController::class, 'index']);
        Route::get('transcripts/{id}', [TranscriptController::class, 'show']);
       

        // People
        Route::get('people', [PersonController::class, 'index']);
        Route::get('people/{id}', [PersonController::class, 'show']);
        Route::post('people', [PersonController::class, 'store']);
        Route::put('people/{id}', [PersonController::class, 'update']);
        Route::delete('people/{id}', [PersonController::class, 'destroy']);

        // Categories
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
        
        // Posts
        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{id}', [PostController::class, 'show']);
      

        // Playlists
        Route::get('playlists', [PlaylistController::class, 'index']);
        Route::get('playlists/{id}', [PlaylistController::class, 'show']);
        

        // Test admin
        Route::get('test', function () {
            return response()->json(['message' => 'You are admin!']);
        });
    });

// ==================================================
// 🧑 USER ROUTES
// ==================================================
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':user'])
    ->prefix('user')
    ->group(function () {
        Route::get('playlists', [PlaylistController::class, 'index']);
        Route::get('playlists/{id}', [PlaylistController::class, 'show']);
        
    });


Route::prefix('subscribe')->group(function () {
    Route::post('/', [SubscriberController::class, 'store']);
});

Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/{id}', [NewsController::class, 'show']);
    Route::post('/', [NewsController::class, 'store']);
});
Route::get('/search', [SearchController::class, 'index']);

use App\Http\Controllers\Api\ContactUsController\ContactUsController;

Route::prefix('contact')->group(function () {
    Route::get('/', [ContactUsController::class, 'index']);
    Route::get('/{id}', [ContactUsController::class, 'show']);
    Route::post('/', [ContactUsController::class, 'store']);
    Route::delete('/{id}', [ContactUsController::class, 'destroy']);
});

use App\Http\Controllers\Api\SeasonController\SeasonController;

Route::prefix('seasons')->group(function () {
    Route::get('/', [SeasonController::class, 'index']);      // GET all
    Route::get('/{id}', [SeasonController::class, 'show']);   // GET one
    Route::post('/', [SeasonController::class, 'store']);     // POST create
    Route::put('/{id}', [SeasonController::class, 'update']); // PUT update
    Route::delete('/{id}', [SeasonController::class, 'destroy']); // DELETE
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});