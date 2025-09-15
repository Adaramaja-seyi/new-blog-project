<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    
    //  Get dashboard statistics
     
    public function getStats()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total_posts' => Post::where('user_id', $userId)->count(),
            
                'total_comments' => Comment::whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count(),
               
                'total_likes' => Like::whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count(),
                'total_views' => Post::where('user_id', $userId)->sum('views_count'),
            ];

            // Recent activity
            $recentPosts = Post::where('user_id', $userId)
                ->with(['category', 'tags'])
                ->latest()
                ->limit(5)
                ->get();

           
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_posts' => $recentPosts,
                  
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

    
    //   Get posts for dashboard with filtering
    
    public function getPosts(Request $request)
    {
        try {
            $userId = Auth::id();

            $query = Post::with(['category', 'tags', 'user', 'likes', 'comments'])
            ->where('user_id', $userId);

            // Apply filters
            if ($request->has('status') && !is_null($request->status)) {
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

           
            $posts = $query->latest()->get();

            // Map posts to include counts and other necessary data
            $mappedPosts = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                     'content' => $post->content,
                    'featured_image' => $post->featured_image,
                    'status' => $post->status,
                    'views_count' => $post->views_count ?? 0,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user' => $post->user ? [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                    ] : null,
                    'category' => $post->category ? [
                        'id' => $post->category->id,
                        'name' => $post->category->name,
                    ] : null,
                    'tags' => $post->tags ? $post->tags->pluck('id')->toArray() : [],
                    'likes_count' => $post->likes()->count(),
                    'comments_count' => $post->comments()->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $mappedPosts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
   
    // Bulk update of multiple  posts at once
     
    public function bulkUpdatePosts(Request $request)
    {
        // dd($request->all());
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:posts,id',
                'status' => 'required|in:published,draft'
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
                'ids.*' => 'integer|exists:posts,id',
            ]);

            $userId = Auth::id();
            $posts = Post::whereIn('id', $validated['ids'])
                ->where('user_id', $userId)
                ->get();

            foreach ($posts as $post) {
                if ($post->featured_image) {
                    $imagePath = str_replace('/storage/', '', $post->featured_image);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
                $post->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Posts deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting posts: ' . $e->getMessage()
            ], 500);
        }
    }

  
    //   Get analytics data for the dashboard
     
    public function getAnalytics(Request $request)
    {
        try {
            $userId = Auth::id();
            $period = $request->query('period', 30);
            $days = (int) $period;

         
            
            $currentStats = $this->getPeriodStats($userId, $days);

            
            $previousStats = $this->getPeriodStats($userId, $days, $days);

       

            $changes = $this->calculateChanges($currentStats, $previousStats);

           
            $engagementData = $this->getEngagementData($userId, $days);

            $topPosts = $this->getTopPosts($userId, 5);

       
            $categoryData = $this->getCategoryPerformance($userId);

            $avgViewsPerPost = $currentStats['total_views'] > 0 ? round($currentStats['total_views'] / $currentStats['total_posts'], 0) : 0;
            $engagementRate = $currentStats['total_views'] > 0 ? round((($currentStats['total_likes'] + $currentStats['total_comments']) / $currentStats['total_views']) * 100, 1) : 0;
            $postsThisMonth = Post::where('user_id', $userId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalViews' => $currentStats['total_views'],
                    'totalLikes' => $currentStats['total_likes'],
                    'totalComments' => $currentStats['total_comments'],
                    'totalPosts' => $currentStats['total_posts'],
                    'viewsChange' => $changes['views_change'],
                    'likesChange' => $changes['likes_change'],
                    'commentsChange' => $changes['comments_change'],
                    'postsChange' => $changes['posts_change'],
                    'engagementData' => $engagementData,
                    'topPosts' => $topPosts,
                    'categoryData' => $categoryData,
                    'avgViewsPerPost' => $avgViewsPerPost,
                    'engagementRate' => $engagementRate,
                    'postsThisMonth' => $postsThisMonth,
                    'avgTimeOnPage' => 145, 
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    //   Get stats for a specific period
     
    private function getPeriodStats($userId, $days, $offset = 0)
    {
        $startDate = now()->subDays($days + $offset);
        $endDate = now()->subDays($offset);

        $posts = Post::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['likes', 'comments'])
            ->get();

        $totalViews = $posts->sum('views_count');
        $totalLikes = $posts->sum(function ($post) {
            return $post->likes->count();
        });
        $totalComments = $posts->sum(function ($post) {
            return $post->comments->count();
        });
        $totalPosts = $posts->count();

        return [
            'total_views' => $totalViews,
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'total_posts' => $totalPosts,
        ];
    }

    // Calculate percentage changes between periods
     
    private function calculateChanges($current, $previous)
    {
        $calculateChange = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };

        return [
            'views_change' => $calculateChange($current['total_views'], $previous['total_views']),
            'likes_change' => $calculateChange($current['total_likes'], $previous['total_likes']),
            'comments_change' => $calculateChange($current['total_comments'], $previous['total_comments']),
            'posts_change' => $calculateChange($current['total_posts'], $previous['total_posts']),
        ];
    }

    // Get engagement data over time
     
    private function getEngagementData($userId, $days)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $posts = Post::where('user_id', $userId)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->with(['likes', 'comments'])
                ->get();

            $views = $posts->sum('views_count');
            $likes = $posts->sum(function ($post) {
                return $post->likes->count();
            });
            $comments = $posts->sum(function ($post) {
                return $post->comments->count();
            });

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'views' => $views,
                'likes' => $likes,
                'comments' => $comments,
            ];
        }

        return $data;
    }

    /**
     * Get top performing posts
     */
    private function getTopPosts($userId, $limit = 5)
    {
        return Post::where('user_id', $userId)
            ->with(['likes', 'comments'])
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'views_count' => $post->views_count ?? 0,
                    'likes_count' => $post->likes->count(),
                    'comments_count' => $post->comments->count(),
                ];
            })
            ->sortByDesc('views_count')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get category performance
     */
    private function getCategoryPerformance($userId)
    {
        return Post::where('user_id', $userId)
            ->with(['category', 'likes', 'comments'])
            ->get()
            ->groupBy('category_id')
            ->map(function ($posts, $categoryId) {
                $category = $posts->first()->category;
                return [
                    'name' => $category ? $category->name : 'Uncategorized',
                    'views' => $posts->sum('views_count'),
                    'posts' => $posts->count(),
                ];
            })
            ->sortByDesc('views')
            ->values()
            ->toArray();
    }
}
