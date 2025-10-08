<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PodcastController\PodcastController;
use App\Http\Controllers\Api\EpisodeController\EpisodeController;
 use App\Http\Controllers\Api\CategoryController\CategoryController;
use App\Http\Controllers\Api\PodcastController\PodcastRssController;
use App\Http\Controllers\Api\FeedeController\FeedController;


Route::prefix('v1')->group(function () {
    Route::get('podcasts', [PodcastController::class, 'index']);
    Route::get('podcasts/{id}', [PodcastController::class, 'show']);
    Route::post('podcasts', [PodcastController::class, 'store']);
    Route::put('podcasts/{id}', [PodcastController::class, 'update']);
    Route::delete('podcasts/{id}', [PodcastController::class, 'destroy']);
});


use App\Http\Controllers\Api\SeasonController\SeasonController;

Route::prefix('v1')->group(function () {
    Route::get('seasons', [SeasonController::class, 'index']);
    Route::get('seasons/{id}', [SeasonController::class, 'show']);
    Route::post('seasons', [SeasonController::class, 'store']);
    Route::put('seasons/{id}', [SeasonController::class, 'update']);
    Route::delete('seasons/{id}', [SeasonController::class, 'destroy']);
});

Route::apiResource('episodes', EpisodeController::class);

use App\Http\Controllers\Api\EpisodeFileController\EpisodeFileController;

Route::prefix('episode-files')->group(function () {
    Route::post('/', [EpisodeFileController::class, 'store']);
    Route::get('/{id}/edit', [EpisodeFileController::class, 'edit']);
    Route::put('/{id}', [EpisodeFileController::class, 'update']);
    Route::delete('/{id}', [EpisodeFileController::class, 'destroy']);
});
use App\Http\Controllers\Api\TranscriptController\TranscriptController;

Route::prefix('transcripts')->group(function () {
    Route::get('/', [TranscriptController::class, 'index']);
    Route::get('/{id}', [TranscriptController::class, 'show']);
    Route::post('/', [TranscriptController::class, 'store']);
    Route::post('/{transcript}', [TranscriptController::class, 'update']);
    Route::delete('/{transcript}', [TranscriptController::class, 'destroy']);
});

use App\Http\Controllers\Api\PersonController\PersonController;

Route::prefix('people')->group(function () {
    Route::get('/', [PersonController::class, 'index']);
    Route::post('/', [PersonController::class, 'store']);
    Route::get('/{id}', [PersonController::class, 'show']);
    Route::put('/{id}', [PersonController::class, 'update']);
    Route::delete('/{id}', [PersonController::class, 'destroy']);
});


Route::prefix('categories')->group(function () {
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::patch('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});


Route::get('/rss/podcasts/{slug}', [PodcastRssController::class, 'show']);
Route::get('/podcasts/{slug}/feed', [FeedController::class, 'showRssFeed']);

use App\Http\Controllers\Api\BlogController\BlogController;
use App\Http\Controllers\Api\PostController\PostController;

Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{id}', [BlogController::class, 'show']);
    Route::post('/', [BlogController::class, 'store']);
    Route::put('/{id}', [BlogController::class, 'update']);
    Route::delete('/{id}', [BlogController::class, 'destroy']);
});

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::post('/', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});
