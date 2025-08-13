<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LikeController;


Route::get('/user', function (Request $request) {
    return $request->user()->load(['posts', 'comments', 'likes']);
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::get('/user_data', [AuthController::class, 'getUserData']);

    // Posts
    Route::apiResource('posts', PostController::class);
    Route::get('posts/search/{query}', [PostController::class, 'search']);
    Route::get('posts/tag/{tag}', [PostController::class, 'postsByTag']);
    Route::get('posts/status/{status}', [PostController::class, 'postsByStatus']);

    // Dashboard stats for current user
    Route::get('dashboard/stats', [PostController::class, 'getDashboardStats']);

    

    // Comments
    Route::apiResource('comments', CommentController::class)->except(['index']);
    Route::get('posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('posts/{post}/comments', [CommentController::class, 'store']);
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve']);

    // Likes
    Route::post('posts/{post}/like', [LikeController::class, 'like']);
    Route::post('posts/{post}/unlike', [LikeController::class, 'unlike']);

   
    Route::post('/update_profile', [AuthController::class, 'updateProfile']);
});
