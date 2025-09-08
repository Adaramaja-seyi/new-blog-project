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
Route::get('/posts', [PostController::class, 'getAllPosts']);
Route::get('/posts/{post:slug}/comments', [CommentController::class, 'getPostComments']);
Route::get('/categories', [CategoryController::class, 'getCategories']);
Route::get('/tags', [TagController::class, 'getTags']);
Route::get('/posts/{post:slug}', [PostController::class, 'getPost']);
Route::post('/user_data', [AuthController::class, 'getUserData']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update_profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

    // Posts
    Route::post('/posts', [PostController::class, 'createPost']);
    Route::put('/posts/{post}', [PostController::class, 'updatePost']);
    Route::delete('/posts/{post:slug}', [PostController::class, 'deletePost']);
    Route::post('/upload-image', [PostController::class, 'uploadImage']);
    Route::get('/posts/status/{status}', [PostController::class, 'getPostsByStatus']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/posts', [DashboardController::class, 'getPosts']);
        Route::get('/comments', [DashboardController::class, 'getComments']);
        Route::get('/analytics', [DashboardController::class, 'getAnalytics']);
        Route::patch('/posts/bulk-update', [DashboardController::class, 'bulkUpdatePosts']);
        Route::delete('/posts/bulk-delete', [DashboardController::class, 'bulkDeletePosts']);
    });

    // // Comments
     Route::post('/posts/{post:slug}/comments', [CommentController::class, 'createComment']);
     Route::delete('/comments/{comment}', [CommentController::class, 'deleteComment']);
   

    // Likes
    Route::post('/posts/{post:id}/like', [LikeController::class, 'likePost']);
    Route::delete('/posts/{post:id}/like', [LikeController::class, 'unlikePost']);
    Route::get('/posts/{post:id}/like-status', [LikeController::class, 'checkLikeStatus']);
    Route::post('/comments/{comment}/like', [LikeController::class, 'likeComment']);
    Route::post('/comments/{comment}/unlike', [LikeController::class, 'unlikeComment']);
});
