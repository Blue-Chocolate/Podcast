<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\Api\PodcastController\PodcastController;

Route::prefix('v1')->group(function () {
    Route::get('podcasts', [PodcastController::class, 'index']);          // list all
    Route::get('podcasts/{id}', [PodcastController::class, 'show']);      // show single
    Route::post('podcasts', [PodcastController::class, 'store']);         // create
    Route::put('podcasts/{id}', [PodcastController::class, 'update']);   // update
    Route::delete('podcasts/{id}', [PodcastController::class, 'destroy']); // delete
});
