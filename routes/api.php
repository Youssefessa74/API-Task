<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\PostController;
use App\Http\Controllers\V1\StatsController;
use App\Http\Controllers\V1\TagController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify', [AuthController::class, 'verify']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function () {
    //Log Out Route
    Route::post('logout', [AuthController::class, 'logout']);
    // User route
    Route::get('profile',[UserController::class,'profile']);
    // Tag Routes
    Route::apiResource('tags', TagController::class);
    // Post Route
    Route::get('posts/deleted', [PostController::class, 'deleted']);
    Route::post('posts/{id}/restore', [PostController::class, 'restore']); // Restore a post
    Route::apiResource('posts', PostController::class);
    // Stats Routes
   Route::controller(StatsController::class)->group(function(){
    Route::get('stats','stats');
    //  Route::get('stats-clear-cached','clearStatsCache');
    // Route::get('zero-users-posts','ZeroPostsUsers');

   });

});

