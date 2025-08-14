<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::id();
            $filterUserId = request()->query('user_id');
            $search = request()->query('search');
            $categoryId = request()->query('category_id');
            $page = (int) request()->query('page', 1);
            $limit = (int) request()->query('limit', 10);

            $query = Post::with(['user', 'comments.user', 'likes', 'category'])->latest();
            if ($filterUserId) {
                $query->where('user_id', $filterUserId);
            }
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('content', 'like', "%$search%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%$search%");
                        });
                });
            }

            $paginator = $query->paginate($limit, ['*'], 'page', $page);
            $posts = $paginator->getCollection()->map(function ($post) use ($userId) {
                $isLiked = $post->likes->contains('user_id', $userId);
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'slug' => $post->slug,
                    'featured_image' => $post->featured_image,
                    'is_published' => $post->is_published,
                    'user' => $post->user,
                    'category' => $post->category,
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
                'data' => $posts,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
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
            // Debug: Log what Laravel receives
            Log::info('Request method: ' . $request->method());
            Log::info('Content-Type: ' . $request->header('Content-Type'));
            Log::info('All request data:', $request->all());
            Log::info('Has file: ' . ($request->hasFile('featured_image') ? 'yes' : 'no'));
            Log::info('Request inputs:', [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'excerpt' => $request->input('excerpt'),
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
                'is_published' => $request->input('is_published'),
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'category_id' => 'nullable|integer',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'nullable|string|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:160',
                'is_published' => 'nullable|boolean',
                'tags' => 'nullable|string' // JSON string of tag IDs
            ]);

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('posts', $imageName, 'public');
                $validated['featured_image'] = '/storage/' . $imagePath;
            }

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
                'user_id' => Auth::id(),
                'published_at' => $validated['status'] === 'published' ? now() : null
            ]);

            $post = Post::create($postData);

            // Handle tags if provided
            if ($request->has('tags') && !empty($validated['tags'])) {
                $tagIds = json_decode($validated['tags'], true);
                if (is_array($tagIds)) {
                    $post->tags()->sync($tagIds);
                }
            }

            // Load the post with relationships for the response
            $post->load(['user', 'category', 'tags']);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Post created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Post creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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
            if ($post->getAttribute('user_id') !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this post'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'category_id' => 'nullable|integer|exists:categories,id',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'nullable|string|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:160',
                'is_published' => 'boolean',
                'tags' => 'nullable|string' // JSON string of tag IDs
            ]);

            // Handle image upload
            if ($request->hasFile('featured_image')) {
                // Delete old image if it exists
                if ($post->featured_image) {
                    $oldImagePath = str_replace('/storage/', '', $post->featured_image);
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                }

                $image = $request->file('featured_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('posts', $imageName, 'public');
                $validated['featured_image'] = '/storage/' . $imagePath;
            }

            // Generate new slug if title changed
            if (isset($validated['title']) && $validated['title'] !== $post->getAttribute('title')) {
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

            // Handle tags if provided
            if ($request->has('tags') && !empty($validated['tags'])) {
                $tagIds = json_decode($validated['tags'], true);
                if (is_array($tagIds)) {
                    $post->tags()->sync($tagIds);
                }
            }

            // Load the post with relationships for the response
            $post->load(['user', 'category', 'tags']);

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
            if ($post->getAttribute('user_id') !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this post'
                ], 403);
            }

            // Delete associated image if it exists
            if ($post->featured_image) {
                $imagePath = str_replace('/storage/', '', $post->featured_image);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
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
     * Upload an image for posts
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('posts', $filename, 'public');
                $imageUrl = '/storage/' . $path;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => $imageUrl,
                        'path' => $path
                    ],
                    'message' => 'Image uploaded successfully'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file found'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
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
