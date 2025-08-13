<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LikeController;


Route::get('/user', function (Request $request) {
    return $request->user()->load(['posts', 'comments', 'likes']);
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post:slug}', [PostController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/search', [TagController::class, 'search']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::get('/user_data', [AuthController::class, 'getUserData']);
    Route::post('/update_profile', [AuthController::class, 'updateProfile']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/recent-posts', [DashboardController::class, 'getRecentPosts']);
        Route::get('/recent-comments', [DashboardController::class, 'getRecentComments']);
        Route::get('/posts', [DashboardController::class, 'getPosts']);
        Route::get('/comments', [DashboardController::class, 'getComments']);
        Route::post('/comments/bulk-update', [DashboardController::class, 'bulkUpdateComments']);
    });

    // Posts management
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    Route::get('posts/search/{query}', [PostController::class, 'search']);
    Route::get('posts/tag/{tag}', [PostController::class, 'postsByTag']);
    Route::get('posts/status/{status}', [PostController::class, 'postsByStatus']);

    // Categories management
    Route::apiResource('categories', CategoryController::class);

    // Tags management
    Route::apiResource('tags', TagController::class);

    // Comments management
    Route::apiResource('comments', CommentController::class)->except(['index']);
    Route::get('posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('posts/{post}/comments', [CommentController::class, 'store']);
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve']);
    Route::post('comments/{comment}/reject', [CommentController::class, 'reject']);
    Route::post('comments/{comment}/spam', [CommentController::class, 'markAsSpam']);

    // Likes
    Route::post('posts/{post}/like', [LikeController::class, 'like']);
    Route::post('posts/{post}/unlike', [LikeController::class, 'unlike']);
    Route::post('comments/{comment}/like', [LikeController::class, 'likeComment']);
    Route::post('comments/{comment}/unlike', [LikeController::class, 'unlikeComment']);
});
