<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total_posts' => Post::where('user_id', $userId)->count(),
                'published_posts' => Post::where('user_id', $userId)->where('status', 'published')->count(),
                'draft_posts' => Post::where('user_id', $userId)->where('status', 'draft')->count(),
                'total_comments' => Comment::whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count(),
                'pending_comments' => Comment::whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->where('status', 'pending')->count(),
                'approved_comments' => Comment::whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->where('status', 'approved')->count(),
                'total_likes' => Like::whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count(),
            ];

            // Recent activity
            $recentPosts = Post::where('user_id', $userId)
                ->with(['category', 'tags'])
                ->latest()
                ->limit(5)
                ->get();

            $recentComments = Comment::whereHas('post', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->with(['post', 'user'])->latest()->limit(5)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_posts' => $recentPosts,
                    'recent_comments' => $recentComments
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get posts for dashboard with filtering
     */
    public function getPosts(Request $request)
    {
        try {
            $userId = Auth::id();
            $query = Post::where('user_id', $userId)->with(['category', 'tags', 'user']);

            // Apply filters
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('category_id') && $request->category_id !== '') {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('content', 'LIKE', "%{$search}%")
                        ->orWhere('excerpt', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('date_from') && $request->date_from !== '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to !== '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $posts = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for dashboard with filtering
     */
    public function getComments(Request $request)
    {
        try {
            $userId = Auth::id();
            $query = Comment::whereHas('post', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->with(['post', 'user']);

            // Apply filters
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('post_id') && $request->post_id !== '') {
                $query->where('post_id', $request->post_id);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('content', 'LIKE', "%{$search}%")
                        ->orWhere('author_name', 'LIKE', "%{$search}%")
                        ->orWhere('author_email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('date_from') && $request->date_from !== '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to !== '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $comments = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update posts
     */
    public function bulkUpdatePosts(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:posts,id',
                'status' => 'required|in:published,draft,pending'
            ]);

            $userId = Auth::id();

            Post::where('user_id', $userId)
                ->whereIn('id', $validated['ids'])
                ->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Posts updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete posts
     */
    public function bulkDeletePosts(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:posts,id'
            ]);

            $userId = Auth::id();

            Post::where('user_id', $userId)
                ->whereIn('id', $validated['ids'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posts deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update comments
     */
    public function bulkUpdateComments(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:comments,id',
                'status' => 'required|in:approved,pending,spam'
            ]);

            $userId = Auth::id();

            Comment::whereHas('post', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->whereIn('id', $validated['ids'])
                ->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Comments updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete comments
     */
    public function bulkDeleteComments(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:comments,id'
            ]);

            $userId = Auth::id();

            Comment::whereHas('post', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->whereIn('id', $validated['ids'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comments deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
