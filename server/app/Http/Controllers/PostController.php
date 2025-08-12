<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::id();
            $filterUserId = request()->query('user_id');
            $query = Post::with(['user', 'comments.user', 'likes'])->latest();
            if ($filterUserId) {
                $query->where('user_id', $filterUserId);
            }
            $posts = $query->get()->map(function ($post) use ($userId) {
                $isLiked = $post->likes->contains('user_id', $userId);
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'slug' => $post->slug,
                    'featured_image' => $post->featured_image,
                    'is_published' => $post->is_published,
                    'user' => $post->user,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'likes_count' => $post->likes->count(),
                    'comments_count' => $post->comments->count(),
                    'is_liked' => $isLiked,
                    'comments' => $post->comments,
                    'likes' => $post->likes,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'is_published' => 'boolean'
            ]);

            // Generate slug from title
            $slug = Str::slug($validated['title']);

            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $postData = array_merge($validated, [
                'slug' => $slug,
                'user_id' => Auth::id()
            ]);

            $post = Post::create($postData);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Post created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Post $post)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $post->load(['user', 'comments.user', 'likes'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching post: ' . $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, Post $post)
    {
        try {
            // Check if user owns the post
            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this post'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'is_published' => 'boolean'
            ]);

            // Generate new slug if title changed
            if (isset($validated['title']) && $validated['title'] !== $post->title) {
                $slug = Str::slug($validated['title']);
                $originalSlug = $slug;
                $counter = 1;
                while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $validated['slug'] = $slug;
            }

            $post->update($validated);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Post updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Post $post)
    {
        try {
            // Check if user owns the post
            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this post'
                ], 403);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $user = $request->user();

            // Use withCount for fast stats
            $totalPosts = $user->posts()->count();
            $published = $user->posts()->where('is_published', true)->count();
            $drafts = $user->posts()->where('is_published', false)->count();
            $totalComments = $user->comments()->count();

            // Get likes count for user's posts
            $totalLikes = Like::whereIn('post_id', $user->posts()->pluck('id'))->count();

            // Get recent posts for dashboard
            $posts = $user->posts()
                ->withCount(['comments', 'likes'])
                ->with(['user'])
                ->latest()
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'totalPosts' => $totalPosts,
                    'published' => $published,
                    'drafts' => $drafts,
                    'comments' => $totalComments,
                    'likes' => $totalLikes,
                ],
                'posts' => $posts,
                'tags' => [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent posts for dashboard
     */
    public function getRecentPosts()
    {
        try {
            $posts = Post::with(['user'])
                ->withCount(['comments', 'likes'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($post) {
                    $userData = [
                        'name' => $post->user->name ?? 'Unknown',
                        'avatar' => $post->user->avatar ?? null
                    ];

                    // Only add ID if user exists
                    if ($post->user && property_exists($post->user, 'id')) {
                        $userData['id'] = $post->user->id;
                    }

                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'content' => $post->content,
                        'status' => $post->is_published ? 'published' : 'draft',
                        'created_at' => $post->created_at,
                        'user' => $userData,
                        'comment_count' => $post->comments_count ?? 0,
                        'like_count' => $post->likes_count ?? 0
                    ];
                });

            return response()->json([
                'success' => true,
                'posts' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching recent posts: ' . $e->getMessage()
            ], 500);
        }
    }
}
