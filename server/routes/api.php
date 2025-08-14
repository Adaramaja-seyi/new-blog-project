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

// Public routes (no auth required)
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post:slug}', [PostController::class, 'show']);
Route::get('/posts/{post:slug}/comments', [CommentController::class, 'getPostComments']);
Route::get('/posts/{post:slug}/related', [PostController::class, 'getRelatedPosts']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::get('/user_data', [AuthController::class, 'getUserData']);
    Route::post('/update_profile', [AuthController::class, 'updateProfile']);

    // Posts (authenticated)
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    Route::post('posts/{post}/update', [PostController::class, 'update']); // Special route for file uploads
    Route::post('upload-image', [PostController::class, 'uploadImage']); // Image upload endpoint
    Route::get('posts/search/{query}', [PostController::class, 'search']);
    Route::get('posts/tag/{tag}', [PostController::class, 'postsByTag']);
    Route::get('posts/status/{status}', [PostController::class, 'postsByStatus']);

    // Categories (authenticated operations)
    Route::apiResource('categories', CategoryController::class)->except(['index']);

    // Tags (authenticated operations)
    Route::apiResource('tags', TagController::class)->except(['index']);
    Route::get('tags/search', [TagController::class, 'search']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [DashboardController::class, 'getStats']);
        Route::get('posts', [DashboardController::class, 'getPosts']);
        Route::get('comments', [DashboardController::class, 'getComments']);

        // Bulk operations
        Route::patch('posts/bulk-update', [DashboardController::class, 'bulkUpdatePosts']);
        Route::delete('posts/bulk-delete', [DashboardController::class, 'bulkDeletePosts']);
        Route::patch('comments/bulk-update', [DashboardController::class, 'bulkUpdateComments']);
        Route::delete('comments/bulk-delete', [DashboardController::class, 'bulkDeleteComments']);
    });

    // Comments
    Route::apiResource('comments', CommentController::class)->except(['index']);
    Route::get('posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('posts/{post}/comments', [CommentController::class, 'store']);
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve']);
    Route::post('comments/{comment}/reject', [CommentController::class, 'reject']);
    Route::post('comments/{comment}/spam', [CommentController::class, 'markAsSpam']);
    Route::patch('comments/{comment}/status', [CommentController::class, 'updateStatus']);

    // Likes system
    Route::post('posts/{post}/like', [LikeController::class, 'likePost']);
    Route::delete('posts/{post}/like', [LikeController::class, 'unlikePost']);
    Route::get('posts/{post}/like-status', [LikeController::class, 'checkLikeStatus']);
    Route::post('comments/{comment}/like', [LikeController::class, 'likeComment']);
    Route::post('comments/{comment}/unlike', [LikeController::class, 'unlikeComment']);
});
