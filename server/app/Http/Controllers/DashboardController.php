<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        // Get basic stats for the current user
        $stats = [
            'total_posts' => $user->posts()->count(),
            'published_posts' => $user->posts()->where('status', 'published')->count(),
            'draft_posts' => $user->posts()->where('status', 'draft')->count(),
            'total_comments' => Comment::whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->getKey());
            })->count(),
            'pending_comments' => Comment::whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->getKey());
            })->where('status', 'pending')->count(),
            'total_views' => $user->posts()->sum('views_count'),
            'total_likes' => Like::whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->getKey());
            })->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent posts for dashboard
     */
    public function getRecentPosts(Request $request)
    {
        $user = $request->user();

        $recentPosts = $user->posts()
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likes'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($recentPosts);
    }

    /**
     * Get recent comments for dashboard
     */
    public function getRecentComments(Request $request)
    {
        $user = $request->user();

        $recentComments = Comment::with(['user', 'post'])
            ->whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->getKey());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($recentComments);
    }

    /**
     * Get posts with filters for dashboard
     */
    public function getPosts(Request $request)
    {
        $user = $request->user();
        $query = $user->posts()->with(['category', 'tags'])->withCount(['comments', 'likes']);

        // Apply filters
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('category_id') && $request->input('category_id') !== '') {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $posts = $query->paginate($perPage);

        return response()->json($posts);
    }

    /**
     * Get comments with filters for dashboard
     */
    public function getComments(Request $request)
    {
        $user = $request->user();
        $query = Comment::with(['user', 'post'])
            ->whereHas('post', function ($q) use ($user) {
                $q->where('user_id', $user->getKey());
            });

        // Apply filters
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('post_id') && $request->input('post_id') !== '') {
            $query->where('post_id', $request->input('post_id'));
        }

        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where('content', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $comments = $query->paginate($perPage);

        return response()->json($comments);
    }

    /**
     * Bulk update comment status
     */
    public function bulkUpdateComments(Request $request)
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'required|integer|exists:comments,id',
            'status' => 'required|in:pending,approved,spam'
        ]);

        $user = $request->user();

        // Update only comments that belong to user's posts
        $updated = Comment::whereIn('id', $request->comment_ids)
            ->whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->getKey());
            })
            ->update(['status' => $request->input('status')]);

        return response()->json([
            'message' => "Updated {$updated} comments successfully",
            'updated_count' => $updated
        ]);
    }
}
