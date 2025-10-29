<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Api\PodcastRssController\PodcastRssController;
use App\Http\Controllers\Api\EpisodeController\EpisodeController;


Route::get('/videos/{file}', function ($file) {
    $path = base_path('public/storage/videos/' . $file);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
});

Route::get('/audios/{file}', function ($file) {
    $path = base_path('public/storage/audios/' . $file);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
});

Route::get('/covers/{file}', function ($file) {
    $path = base_path('public/storage/covers/' . $file);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
});
Route::get('/files/{file}', function ($file) {
    $path = base_path('public/storage/files/' . $file);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
});
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/releases', function () {
    return view('releases');
});

Route::get('/login', function () {
    return view('auth.login'); // or your login view
})->name('login');


Route::get('/redis-test', function () {
    Redis::set('test-key', 'Hello Predis!');
    return Redis::get('test-key');
});


Route::get('/forms', function () {
    return view('forms');
})->middleware(['auth', 'verified'])->name('forms');

Route::get('/podcast/{slug}/rss', [PodcastRssController::class, 'generateRss'])->name('podcast.rss');

Route::get('episodes', [EpisodeController::class, 'index']);
Route::get('episodes/{id}', [EpisodeController::class, 'show']);
Route::get('episodes/{slug}', [EpisodeController::class, 'show']);


