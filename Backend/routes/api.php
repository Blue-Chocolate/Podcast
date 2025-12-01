<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Controllers
use App\Http\Controllers\Api\PodcastController\PodcastController;
use App\Http\Controllers\Api\EpisodeController\EpisodeController;
use App\Http\Controllers\Api\FeedController\FeedController;
use App\Http\Controllers\Api\BlogController\BlogController;
use App\Http\Controllers\Api\PostController\PostController;
use App\Http\Controllers\Api\PlaylistController\PlaylistController;
use App\Http\Controllers\Api\ReleaseController\ReleaseController;
use App\Http\Controllers\Api\SubmissionController\SubmissionController;
use App\Http\Controllers\Api\NewsController\NewsController;
use App\Http\Controllers\Api\SearchController\SearchController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\VideoController\VideoController;
use App\Http\Controllers\Api\SubscriberController\SubscriberController;
use App\Http\Controllers\Api\ContactUsController\ContactUsController;
use App\Http\Controllers\Api\SeasonController\SeasonController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\VideoCategoryController\VideoCategoryController;

// ==================================================
// 🔓 PUBLIC ROUTES
// ==================================================
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe.store');
Route::post('/news', [NewsController::class, 'store']);
Route::get('releases', [ReleaseController::class, 'index']);
Route::get('/releases/{id}', [ReleaseController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    // IMPORTANT: Add the route name here
    Route::get('/releases/{id}/download/{type}', [ReleaseController::class, 'download'])
        ->name('releases.download');
    
    Route::post('/releases', [ReleaseController::class, 'store']);
});
Route::get('podcasts/{slug}/feed', [FeedController::class, 'showRssFeed']);
Route::post('submissions', [SubmissionController::class, 'store']);


Route::get('episodes', [EpisodeController::class, 'index']);
Route::get('episodes/{id}', [EpisodeController::class, 'show']);

// 🎙️ Public podcast routes
Route::get('podcasts', [PodcastController::class, 'index']);
Route::get('podcasts/{id}', [PodcastController::class, 'show']);

// 📝 Blog routes
Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs/{id}', [BlogController::class, 'show']);
// Route::get('blogs/categories', [BlogController::class, 'categories']);
// Route::get('blogs/category/{category_id}', [BlogController::class, 'categoryBlogs']);

use App\Http\Controllers\Api\BlogCategoryController\BlogCategoryController;

Route::prefix('categories/blogs')->group(function () {
    Route::get('/', [BlogCategoryController::class, 'index']); // all categories with blogs
    Route::get('/{category_id}', [BlogCategoryController::class, 'show']); // category + blogs
    Route::get('/{category_id}/{blog_id}', [BlogCategoryController::class, 'showBlog']); // specific blog
});


// 🎬 Doc videos


Route::prefix('categories/videos')->group(function () {
    Route::get('/', [VideoCategoryController::class, 'index']); // all categories with videos
    Route::get('/{category_id}', [VideoCategoryController::class, 'show']); // single category + videos
    Route::get('/{category_id}/{video_id}', [VideoCategoryController::class, 'showVideo']); // single video inside category
});

// 🎬 Doc Videos Routes (individual videos)
Route::get('videos/{id}', [VideoController::class, 'show']); // فيديو محدد
Route::get('videos', [VideoController::class, 'videosList']);

// ==================================================
// 📁 FILE SERVING ROUTES (with CORS support)
// ==================================================

// 🎵 Episode audio files
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

// 📄 PDF/Release files
Route::get('files/{path}', function ($path) {
    $fullPath = storage_path('app/public/releases/files/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($fullPath);
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
    ]);
})->where('path', '.*');

// 🎥 Video files
Route::get('videos/{filename}', function ($filename) {
    $path = public_path('storage/releases/videos/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Content-Type' => 'video/mp4',
        'Accept-Ranges' => 'bytes',
    ]);
})->where('filename', '.*');

// 🔊 Audio files (releases)
Route::get('audios/{filename}', function ($filename) {
    $path = public_path('storage/releases/audios/' . $filename);
    
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
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

// ==================================================
// 📰 NEWS ROUTES
// ==================================================
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/{id}', [NewsController::class, 'show']);
});

// ==================================================
// 📞 CONTACT ROUTES
// ==================================================
Route::prefix('contact')->group(function () {
    Route::get('/', [ContactUsController::class, 'index']);
    Route::get('/{id}', [ContactUsController::class, 'show']);
    Route::post('/', [ContactUsController::class, 'store']);
    Route::delete('/{id}', [ContactUsController::class, 'destroy']);
});

// ==================================================
// 🎬 SEASONS ROUTES
// ==================================================
Route::prefix('seasons')->group(function () {
    Route::get('/', [SeasonController::class, 'index']);
    Route::get('/{id}', [SeasonController::class, 'show']);
    Route::post('/', [SeasonController::class, 'store']);
    Route::put('/{id}', [SeasonController::class, 'update']);
    Route::delete('/{id}', [SeasonController::class, 'destroy']);
});

// ==================================================
// 🔍 SEARCH ROUTE
// ==================================================
Route::get('/search', [SearchController::class, 'search']);